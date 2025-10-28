<?php
session_start();
require_once __DIR__ . '/../../classes/db.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        if (!$conn->begin_transaction()) {
            throw new Exception("Failed to start transaction: " . $conn->error);
        }

        $student_id = filter_var($_POST['student_id']);
        $action = filter_var($_POST['action']);

        if (empty($student_id) || empty($action)) {
            throw new Exception("Invalid input data.");
        }

        if ($action === 'approve') {

            $stmt = $conn->prepare("UPDATE students SET checked = 1 WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("i", $student_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to update student record: " . $stmt->error);
            }
            $stmt->close();

            // Send approval email to student
            $gmail = filter_var($_POST['gmail']);
            $name = filter_var($_POST['name']);
            $course_name = filter_var($_POST['course_name']);
            $regional_centre = filter_var($_POST['regional_centre']);
            $amount = (float)$_POST['amount'];
            $reference_no = filter_var($_POST['reference_no']);

            if (!empty($gmail)) {
                $to = $gmail;
                $subject = "Application Approval Notification";
                $message = "
Dear $name,

Congratulations! Your application for the course '$course_name' at $regional_centre has been approved.

Reference Number: $reference_no
Total Fee: Rs. " . number_format($amount, 2) . "

Please proceed with the payment to confirm your enrollment by visiting the following link:
https://sltdigital.site/coursePay/proceed-to-pay.php?ref=$reference_no

Best regards,
Gem and Jewellery Research and Training Institute
";
                $headers = "From: no-reply@sltdigital.site\r\n";
                $headers .= "Reply-To: no-reply@sltdigital.site\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

                if (!mail($to, $subject, $message, $headers)) {
                    error_log("Failed to send approval email to $to");
                }
            }

        } elseif ($action === 'not_approved') {
 
            $stmt = $conn->prepare("
                DELETE p FROM payments p
                INNER JOIN applications a ON p.application_id = a.id
                WHERE a.student_id = ?
            ");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("i", $student_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete payment record: " . $stmt->error);
            }
            $stmt->close();


            $stmt = $conn->prepare("DELETE FROM applications WHERE student_id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("i", $student_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete application record: " . $stmt->error);
            }
            $stmt->close();

      
            $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("i", $student_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to delete student record: " . $stmt->error);
            }
            $stmt->close();
        } else {
            throw new Exception("Invalid action specified.");
        }

        if (!$conn->commit()) {
            throw new Exception("Failed to commit transaction: " . $conn->error);
        }

       
        header("Location: dashboard.php");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        die("Error: " . htmlspecialchars($e->getMessage()));
    }
} else {
    die("Invalid request method.");
}


?>