<?php
session_start();
require_once __DIR__ . '/../../classes/db.php';
require_once __DIR__ . '/../../classes/Mailer.php';

$db = new Database();
$conn = $db->getConnection();

// Handle GET request with ref (reference_no)
$reference_no = trim($_GET['ref'] ?? '');
if (empty($reference_no)) {
    die("Invalid reference number.");
}

// Fetch student details using reference_no
$stmt = $conn->prepare("
    SELECT s.id, s.name, s.gmail, s.reference_no,
           a.id AS application_id, a.course_name, a.regional_centre,
           a.registration_fee, a.course_fee, a.refundable_deposit,
           p.id AS payment_id, p.paid_amount, p.due_amount, p.amount, p.status
    FROM students s
    JOIN applications a ON s.id = a.student_id
    LEFT JOIN payments p ON a.id = p.application_id
    WHERE s.reference_no = ?
    ORDER BY p.id DESC LIMIT 1
");
$stmt->bind_param("s", $reference_no);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    die("Student not found.");
}

if (empty($data['gmail'])) {
    $_SESSION['msg'] = "Error: Student does not have an email address on record.";
    header("Location: dashboard.php");
    exit;
}

$refundable_deposit = (float)($data['refundable_deposit'] ?? 0);
$registration_fee   = (float)($data['registration_fee'] ?? 0);
$course_fee          = (float)($data['course_fee'] ?? 0);
$total_amount        = $registration_fee + $course_fee + $refundable_deposit;

$paid_amount = (float)($data['paid_amount'] ?? 0);
$due_amount  = isset($data['due_amount']) && $data['due_amount'] !== null 
    ? (float)$data['due_amount'] 
    : ($course_fee / 2);

// Send second installment reminder email via authenticated Mailer
$to = $data['gmail'];
$subject = "Payment Reminder: Second Installment - " . $data['course_name'];
$message = "Dear {$data['name']},

We hope you have started your course and are enjoying the learning experience!

This is a friendly reminder about your outstanding installment payment due for:
Course: {$data['course_name']}
Regional Centre: {$data['regional_centre']}
Reference Number: {$data['reference_no']}

Payment Details:
- Amount Paid: Rs. " . number_format($paid_amount, 2) . "
- Remaining Due Now: Rs. " . number_format($due_amount, 2) . "

Please complete your installment payment using the link below:
https://www.gjrti.gov.lk/CoursePay/second-installment.php?ref=" . urlencode($data['reference_no']) . "

If you have already made the payment, please disregard this reminder.

For any queries, please contact us at:
Email: info@gjrti.gov.lk

Best regards,
Gem and Jewellery Research and Training Institute (GJRTI)
";

try {
    if (Mailer::send($to, $subject, $message, false, 'no-reply@gjrti.gov.lk')) {
        $_SESSION['msg'] = "Second installment reminder sent successfully to {$data['name']} ({$data['gmail']})!";
    } else {
        $_SESSION['msg'] = "Failed to send reminder email. Please check mail settings.";
    }
} catch (Throwable $e) {
    error_log("send_installment_reminder.php error: " . $e->getMessage());
    $_SESSION['msg'] = "Failed to send reminder: " . $e->getMessage();
}

header("Location: dashboard.php");
exit;

$conn->close();
?>
