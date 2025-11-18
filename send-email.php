<?php
session_start();
require_once __DIR__ . '/classes/db.php';


$to = $application['gmail']; 
$subject = "Payment Confirmation - Reference #$reference_no";
$message = "
<html>
<body>
    <h2>Payment Confirmation</h2>
    <p>Dear {$application['name']},</p>
    <p>Thank you for your payment. Your transaction details are below:</p>
    <ul>
        <li><strong>Reference No:</strong> $reference_no</li>
        <li><strong>Transaction ID:</strong> $transaction_id</li>
        <li><strong>Regional Centre:</strong> {$application['regional_centre']}</li>
        <li><strong>Course:</strong> {$application['course_name']}</li>
        <li><strong>Total Amount Paid:</strong> Rs. {$application['amount']}</li>
    </ul>
    <p>Regards,<br>Gem Institute</p>
</body>
</html>
";

$headers = "From: Gem Institute <info@geminstitute.lk>\r\n";
$headers .= "Reply-To: info@geminstitute.lk\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";


mail($to, $subject, $message, $headers);
