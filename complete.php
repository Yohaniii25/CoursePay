<?php
session_start();
require_once __DIR__ . '/classes/db.php';

$logDir = __DIR__ . '/logs/';
if (!is_dir($logDir)) mkdir($logDir, 0755, true);
function logMsg($msg) {
    global $logDir;
    error_log(date('[Y-m-d H:i:s] ') . $msg . PHP_EOL, 3, $logDir . 'complete.log');
}

logMsg("complete.php accessed – GET: " . json_encode($_GET) . " | SESSION: " . json_encode($_SESSION));

$success_indicator_from_url = $_GET['resultIndicator'] ?? '';
$reference_no      = $_SESSION['reference_no'] ?? '';
$order_id          = $_SESSION['order_id'] ?? '';
$amount_to_pay     = $_SESSION['amount_to_pay'] ?? 0;
$installment_type  = $_SESSION['installment_type'] ?? 'first';
$application_id    = $_SESSION['application_id'] ?? 0;
$session_success   = $_SESSION['success_indicator'] ?? '';

if (empty($reference_no) || empty($order_id) || $amount_to_pay <= 0 || empty($success_indicator_from_url)) {
    die("Invalid session.");
}
if ($success_indicator_from_url !== $session_success) {
    die("Payment failed.");
}

$db = new Database();
$conn = $db->getConnection();

// PREVENT DUPLICATE
$transaction_id = 'TXN-' . $order_id;
$stmt = $conn->prepare("SELECT id FROM payments WHERE transaction_id = ?");
$stmt->bind_param("s", $transaction_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $stmt->close();
} else {
    // GET APPLICATION + CHARGE TYPE (FREE or PAYABLE)
    $stmt = $conn->prepare("
        SELECT a.registration_fee, a.course_fee, a.charge_type,
               COALESCE(SUM(p.paid_amount), 0) AS total_paid_so_far
        FROM applications a
        LEFT JOIN payments p ON a.id = p.application_id
        WHERE a.id = ?
    ");
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $app = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // DETERMINE TOTAL REQUIRED BASED ON CHARGE TYPE
    if ($app['charge_type'] === 'free') {
        $total_required = 2000.00;
    } else {
        $total_required = $app['registration_fee'] + $app['course_fee'];
    }

    $current_paid = (float)$app['total_paid_so_far'];
    $new_paid = $current_paid + $amount_to_pay;
    $new_due = max(0, $total_required - $new_paid);
    $status = $new_due <= 0 ? 'completed' : 'pending';

    // RECORD PAYMENT
    $stmt = $conn->prepare("
        INSERT INTO payments 
        (application_id, amount, paid_amount, due_amount, method, installment_type, transaction_id, status)
        VALUES (?, ?, ?, ?, 'Online Payment', ?, ?, ?)
    ");
    $stmt->bind_param(
        "idddsss",
        $application_id,
        $amount_to_pay,
        $amount_to_pay,
        $new_due,
        $installment_type,
        $transaction_id,
        $status
    );
    $stmt->execute();
    $stmt->close();

    logMsg("Payment recorded | Amount: Rs. $amount_to_pay | Due: Rs. $new_due | Charge Type: {$app['charge_type']} | Ref: $reference_no");
}

// FETCH FINAL DATA FOR RECEIPT
$stmt = $conn->prepare("
    SELECT s.name, s.gmail, a.course_name, a.regional_centre, a.charge_type,
           COALESCE(SUM(p.paid_amount), 0) AS total_paid,
           CASE 
               WHEN a.charge_type = 'free' THEN 2000.00
               ELSE (a.registration_fee + a.course_fee)
           END AS total_required
    FROM students s
    JOIN applications a ON s.id = a.student_id
    LEFT JOIN payments p ON a.id = p.application_id
    WHERE s.reference_no = ?
");
$stmt->bind_param("s", $reference_no);
$stmt->execute();
$final = $stmt->get_result()->fetch_assoc();
$stmt->close();

$total_paid = (float)$final['total_paid'];
$total_required = (float)$final['total_required'];
$remaining = $total_required - $total_paid;

// SEND EMAIL
$to = $final['gmail'];
$subject = "Payment Successful – GJRTI";
$message = "Dear {$final['name']},\n\n";
$message .= "Your payment of Rs. " . number_format($amount_to_pay, 2) . " was successful!\n\n";
$message .= "Reference: $reference_no\n";
$message .= "Course: {$final['course_name']}\n";
$message .= "Total Amount: Rs. " . number_format($total_required, 2) . "\n";
$message .= "Total Paid: Rs. " . number_format($total_paid, 2) . "\n";
$message .= "Remaining: Rs. " . number_format($remaining, 2) . "\n\n";

if ($final['charge_type'] === 'free') {
    $message .= "This is a FREE course. You only paid the registration fee of Rs. 2000.\n";
}

if ($remaining <= 0) {
    $message .= "FULL PAYMENT COMPLETED! Welcome to the course!\n";
}

$message .= "\nBest regards,\nGJRTI Team";

$headers = "From: no-reply@sltdigital.site\r\nContent-Type: text/plain; charset=UTF-8";
mail($to, $subject, $message, $headers);

$_SESSION = [];
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Success - GJRTI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen flex items-center justify-center p-6">
<div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full p-12 text-center">
    <div class="w-28 h-28 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
        </svg>
    </div>
    <h1 class="text-4xl font-bold text-green-600 mb-4">Payment Successful!</h1>
    <p class="text-2xl text-gray-700 mb-8">Rs. <?= number_format($amount_to_pay, 2) ?> Received</p>

    <div class="bg-gray-50 rounded-2xl p-8 text-left space-y-4 text-lg">
        <div><strong>Student:</strong> <?= htmlspecialchars($final['name']) ?></div>
        <div><strong>Reference:</strong> <?= htmlspecialchars($reference_no) ?></div>
        <div><strong>Course:</strong> <?= htmlspecialchars($final['course_name']) ?></div>
        <?php if ($final['charge_type'] === 'free'): ?>
            <div class="text-xl font-bold text-indigo-600 pt-4 border-t">
                FREE Course – Registration Fee Only
            </div>
        <?php endif; ?>
        <div class="text-2xl font-bold pt-6 border-t">
            Total Amount: <span class="text-blue-600">Rs. <?= number_format($total_required, 2) ?></span>
        </div>
        <div class="text-2xl font-bold">
            Total Paid: <span class="text-green-600">Rs. <?= number_format($total_paid, 2) ?></span>
        </div>
        <div class="text-2xl font-bold">
            Remaining: <span class="<?= $remaining > 0 ? 'text-orange-600' : 'text-green-600' ?>">
                Rs. <?= number_format($remaining, 2) ?>
            </span>
        </div>
    </div>

    <?php if ($remaining <= 0): ?>
        <div class="mt-10 p-8 bg-emerald-100 rounded-2xl text-emerald-800 font-bold text-3xl">
            FULL PAYMENT COMPLETED!
        </div>
    <?php endif; ?>

    <div class="mt-10 flex gap-6 justify-center">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-5 px-12 rounded-xl text-xl shadow-lg">
            Print Receipt
        </button>
        <a href="https://sltdigital.site/gem/" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-5 px-12 rounded-xl text-xl shadow-lg">
            Back to Website
        </a>
    </div>

    <p class="mt-10 text-sm text-gray-500">
        Payment recorded at <?= date('d M Y, h:i A') ?> • Thank you for choosing GJRTI
    </p>
</div>
</body>
</html>