<?php
session_start();
require_once __DIR__ . '/../../classes/db.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

$student_id = (int)$_POST['student_id'];
$action = $_POST['action'] ?? '';

if (empty($student_id) || !in_array($action, ['send_reminder', 'mark_sent'])) {
    die("Invalid parameters.");
}

try {
    // Fetch student and application details
    $stmt = $conn->prepare("
        SELECT s.id, s.name, s.gmail, s.reference_no,
               a.id AS application_id, a.course_name, a.regional_centre, 
               a.registration_fee, a.course_fee,
               p.id AS payment_id, p.paid_amount, p.due_amount, p.amount, p.status
        FROM students s
        JOIN applications a ON s.id = a.student_id
        LEFT JOIN payments p ON a.id = p.application_id
        WHERE s.id = ?
    ");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();

    if (!$data) {
        die("Student not found.");
    }

    $total_amount = $data['registration_fee'] + $data['course_fee'];
    $registration_fee = $data['registration_fee'];
    $course_fee = $data['course_fee'];
    $fifty_percent_amount = $registration_fee + ($course_fee / 2);
    $remaining_fifty_percent = $course_fee / 2;

    if ($action === 'send_reminder') {
        // Send second installment reminder email
        $to = $data['gmail'];
        $subject = "Second Installment Payment Reminder - Course Payment";
        $message = "Dear {$data['name']},

We hope you have started your course and are enjoying the learning experience!

This is a friendly reminder about your second installment payment due for:

**Course:** {$data['course_name']}
**Regional Centre:** {$data['regional_centre']}
**Reference Number:** {$data['reference_no']}

**Payment Details:**
- First Installment (Already Paid): Rs. " . number_format($fifty_percent_amount, 2) . "
- Second Installment (Due Now): Rs. " . number_format($remaining_fifty_percent, 2) . "

Please complete the second installment payment to continue your enrollment.

Payment Link: https://sltdigital.site/gem/CoursePay/proceed-to-pay.php?ref={$data['reference_no']}

If you have already made the payment, please disregard this reminder.

For any queries, please contact us at:
Email: info@sltdigital.site
Phone: [Contact Number]

Best regards,
Gem and Jewellery Research and Training Institute
";
        
        $headers = "From: no-reply@sltdigital.site\r\n";
        $headers .= "Reply-To: no-reply@sltdigital.site\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (mail($to, $subject, $message, $headers)) {
            $_SESSION['msg'] = "Second installment reminder sent successfully to {$data['name']}!";
        } else {
            $_SESSION['msg'] = "Failed to send reminder email. Please try again.";
        }
    }

    $conn->close();
    header("Location: dashboard.php");
    exit;

} catch (Exception $e) {
    $_SESSION['msg'] = "Error: " . $e->getMessage();
    $conn->close();
    header("Location: dashboard.php");
    exit;
}
?>
