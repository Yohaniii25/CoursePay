<?php
require_once __DIR__ . '/classes/Mailer.php';

$recipient = $_GET['to'] ?? 'test@example.com';
$subject   = "CoursePay Webmail Test - " . date('Y-m-d H:i:s');
$message   = "<h2>Webmail Test Successful</h2><p>This is a test email sent from GJRTI CoursePay via Webmail SMTP.</p>";

echo "Attempting to send test email to: " . htmlspecialchars($recipient) . "<br><br>";

$sent = Mailer::send($recipient, $subject, $message, true);

if ($sent) {
    echo "<b style='color:green;'>SUCCESS: Test email dispatch completed!</b><br>";
} else {
    echo "<b style='color:red;'>FAILED: Email sending failed. Check logs/mail_errors.log for details.</b><br>";
}

if (file_exists(__DIR__ . '/logs/mail_errors.log')) {
    echo "<h3>Recent Error Log:</h3>";
    echo "<pre style='background:#f4f4f4; padding:10px; border:1px solid #ccc;'>" . htmlspecialchars(file_get_contents(__DIR__ . '/logs/mail_errors.log')) . "</pre>";
}
