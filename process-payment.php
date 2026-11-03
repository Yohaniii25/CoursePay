<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once __DIR__ . '/classes/db.php';

$logDir = __DIR__ . '/logs/';
if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
$GLOBALS['logDir'] = $logDir;

function ipg_start($order_id, $amount, $reference_no, $installment_type, $application_id)
{
    $return_url = "https://www.gjrti.gov.lk/CoursePay/proceed-to-pay.php?ref=" . urlencode($reference_no);

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://test-bankofceylon.mtf.gateway.mastercard.com/api/rest/version/100/merchant/TEST700182200500/session',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => json_encode([
            "apiOperation" => "INITIATE_CHECKOUT",
            "checkoutMode" => "WEBSITE",
            "interaction" => [
                "operation" => "PURCHASE",
                "merchant" => ["name" => "Gem and Jewellery", "url" => "https://www.gjrti.gov.lk/"],
                "returnUrl" => "https://www.gjrti.gov.lk/CoursePay/complete.php"
            ],
            "order" => [
                "currency" => "LKR",
                "amount" => number_format($amount, 2, '.', ''),
                "id" => $order_id,
                "description" => "GJRTI Payment - Ref: $reference_no"
            ]
        ]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Basic bWVyY2hhbnQuVEVTVDcwMDE4MjIwMDUwMDpiMWUzZTE1NjU3MWNlNGFhZTRmNzMzZTVmMWY1MGYyMw=='
        ],
    ]);

    $response = curl_exec($curl);
    if (curl_errno($curl)) die("Gateway error.");
    curl_close($curl);

    $data = json_decode($response, true);
    if (!isset($data['session']['id']) || !isset($data['successIndicator'])) die("Gateway failed.");

    $_SESSION['session_id'] = $data['session']['id'];
    $_SESSION['success_indicator'] = $data['successIndicator'];
    $_SESSION['order_id'] = $order_id;
    $_SESSION['reference_no'] = $reference_no;
    $_SESSION['amount_to_pay'] = $amount;
    $_SESSION['installment_type'] = $installment_type;
    $_SESSION['application_id'] = $application_id;

    session_write_close();

    echo '<!DOCTYPE html><html><head>
        <script src="https://test-bankofceylon.mtf.gateway.mastercard.com/static/checkout/checkout.min.js"
                data-error="errorCallback" data-cancel="cancelCallback"></script>
        <script>
            function errorCallback(){ alert("Failed"); location.href="' . $return_url . '"; }
            function cancelCallback(){ alert("Cancelled"); location.href="' . $return_url . '"; }
            Checkout.configure({ session: { id: "' . $data['session']['id'] . '" } });
        </script>
        </head><body>
        <div id="embed-target"></div>
        <script>window.onload = function(){ Checkout.showPaymentPage(); }</script>
        </body></html>';
    exit;
}

// MAIN LOGIC
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Invalid request.");

$reference_no   = trim($_POST['reference_no'] ?? '');
$payment_method = $_POST['payment_method'] ?? '';
$payment_option = $_POST['payment_option'] ?? '';

if (empty($reference_no) || empty($payment_method) || empty($payment_option)) {
    header("Location: proceed-to-pay.php?ref=" . urlencode($reference_no) . "&error=Missing+fields");
    exit;
}

// GET APPLICATION + REMAINING DUE + STUDENT INFO
$stmt = $conn->prepare("
    SELECT 
        s.name, s.gmail, a.course_name, a.regional_centre,
        a.id AS application_id, a.course_type,
        a.registration_fee, a.course_fee, a.refundable_deposit, a.charge_type,
        COALESCE(
            (SELECT due_amount FROM payments WHERE application_id = a.id ORDER BY id DESC LIMIT 1),
            (a.registration_fee + a.course_fee + a.refundable_deposit)
        ) AS remaining_due,
        COALESCE(SUM(p.paid_amount), 0) AS total_paid
    FROM applications a
    JOIN students s ON a.student_id = s.id
    LEFT JOIN payments p ON a.id = p.application_id AND p.status = 'completed'
    WHERE s.reference_no = ?
    GROUP BY a.id, s.id
");
$stmt->bind_param("s", $reference_no);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$app) die("Invalid reference.");

$remaining_due = (float)$app['remaining_due'];
$total_paid = (float)$app['total_paid'];
$is_first_payment = $total_paid == 0;

// FREE COURSE
if ($app['charge_type'] === 'free') {
    $free_fee = (($app['course_type'] ?? '') === 'Diploma Level Courses') ? 5000.00 : 2000.00;
    $amount_to_pay = $remaining_due > 0 ? $remaining_due : $free_fee;
    $installment_type = 'full';
} 
// NORMAL COURSE
else {
    if ($payment_option === '50_percent') {
        if (!$is_first_payment) {
            header("Location: proceed-to-pay.php?ref=" . urlencode($reference_no) . "&error=50%25+not+allowed");
            exit;
        }
        $amount_to_pay = $app['registration_fee'] + ($app['course_fee'] * 0.5) + (float)($app['refundable_deposit'] ?? 0);
        $installment_type = 'first';
    } else {
        $amount_to_pay = $remaining_due;
        $installment_type = $is_first_payment ? 'full' : 'second';
    }
}

if ($amount_to_pay <= 0) {
    header("Location: payment-success.php?ref=$reference_no");
    exit;
}

try {
    $conn->autocommit(FALSE);

    if ($payment_method === 'Online Payment') {
        $unique_order_id = $reference_no . '-' . strtoupper($installment_type) . '-' . time();
        $conn->commit();
        $conn->autocommit(TRUE);
        ipg_start($unique_order_id, $amount_to_pay, $reference_no, $installment_type, $app['application_id']);
        exit;
    }

    if ($payment_method === 'Bank Slip') {
        if (empty($_FILES['payment_slip']['name'])) throw new Exception("Please upload slip");

        $uploadDir = __DIR__ . "/Uploads/slips/";
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0777, true);

        $filename = time() . "_" . basename($_FILES['payment_slip']['name']);
        $slip_path = "Uploads/slips/" . $filename;
        $full_path = __DIR__ . "/" . $slip_path;

        if (!move_uploaded_file($_FILES['payment_slip']['tmp_name'], $full_path)) {
            throw new Exception("Upload failed");
        }

        $transaction_id = 'SLIP-' . $reference_no . '-' . time();
        $new_due = $remaining_due - $amount_to_pay;
        $status = $new_due <= 0 ? 'completed' : 'pending';

        $stmt = $conn->prepare("
            INSERT INTO payments 
            (application_id, amount, paid_amount, due_amount, method, installment_type, transaction_id, slip_file, status)
            VALUES (?, ?, ?, ?, 'Upload Payslip', ?, ?, ?, ?)
        ");
        
        $stmt->bind_param(
            "idddssss", 
            $app['application_id'],     
            $amount_to_pay,             
            $amount_to_pay,             
            $new_due,                   
            $installment_type,      
            $transaction_id,            
            $slip_path,                 
            $status                     
        );
        
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        $conn->autocommit(TRUE);

        // SEND NOTIFICATION EMAILS (via authenticated SMTP)
        require_once __DIR__ . '/classes/Mailer.php';

        // 1. Confirmation Email to Student
        if (!empty($app['gmail'])) {
            $student_subject = "Bank Slip Payment Submitted - Ref: $reference_no";
            $student_message = "Dear {$app['name']},

Thank you for uploading your course payment slip.

Payment Details:
- Reference Number: $reference_no
- Course: {$app['course_name']}
- Regional Centre: {$app['regional_centre']}
- Amount Paid: Rs. " . number_format($amount_to_pay, 2) . "
- Remaining Balance: Rs. " . number_format($new_due, 2) . "
- Payment Method: Bank Slip Upload
- Installment: " . ucfirst($installment_type) . "
- Status: Pending Verification

Your bank slip has been received and is currently under review by our admissions and finance team. You will receive an official confirmation once your payment is verified.

Best regards,
Gem and Jewellery Research and Training Institute (GJRTI)
";
            try {
                Mailer::send($app['gmail'], $student_subject, $student_message, false, 'no-reply@gjrti.gov.lk');
            } catch (Throwable $mEx) {
                error_log("process-payment.php student mail error: " . $mEx->getMessage());
            }
        }

        // 2. Alert Email to Admin
        $admin_email = 'yohanii725@gmail.com';
        $admin_subject = "New Bank Slip Uploaded - Ref: $reference_no";
        $admin_message = "Dear Admin,

A student has uploaded a bank payment slip for verification:

Student Name: {$app['name']}
Reference Number: $reference_no
Course: {$app['course_name']}
Regional Centre: {$app['regional_centre']}
Amount Paid: Rs. " . number_format($amount_to_pay, 2) . "
Installment Type: " . ucfirst($installment_type) . "
Remaining Balance: Rs. " . number_format($new_due, 2) . "
Slip File: $slip_path

Please log in to the admin dashboard to verify and approve the payment:
https://www.gjrti.gov.lk/CoursePay/admin/pages/dashboard.php

Best regards,
GJRTI CoursePay System
";
        try {
            Mailer::send($admin_email, $admin_subject, $admin_message, false, 'no-reply@gjrti.gov.lk');
        } catch (Throwable $mEx) {
            error_log("process-payment.php admin mail error: " . $mEx->getMessage());
        }

        header("Location: payment-success.php?ref=$reference_no");
        exit;
    }

} catch (Exception $e) {
    if ($conn->autocommit(FALSE)) $conn->rollback();
    $conn->autocommit(TRUE);
    header("Location: proceed-to-pay.php?ref=" . urlencode($reference_no) . "&error=" . urlencode($e->getMessage()));
    exit;
}
