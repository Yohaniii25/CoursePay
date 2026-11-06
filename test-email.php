<?php
require_once __DIR__ . '/classes/Mailer.php';

$recipient = $_GET['to'] ?? 'yohanii725@gmail.com';
$subject   = "CoursePay Template Test - " . date('Y-m-d H:i');

$html = Mailer::buildTemplate(
    "Course Fee Installment Reminder",
    "Payment Due",
    "orange",
    "Dear Student,",
    "This is a preview of the new attractive email template designed for the GJRTI CoursePay system.",
    [
        'Reference Number'  => "<strong style='color:#25116F; font-size:16px;'>TEST-98E6C6DC</strong>",
        'Course Name'       => "Certificate in Basic Gemmology",
        'Regional Centre'   => "Head Office - Kaduwela",
        'Amount Paid'       => "Rs. 25,000.00",
        'Remaining Due Now' => "<strong style='color:#dc2626; font-size:16px;'>Rs. 25,000.00</strong>"
    ],
    "Pay Remaining Balance Online",
    "https://www.gjrti.gov.lk/CoursePay/second-installment.php?ref=TEST-98E6C6DC",
    "Please settle your pending balance promptly to ensure continuous participation in lectures."
);

echo "Attempting to send attractive test email to: " . htmlspecialchars($recipient) . "<br><br>";

$sent = Mailer::send($recipient, $subject, $html, true, 'no-reply@gjrti.gov.lk');

if ($sent) {
    echo "<b style='color:green;'>SUCCESS: Attractive test email dispatched successfully!</b><br>";
} else {
    echo "<b style='color:red;'>FAILED: Email sending failed. Check logs/mail_errors.log for details.</b><br>";
}
