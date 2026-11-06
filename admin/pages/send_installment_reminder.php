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

// Send attractive second installment reminder email
$to = $data['gmail'];
$subject = "Payment Reminder: Course Installment - " . $data['course_name'];
$html = Mailer::buildTemplate(
    "Course Fee Installment Reminder",
    "Payment Due",
    "orange",
    "Dear " . htmlspecialchars($data['name']) . ",",
    "We hope you are having an enriching learning experience with GJRTI! This is a friendly reminder regarding your outstanding course installment payment.",
    [
        'Reference Number'  => "<strong style='color:#25116F; font-size:16px;'>{$data['reference_no']}</strong>",
        'Course Name'       => $data['course_name'],
        'Regional Centre'   => $data['regional_centre'],
        'Amount Paid'       => "Rs. " . number_format($paid_amount, 2),
        'Remaining Due Now' => "<strong style='color:#dc2626; font-size:16px;'>Rs. " . number_format($due_amount, 2) . "</strong>"
    ],
    "Pay Remaining Balance Online",
    "https://www.gjrti.gov.lk/CoursePay/second-installment.php?ref=" . urlencode($data['reference_no']),
    "Please settle your pending balance promptly to ensure continuous participation in lectures and practical examination eligibility. If you have already paid, kindly disregard this notice."
);

try {
    if (Mailer::send($to, $subject, $html, true, 'no-reply@gjrti.gov.lk')) {
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
