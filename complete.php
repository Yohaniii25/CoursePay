<?php
session_start();
require_once __DIR__ . '/classes/db.php';

// === LOGGING ===
$logDir = __DIR__ . '/logs/';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
    chmod($logDir, 0755);
}
function logMsg($msg) {
    global $logDir;
    error_log(date('[Y-m-d H:i:s] ') . $msg . PHP_EOL, 3, $logDir . 'error.log');
}
logMsg("complete.php started – SID: " . session_id());
logMsg("GET: " . json_encode($_GET));
logMsg("POST: " . json_encode($_POST));
logMsg("SESSION: " . json_encode($_SESSION));

// === DATABASE ===
$db = new Database();
$conn = $db->getConnection();

// === SESSION VARIABLES ===
$reference_no = $_SESSION['reference_no'] ?? '';
$amount = (float)($_SESSION['amount'] ?? 0); // online payment
$session_id = $_SESSION['session_id'] ?? '';
$success_indicator = $_SESSION['success_indicator'] ?? '';
$payment_option = $_SESSION['payment_option'] ?? 'full';
$online_processed = $_SESSION['online_processed'] ?? false;

// === VALIDATE SESSION ===
if (empty($reference_no) || empty($session_id) || empty($success_indicator)) {
    logMsg("Missing session data");
    header("Location: proceed-to-pay.php?ref=" . urlencode($reference_no) . "&error=Invalid+session");
    exit;
}

// === MPGS VERIFICATION (only once) ===
$transaction_success = false;
if (!$online_processed) {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://test-bankofceylon.mtf.gateway.mastercard.com/api/rest/version/100/merchant/TEST700182200500/order/$reference_no",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => ['Authorization: Basic bWVyY2hhbnQuVEVTVDcwMDE4MjIwMDUwMDpiMWUzZTE1NjU3MWNlNGFhZTRmNzMzZTVmMWY1MGYyMw=='],
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    $data = json_decode($response, true);
    $transaction_success = isset($data['result']) && $data['result'] === 'SUCCESS';
    if (isset($data['successIndicator'])) {
        $transaction_success = $transaction_success && ($data['successIndicator'] === $success_indicator);
    }
    if (!$transaction_success) {
        logMsg("MPGS verification failed");
        header("Location: proceed-to-pay.php?ref=" . urlencode($reference_no) . "&error=Payment+failed");
        exit;
    }
}

// === FETCH APPLICATION + PAYMENT ===
$stmt = $conn->prepare("
    SELECT s.id AS student_id, s.name, s.gmail,
           a.id AS application_id, a.course_name, a.regional_centre,
           a.registration_fee, a.course_fee,
           p.id AS payment_id, p.amount, p.paid_amount, p.due_amount, p.status, p.method, p.slip_file
    FROM students s
    JOIN applications a ON s.id = a.student_id
    LEFT JOIN payments p ON a.id = p.application_id
    WHERE s.reference_no = ?
");
$stmt->bind_param("s", $reference_no);
$stmt->execute();
$res = $stmt->get_result();
$application = $res->fetch_assoc();
$stmt->close();
if (!$application) {
    die("Invalid Reference ID.");
}

// === TOTAL AMOUNT ===
$total_amount = $application['registration_fee'] + $application['course_fee'];
$payment = $application['payment_id'] ? [
    'id' => $application['payment_id'],
    'amount' => $application['amount'],
    'paid' => $application['paid_amount'],
    'due' => $application['due_amount'],
    'method' => $application['method'] ?? '',
    'slip_file' => $application['slip_file']
] : null;

// === UI VARIABLES ===
$upload_success = false;
$slip_error = '';
$slip_amount = 0;
$current_paid = 0;
$current_due = 0;

try {
    $conn->begin_transaction();

    // === CREATE PAYMENT RECORD IF MISSING ===
    if (!$payment) {
        $stmt = $conn->prepare("
            INSERT INTO payments (application_id, amount, paid_amount, due_amount, status, method)
            VALUES (?, ?, 0, ?, 'pending', 'Online Payment')
        ");
        $stmt->bind_param("idd", $application['application_id'], $total_amount, $total_amount);
        $stmt->execute();
        $payment_id = $conn->insert_id;
        $payment = [
            'id' => $payment_id,
            'amount' => $total_amount,
            'paid' => 0,
            'due' => $total_amount,
            'method' => '',
            'slip_file' => null
        ];
        $stmt->close();
    }

    // === APPLY ONLINE PAYMENT (ONLY ONCE) ===
    if (!$online_processed && $amount > 0 && $transaction_success) {
        $new_paid = $payment['paid'] + $amount;
        $new_due = $total_amount - $new_paid;
        $new_status = $new_due <= 0 ? 'completed' : 'pending';
        $stmt = $conn->prepare("
            UPDATE payments
            SET paid_amount = ?, due_amount = ?, status = ?, method = 'Online Payment'
            WHERE id = ?
        ");
        $stmt->bind_param("ddsi", $new_paid, $new_due, $new_status, $payment['id']);
        $stmt->execute();
        $stmt->close();
        $_SESSION['online_processed'] = true;
        logMsg("Online payment recorded: Rs. $amount (option: $payment_option)");
    }

    // === RE-READ CURRENT STATE AFTER ONLINE UPDATE ===
    $stmt = $conn->prepare("SELECT paid_amount, due_amount, amount FROM payments WHERE id = ?");
    $stmt->bind_param("i", $payment['id']);
    $stmt->execute();
    $cur = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $current_paid = (float)$cur['paid_amount'];
    $current_due = (float)$cur['due_amount'];
    $total_amount = (float)$cur['amount'];

    // === BANK SLIP UPLOAD (PARTIAL OR FULL) ===
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_slip'])) {
        $slip_amount_input = (float)($_POST['slip_amount'] ?? 0);

        if ($slip_amount_input <= 0) {
            $slip_error = "Please enter your paid amount.";
        } elseif ($slip_amount_input > $current_due + 0.01) {
            $slip_error = "Amount cannot exceed remaining balance: Rs. " . number_format($current_due, 2);
        } elseif (empty($_FILES['payment_slip']['name'])) {
            $slip_error = "Please upload your bank slip.";
        } else {
            $uploadDir = __DIR__ . "/Uploads/slips/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $filename = time() . "_" . basename($_FILES['payment_slip']['name']);
            $slip_path = "Uploads/slips/" . $filename;
            $ext = strtolower(pathinfo($slip_path, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','pdf'])) {
                $slip_error = "Only JPG, PNG, PDF allowed.";
            } elseif (!move_uploaded_file($_FILES['payment_slip']['tmp_name'], __DIR__ . '/' . $slip_path)) {
                $slip_error = "Upload failed. Try again.";
            } else {
                $final_paid = $current_paid + $slip_amount_input;
                $final_due = $total_amount - $final_paid;
                $final_status = $final_due <= 0 ? 'completed' : 'pending';

                // Build slip file JSON array (preserve existing single-string values)
                $existing_slip = isset($payment['slip_file']) ? $payment['slip_file'] : null;
                $slips = [];
                if ($existing_slip) {
                    $decoded = json_decode($existing_slip, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $slips = $decoded;
                    } else {
                        $slips = [$existing_slip];
                    }
                }
                $slips[] = $slip_path;
                $slip_json = json_encode($slips);

                $stmt = $conn->prepare("
                    UPDATE payments
                    SET paid_amount = ?, due_amount = ?, status = ?, method = 'Bank Slip', slip_file = ?
                    WHERE id = ?
                ");
                $stmt->bind_param("ddssi", $final_paid, $final_due, $final_status, $slip_json, $payment['id']);
                $stmt->execute();
                $stmt->close();

                $current_paid = $final_paid;
                $current_due = $final_due;
                $upload_success = true;

                // === USER EMAIL ===
                $to = $application['gmail'];
                $subject = "Bank Slip Payment Recorded";
                $msg = "Dear {$application['name']},\n\n";
                $msg .= "Your bank slip payment of Rs. " . number_format($slip_amount_input, 2) . " has been recorded.\n\n";
                $msg .= "Reference: $reference_no\n";
                $msg .= "Total Paid: Rs. " . number_format($current_paid, 2) . "\n";
                $msg .= "Remaining: Rs. " . number_format($current_due, 2) . "\n\n";
                if ($current_due <= 0) {
                    $msg .= "Congratulations! Your full payment is complete.\n";
                } else {
                    $msg .= "Please pay the remaining balance before the due date.\n";
                }
                $msg .= "\nBest regards,\nGJRTI";
                $headers = "From: no-reply@sltdigital.site\r\nContent-Type: text/plain; charset=UTF-8\r\n";
                mail($to, $subject, $msg, $headers);

                // === ADMIN EMAIL ===
                $admin_msg = "Bank Slip Received\n\n";
                $admin_msg .= "Student: {$application['name']}\n";
                $admin_msg .= "Reference: $reference_no\n";
                $admin_msg .= "Course: {$application['course_name']}\n";
                $admin_msg .= "Slip Amount: Rs. " . number_format($slip_amount_input, 2) . "\n";
                $admin_msg .= "Total Paid: Rs. " . number_format($current_paid, 2) . "\n";
                $admin_msg .= "Remaining: Rs. " . number_format($current_due, 2) . "\n";
                $admin_msg .= "Slip File: https://sltdigital.site/gem/CoursePay/$slip_path\n";
                mail('sutharshankanna04@gmail.com', "Slip Uploaded – $reference_no", $admin_msg, $headers);

                $conn->commit();
                $conn->close();
                header("Location: index.php");
                exit;
                    
            }
        }
    }

    // === ONLINE PAYMENT EMAIL (once) ===
    if (!$online_processed && $amount > 0 && $transaction_success) {
        $to = $application['gmail'];
        $subject = "Online Payment Received";
        $msg = "Dear {$application['name']},\n\n";
        $msg .= "Your online payment of Rs. " . number_format($amount, 2) . " has been received.\n\n";
        $msg .= "Reference: $reference_no\n";
        $msg .= "Total Paid: Rs. " . number_format($current_paid, 2) . "\n";
        $msg .= "Remaining: Rs. " . number_format($current_due, 2) . "\n\n";
        if ($payment_option === '50_percent') {
            $msg .= "You have paid 50%. The remaining 50% must be paid within half the course duration.\n\n";
        }
        $msg .= "Print receipt from the payment page.\n\nBest regards,\nGJRTI";
        $headers = "From: no-reply@sltdigital.site\r\nContent-Type: text/plain; charset=UTF-8\r\n";
        mail($to, $subject, $msg, $headers);
        mail('yohanii725@gmail.com', "Online Payment – $reference_no", "Rs. " . number_format($amount, 2), $headers);
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    logMsg("ERROR: " . $e->getMessage());
    header("Location: proceed-to-pay.php?ref=" . urlencode($reference_no) . "&error=" . urlencode($e->getMessage()));
    exit;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - GJRTI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            body { background: white; }
            .no-print { display: none !important; }
            .printable { box-shadow: none !important; border: 1px solid #e5e7eb; }
            .printable::before {
                content: "Official Receipt";
                display: block;
                text-align: center;
                font-weight: 700;
                font-size: 1.5rem;
                margin-bottom: 1rem;
                color: #1f2937;
            }
        }
        .receipt-header { border-bottom: 2px dashed #9333ea; padding-bottom: 1rem; margin-bottom: 1.5rem; }
        .receipt-footer { border-top: 2px dashed #9333ea; padding-top: 1rem; margin-top: 1.5rem; }
        .logo { height: 80px; }
        .divider { height: 1px; background: linear-gradient(to right, transparent, #d1d5db, transparent); margin: 1rem 0; }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 min-h-screen py-10 px-4">
<div class="max-w-3xl mx-auto">

    <!-- ERROR MESSAGE -->
    <?php if (!empty($slip_error)): ?>
        <div class="p-6 bg-red-50 border-l-4 border-red-500 rounded-lg mb-6">
            <p class="text-red-700 font-medium"><?= htmlspecialchars($slip_error) ?></p>
        </div>
    <?php endif; ?>

    <!-- RECEIPT CARD -->
    <div class="printable bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
        <div class="receipt-header bg-gradient-to-r from-purple-600 to-pink-600 text-white p-6 text-center">
            <div class="flex items-center justify-center gap-4 mb-3">
                <img src="https://sltdigital.site/gem/wp-content/uploads/2025/06/GJRT-1.png" alt="GJRTI Logo" class="logo">
            </div>
            <h1 class="text-2xl font-bold">Gem & Jewellery Research and Training Institute</h1>
            <p class="text-sm opacity-90">Official Payment Receipt</p>
        </div>
        <div class="p-8">
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-500">Student Name</p>
                    <p class="font-semibold text-lg"><?php echo htmlspecialchars($application['name']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Reference No.</p>
                    <p class="font-semibold text-lg text-purple-700"><?php echo htmlspecialchars($reference_no); ?></p>
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-500">Course</p>
                    <p class="font-medium"><?php echo htmlspecialchars($application['course_name']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Regional Centre</p>
                    <p class="font-medium"><?php echo htmlspecialchars($application['regional_centre']); ?></p>
                </div>
            </div>
            <div class="divider"></div>
            <!-- PAYMENT SUMMARY -->
            <div class="space-y-3 text-lg">
                <div class="flex justify-between">
                    <span class="text-gray-600">Payment Received</span>
                    <span class="font-bold text-green-600">Rs. <?php echo number_format($amount, 2); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Paid So Far</span>
                    <span class="font-bold text-blue-600">Rs. <?php echo number_format($current_paid, 2); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Remaining Balance</span>
                    <span class="font-bold <?php echo $current_due > 0 ? 'text-orange-600' : 'text-green-600'; ?>">
                        Rs. <?php echo number_format($current_due, 2); ?>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Payment Method</span>
                    <span class="font-medium"><?php echo htmlspecialchars($payment['method'] ?: 'Online Payment'); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Date & Time</span>
                    <span class="font-medium"><?php echo date('d M Y, h:i A'); ?></span>
                </div>
            </div>
            <?php if ($payment_option === '50_percent'): ?>
                <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-sm">
                    <strong>50% Installment Plan:</strong> The remaining 50% must be paid within half the course duration.
                </div>
            <?php endif; ?>
            <?php if ($current_due <= 0): ?>
                <div class="mt-6 p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-center font-semibold">
                    Full Payment Completed – You're all set!
                </div>
            <?php endif; ?>
        </div>
        <div class="receipt-footer bg-gray-50 px-8 py-4 text-center text-xs text-gray-500">
            <p>Thank you for choosing GJRTI. For queries: <span class="font-medium">info@sltdigital.site</span></p>
            <p class="mt-1">This is a system-generated receipt. No signature required.</p>
        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center no-print">
        <button onclick="printReceipt()" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-8 rounded-xl shadow-lg transform transition hover:scale-105">
            Print Receipt
        </button>
        <a href="index.php" class="bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-bold py-3 px-8 rounded-xl shadow-lg text-center transform transition hover:scale-105">
            Back to Home
        </a>
    </div>


</div>

<script>
    function printReceipt() { window.print(); }
    
</script>
</body>
</html>