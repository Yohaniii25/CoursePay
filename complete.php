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
$db   = new Database();
$conn = $db->getConnection();

// === SESSION VARIABLES ===
$reference_no      = $_SESSION['reference_no'] ?? '';
$amount            = (float)($_SESSION['amount'] ?? 0);           // 50% or full
$session_id        = $_SESSION['session_id'] ?? '';
$success_indicator = $_SESSION['success_indicator'] ?? '';
$payment_option    = $_SESSION['payment_option'] ?? 'full';       // '50_percent' or 'full'
$online_processed  = $_SESSION['online_processed'] ?? false;

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
        CURLOPT_URL            => "https://test-bankofceylon.mtf.gateway.mastercard.com/api/rest/version/100/merchant/TEST700182200500/order/$reference_no",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'GET',
        CURLOPT_HTTPHEADER     => ['Authorization: Basic bWVyY2hhbnQuVEVTVDcwMDE4MjIwMDUwMDpiMWUzZTE1NjU3MWNlNGFhZTRmNzMzZTVmMWY1MGYyMw=='],
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

$payment = $application['payment_id'] ? [
    'id'        => $application['payment_id'],
    'amount'    => $application['amount'],
    'paid'      => $application['paid_amount'],
    'due'       => $application['due_amount'],
    'method'    => $application['method'] ?? '',
    'slip_file' => $application['slip_file']
] : null;

// === UI VARIABLES ===
$upload_success = false;
$slip_amount    = 0;
$current_due    = 0;

try {
    $conn->begin_transaction();

    // === CREATE PAYMENT RECORD IF MISSING ===
    if (!$payment) {
        $total = $application['registration_fee'] + $application['course_fee'];
        $stmt = $conn->prepare("
            INSERT INTO payments (application_id, amount, paid_amount, due_amount, status, method)
            VALUES (?, ?, 0, ?, 'pending', 'Online Payment')
        ");
        $stmt->bind_param("idd", $application['application_id'], $total, $total);
        $stmt->execute();
        $payment_id = $conn->insert_id;
        $payment = [
            'id'        => $payment_id,
            'amount'    => $total,
            'paid'      => 0,
            'due'       => $total,
            'method'    => '',
            'slip_file' => null
        ];
        $stmt->close();
    }

    // === APPLY ONLINE PAYMENT (ONLY ONCE) ===
    if (!$online_processed && $amount > 0 && $transaction_success) {
        $new_paid = $amount - $payment['paid'] ;
        $new_due  = $amount - $new_paid;
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

    // === RE-READ CURRENT STATE ===
    $stmt = $conn->prepare("SELECT paid_amount, due_amount, amount FROM payments WHERE id = ?");
    $stmt->bind_param("i", $payment['id']);
    $stmt->execute();
    $cur = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $current_paid = (float)$cur['paid_amount'];
    $current_due  = (float)$cur['due_amount'];
    $total_amount = (float)$cur['amount'];

    // === BANK SLIP UPLOAD ===
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_slip'])) {
        $slip_amount = (float)($_POST['slip_amount'] ?? 0);

        if ($slip_amount <= 0 || $slip_amount > $current_due + 0.01) {
            throw new Exception("Invalid slip amount. Submitted: $slip_amount, Due: $current_due");
        }
        if (empty($_FILES['payment_slip']['name'])) {
            throw new Exception("Bank slip required.");
        }

        $uploadDir = __DIR__ . "/Uploads/slips/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filename = time() . "_" . basename($_FILES['payment_slip']['name']);
        $slip_path = "Uploads/slips/" . $filename;
        $ext = strtolower(pathinfo($slip_path, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','pdf'])) {
            throw new Exception("Invalid file type.");
        }
        if (!move_uploaded_file($_FILES['payment_slip']['tmp_name'], __DIR__ . '/' . $slip_path)) {
            throw new Exception("Upload failed.");
        }

        $final_paid = $current_paid + $slip_amount;
        $final_due  = $total_amount - $final_paid;
        $final_status = $final_due <= 0 ? 'completed' : 'pending';

        $stmt = $conn->prepare("
            UPDATE payments
            SET paid_amount = ?, due_amount = ?, status = ?, method = 'Bank Slip', slip_file = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ddssi", $final_paid, $final_due, $final_status, $slip_path, $payment['id']);
        $stmt->execute();
        $stmt->close();

        $current_paid = $final_paid;
        $current_due  = $final_due;
        $upload_success = true;

        // === SEND EMAILS ===
        $to = $application['gmail'];
        $subject = "Payment Completed – Bank Slip Received";
        $msg = "Dear {$application['name']},\n\n";
        $msg .= "Your bank slip payment of Rs. " . number_format($slip_amount, 2) . " has been recorded.\n";
        $msg .= "Total Paid: Rs. " . number_format($current_paid, 2) . "\n";
        $msg .= "Remaining Balance: Rs. " . number_format($current_due, 2) . "\n\n";
        if ($current_due <= 0) {
            $msg .= "Congratulations! Your course fee is fully paid.\n";
        } else {
            $msg .= "Please pay the remaining balance before the due date.\n";
        }
        $msg .= "\nThank you for choosing GJRTI.\n\nBest regards,\nGem and Jewellery Research and Training Institute";
        $headers = "From: no-reply@sltdigital.site\r\nContent-Type: text/plain; charset=UTF-8\r\n";
        mail($to, $subject, $msg, $headers);

        // Admin email
        $admin_email = 'yohanii725@gmail.com';
        $admin_msg = "Bank Slip Received\n\n";
        $admin_msg .= "Student: {$application['name']}\n";
        $admin_msg .= "Reference: $reference_no\n";
        $admin_msg .= "Course: {$application['course_name']}\n";
        $admin_msg .= "Slip Amount: Rs. " . number_format($slip_amount, 2) . "\n";
        $admin_msg .= "Total Paid: Rs. " . number_format($current_paid, 2) . "\n";
        $admin_msg .= "Remaining: Rs. " . number_format($current_due, 2) . "\n";
        $admin_msg .= "Slip File: https://sltdigital.site/gem/CoursePay/$slip_path\n";
        mail($admin_email, "Slip Uploaded – $reference_no", $admin_msg, $headers);

        // === REDIRECT TO index.php ===
        $conn->commit();
        $conn->close();

        $_SESSION['payment_success'] = [
            'name'    => $application['name'],
            'ref'     => $reference_no,
            'course'  => $application['course_name'],
            'paid'    => $current_paid,
            'due'     => $current_due,
            'status'  => $final_status
        ];

        header("Location: index.php");
        exit;
    }

    // === ONLINE PAYMENT EMAIL (once) ===
    if (!$online_processed && $amount > 0 && $transaction_success) {
        $to = $application['gmail'];
        $subject = "Online Payment Received";
        $msg = "Dear {$application['name']},\n\n";
        $msg .= "Your online payment of Rs. " . number_format($amount, 2) . " has been received.\n";
        $msg .= "Remaining Balance: Rs. " . number_format($current_due, 2) . "\n";
        if ($payment_option === '50_percent') {
            $msg .= "\nYou have paid 50%. The remaining 50% must be paid within half the course duration.\n";
        }
        $msg .= "\nPrint receipt from the payment page.\n\nBest regards,\nGJRTI";
        $headers = "From: no-reply@sltdigital.site\r\nContent-Type: text/plain; charset=UTF-8\r\n";
        mail($to, $subject, $msg, $headers);
        mail('yohanii725@gmail.com', "Online Payment – $reference_no", "Rs. " . number_format($amount,2), $headers);
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
    <title>Payment Complete</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print { body * { visibility: hidden; } .printable, .printable * { visibility: visible; } .printable { position: absolute; top: 0; left: 0; width: 100%; } .no-print { display: none; } }
    </style>
    <script>
        function printReceipt() { window.print(); }
        function toggleUpload() { document.getElementById('upload-section').classList.toggle('hidden'); }
    </script>
</head>
<body class="bg-gray-50 min-h-screen py-10 px-4">
<div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-8">

    <h1 class="text-3xl font-bold mb-6 text-blue-700 no-print">Payment Complete</h1>

    <!-- Online Receipt -->
    <div class="p-6 bg-green-50 border-l-4 border-green-500 rounded-lg mb-6 printable">
        <p class="font-semibold text-green-700">Online Payment Successful</p>
        <p class="text-gray-600 mt-2">Student: <?php echo htmlspecialchars($application['name']); ?></p>
        <p class="text-gray-600 mt-2">Reference: <?php echo htmlspecialchars($reference_no); ?></p>
        <p class="text-gray-600 mt-2">Course: <?php echo htmlspecialchars($application['course_name']); ?></p>
        <p class="text-gray-600 mt-2">Paid: Rs. <?php echo number_format($amount, 2); ?></p>
        <p class="text-gray-600 mt-2">Remaining: Rs. <?php echo number_format($current_due, 2); ?></p>
        <p class="text-gray-600 mt-2">Method: Online Payment</p>
        <p class="text-gray-600 mt-2">Date: <?php echo date('Y-m-d H:i:s'); ?></p>
        <?php if ($payment_option === '50_percent'): ?>
            <p class="text-gray-600 mt-2 text-sm italic">50% paid. Remaining 50% due within half course duration.</p>
        <?php endif; ?>
        <button onclick="printReceipt()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg no-print">
            Print Receipt
        </button>
    </div>

    <!-- Upload Form (only if due > 0) -->
    <?php if ($current_due > 0 && !$upload_success): ?>
        <div class="mb-6 no-print">
            <p class="font-semibold mb-4">Upload slip for remaining: Rs. <?php echo number_format($current_due, 2); ?></p>
            <button onclick="toggleUpload()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                Proceed for Submission
            </button>
            <div id="upload-section" class="hidden mt-4">
                <form action="complete.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="upload_slip" value="1">
                    <div>
                        <label>Amount (Rs.) <span class="text-red-500">*</span></label>
                        <input type="number" name="slip_amount" step="0.01" min="0.01" max="<?php echo $current_due; ?>" required class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label>Slip <span class="text-red-500">*</span></label>
                        <input type="file" name="payment_slip" accept=".jpg,.jpeg,.png,.pdf" required class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg">Upload</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <a href="index.php" class="block text-center bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 rounded-lg mt-6 no-print">
        Return to Home
    </a>
</div>
</body>
</html>