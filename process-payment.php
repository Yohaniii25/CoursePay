<?php
session_start();
require_once __DIR__ . '/classes/db.php';

$logDir = __DIR__ . '/logs/';
if (!is_dir($logDir)) mkdir($logDir, 0755, true);
$GLOBALS['logDir'] = $logDir;

function ipg_start($order_id, $amount, $reference_no, $installment_type, $application_id)
{
    $return_url = "https://sltdigital.site/gem/CoursePay/proceed-to-pay.php?ref=" . urlencode($reference_no);

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://test-bankofceylon.mtf.gateway.mastercard.com/api/rest/version/100/merchant/TEST700182200500/session',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => json_encode([
            "apiOperation" => "INITIATE_CHECKOUT",
            "checkoutMode" => "WEBSITE",
            "interaction" => [
                "operation" => "PURCHASE",
                "merchant" => ["name" => "Gem and Jewellery", "url" => "https://sltdigital.site/gem/"],
                "returnUrl" => "https://sltdigital.site/gem/CoursePay/complete.php"
            ],
            "order" => [
                "currency" => "LKR",
                "amount" => number_format($amount, 2, '.', ''),
                "id" => $order_id,
                "description" => "GJRTI Course Payment - Ref: $reference_no"
            ]
        ]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Basic bWVyY2hhbnQuVEVTVDcwMDE4MjIwMDUwMDpiMWUzZTE1NjU3MWNlNGFhZTRmNzMzZTVmMWY1MGYyMw=='
        ],
    ]);

    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        error_log(date('[Y-m-d H:i:s] ') . "cURL Error: " . curl_error($curl), 3, $GLOBALS['logDir'] . 'error.log');
        die("Payment gateway temporarily unavailable.");
    }
    curl_close($curl);

    $data = json_decode($response, true);
    if (!isset($data['session']['id']) || !isset($data['successIndicator'])) {
        error_log(date('[Y-m-d H:i:s] ') . "MPGS Error: " . json_encode($data), 3, $GLOBALS['logDir'] . 'error.log');
        die("Failed to connect to payment gateway.");
    }

    // SAVE EVERYTHING TO SESSION — THIS IS CRITICAL
    $_SESSION['session_id']         = $data['session']['id'];
    $_SESSION['success_indicator']  = $data['successIndicator'];   // ← THIS WAS MISSING!
    $_SESSION['order_id']           = $order_id;
    $_SESSION['reference_no']       = $reference_no;
    $_SESSION['amount_to_pay']      = $amount;
    $_SESSION['installment_type']   = $installment_type;
    $_SESSION['application_id']     = $application_id;

    session_write_close();

    echo '<!DOCTYPE html><html><head>
        <script src="https://test-bankofceylon.mtf.gateway.mastercard.com/static/checkout/checkout.min.js"
                data-error="errorCallback" data-cancel="cancelCallback"></script>
        <script>
            function errorCallback(e){ alert("Payment failed"); location.href="' . $return_url . '"; }
            function cancelCallback(){ alert("Payment cancelled"); location.href="' . $return_url . '"; }
            Checkout.configure({ session: { id: "' . $data['session']['id'] . '" } });
        </script>
        </head><body>
        <div id="embed-target"></div>
        <script>window.onload = function(){ Checkout.showPaymentPage(); }</script>
        </body></html>';
    exit;
}

// ============================
// MAIN LOGIC
// ============================
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Invalid request method.");

$reference_no   = trim($_POST['reference_no'] ?? '');
$payment_method = $_POST['payment_method'] ?? '';
$payment_option = $_POST['payment_option'] ?? '';

if (empty($reference_no) || empty($payment_method) || empty($payment_option)) {
    header("Location: proceed-to-pay.php?ref=" . urlencode($reference_no) . "&error=Missing+fields");
    exit;
}

// Fetch application + total already paid
$stmt = $conn->prepare("
    SELECT s.id AS student_id, s.name, s.gmail, s.checked,
           a.id AS application_id, a.course_name, a.regional_centre,
           a.registration_fee, a.course_fee, a.charge_type,
           COALESCE(SUM(p.paid_amount), 0) AS already_paid
    FROM students s
    JOIN applications a ON s.id = a.student_id
    LEFT JOIN payments p ON a.id = p.application_id AND p.status = 'completed'
    WHERE s.reference_no = ?
    GROUP BY s.id, a.id
");
$stmt->bind_param("s", $reference_no);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$app || !$app['checked']) {
    header("Location: proceed-to-pay.php?ref=" . urlencode($reference_no) . "&error=Application+not+approved");
    exit;
}

$total_required = $app['charge_type'] === 'free'
    ? $app['registration_fee']
    : $app['registration_fee'] + $app['course_fee'];

$already_paid = (float)$app['already_paid'];
$remaining    = $total_required - $already_paid;

$is_first_payment = $already_paid == 0;
$installment_type = $payment_option === '50_percent'
    ? ($is_first_payment ? 'first' : 'second')
    : 'full';

if ($payment_option === '50_percent') {
    $amount_to_pay = $is_first_payment
        ? $app['registration_fee'] + ($app['course_fee'] * 0.5)
        : $app['course_fee'] * 0.5;
} else {
    $amount_to_pay = $remaining;
}

if ($amount_to_pay <= 0 || $amount_to_pay > $remaining + 0.01) {
    header("Location: proceed-to-pay.php?ref=" . urlencode($reference_no) . "&error=Invalid+amount");
    exit;
}

try {
    $conn->autocommit(FALSE);

    // ONLINE PAYMENT — DO NOT INSERT HERE
    if ($payment_method === 'Online Payment') {
        $unique_order_id = $reference_no . '-' . strtoupper($installment_type) . '-' . time();

        $conn->commit();
        $conn->autocommit(TRUE);

        ipg_start($unique_order_id, $amount_to_pay, $reference_no, $installment_type, $app['application_id']);
        exit;
    }

    // BANK SLIP — INSERT NOW
    if ($payment_method === 'Bank Slip') {
        if (empty($_FILES['payment_slip']['name'])) throw new Exception("Please upload bank slip");

        $uploadDir = __DIR__ . "/Uploads/slips/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filename = time() . "_" . basename($_FILES['payment_slip']['name']);
        $slip_path = "Uploads/slips/" . $filename;
        $full_path = __DIR__ . "/" . $slip_path;

        $ext = strtolower(pathinfo($full_path, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'])) throw new Exception("Only JPG, PNG, PDF allowed");
        if (!move_uploaded_file($_FILES['payment_slip']['tmp_name'], $full_path)) throw new Exception("Upload failed");

        $transaction_id = 'SLIP-' . $reference_no . '-' . time();
        $new_paid_total = $already_paid + $amount_to_pay;
        $new_due = $total_required - $new_paid_total;
        $status = $new_due <= 0 ? 'completed' : 'pending';

        $stmt = $conn->prepare("
            INSERT INTO payments 
            (application_id, amount, paid_amount, due_amount, method, installment_type, transaction_id, slip_file, status)
            VALUES (?, ?, ?, ?, 'Upload Payslip', ?, ?, ?, ?)
        ");
        $stmt->bind_param("idddsssss", $app['application_id'], $amount_to_pay, $amount_to_pay, $new_due, $installment_type, $transaction_id, $slip_path, $status);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        $conn->autocommit(TRUE);

        $to = $app['gmail'];
        $subject = "Payment Slip Received – Thank You!";
        $message = "Dear {$app['name']},\n\nYour payment slip has been received.\nReference: $reference_no\nAmount: Rs. " . number_format($amount_to_pay, 2) . "\n\n" . ($new_due <= 0 ? "Full payment completed!" : "Remaining: Rs. " . number_format($new_due, 2)) . "\n\nBest regards,\nGJRTI Team";
        $headers = "From: no-reply@sltdigital.site\r\nContent-Type: text/plain; charset=UTF-8";
        mail($to, $subject, $message, $headers);

        header("Location: payment-success.php?ref=$reference_no");
        exit;
    }

} catch (Exception $e) {
    if ($conn->autocommit(FALSE)) $conn->rollback();
    $conn->autocommit(TRUE);
    header("Location: proceed-to-pay.php?ref=" . urlencode($reference_no) . "&error=" . urlencode($e->getMessage()));
    exit;
}