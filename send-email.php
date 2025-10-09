<?php
require_once __DIR__ . '/classes/db.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


if (!isset($_POST['reference_no']) || !isset($_POST['transaction_id'])) {
    header('Location: complete.php?email_status=error');
    exit();
}

$reference_no = $_POST['reference_no'];
$transaction_id = $_POST['transaction_id'];


$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("
    SELECT s.name, s.email AS gmail, a.regional_centre, a.course_name, p.amount
    FROM students s
    JOIN applications a ON s.id = a.student_id
    JOIN payments p ON a.id = p.application_id
    WHERE s.reference_no = ? AND p.transaction_id = ?
");
$stmt->bind_param("ss", $reference_no, $transaction_id);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$application || empty($application['gmail'])) {
    header('Location: complete.php?email_status=error');
    exit();
}


$to = $application['gmail'];
$subject = 'Payment Confirmation for Your Course Application';
$message = <<<EOD
Dear {$application['name']},

Your payment for the course application has been successfully processed. Here are the details:

- Reference Number: {$reference_no}
- Name: {$application['name']}
- Regional Centre: {$application['regional_centre']}
- Course: {$application['course_name']}
- Total Amount Paid: Rs. {$formatted_amount}

Thank you for your payment!

Best regards,
CoursePay Team
EOD;


$formatted_amount = number_format($application['amount'], 2);
$message = str_replace('{$formatted_amount}', $formatted_amount, $message);

$headers = "From: no-reply@sltdigital.site\r\n" .
           "Reply-To: no-reply@sltdigital.site\r\n" .
           "X-Mailer: PHP/" . phpversion();

if (mail($to, $subject, $message, $headers)) {
    header('Location: complete.php?email_status=success');
} else {
    error_log("Failed to send email for reference_no: $reference_no");
    header('Location: complete.php?email_status=error');
}
exit();
?>