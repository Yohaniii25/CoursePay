<?php
session_start();
require_once __DIR__ . '/../../classes/db.php';
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || !in_array($_POST['action'], ['approve_free', 'approve_payable'])) {
    header("Location: dashboard.php");
    exit;
}

try {
    $conn->begin_transaction();

    $student_id = (int)$_POST['student_id'];
    $gmail = $_POST['gmail'] ?? '';
    $name = $_POST['name'] ?? '';
    $course_name = $_POST['course_name'] ?? '';
    $regional_centre = $_POST['regional_centre'] ?? '';
    $reference_no = $_POST['reference_no'] ?? '';
    $action = $_POST['action'];

    if (!$student_id) {
        throw new Exception("Invalid student ID.");
    }

    // Get application
    $stmt = $conn->prepare("SELECT id FROM applications WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $app = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$app) throw new Exception("Application not found.");
    $application_id = $app['id'];

    // DETERMINE DUE AMOUNT
    if ($action === 'approve_free') {
        $due_amount = 2000.00;
        $charge_type = 'free';
        $email_msg = "FREE course approved – Registration Fee: Rs. 2000.00 only";
    } else {
        // FOR PAYABLE: USE THE LATEST due_amount FROM PAYMENTS (ADMIN SET)
        $stmt = $conn->prepare("
            SELECT due_amount 
            FROM payments 
            WHERE application_id = ? 
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->bind_param("i", $application_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $payment = $result->fetch_assoc();
        $stmt->close();

        if ($payment && $payment['due_amount'] > 0) {
            $due_amount = (float)$payment['due_amount'];
        } else {
            // Fallback: reg + course fee (for normal courses)
            $stmt = $conn->prepare("SELECT registration_fee + course_fee AS total FROM applications WHERE id = ?");
            $stmt->bind_param("i", $application_id);
            $stmt->execute();
            $fallback = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $due_amount = $fallback ? (float)$fallback['total'] : 0;
        }

        $charge_type = 'payable';
        $email_msg = "Application approved – Full Course Fee: Rs. " . number_format($due_amount, 2);
    }

    // Update charge_type
    $stmt = $conn->prepare("UPDATE applications SET charge_type = ? WHERE id = ?");
    $stmt->bind_param("si", $charge_type, $application_id);
    $stmt->execute();
    $stmt->close();

    // Mark student as approved
    $stmt = $conn->prepare("UPDATE students SET checked = 1 WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->close();

    // Create/Update payment record
    $stmt = $conn->prepare("
        INSERT INTO payments 
            (application_id, amount, paid_amount, due_amount, status, method)
        VALUES (?, ?, 0, ?, 'pending', 'Online Payment')
        ON DUPLICATE KEY UPDATE
            amount = VALUES(amount),
            paid_amount = 0,
            due_amount = VALUES(due_amount),
            status = 'pending'
    ");
    $stmt->bind_param("idd", $application_id, $due_amount, $due_amount);
    $stmt->execute();
    $stmt->close();

    // SEND EMAIL
    if (!empty($gmail)) {
        $to = $gmail;
        $subject = $action === 'approve_free' ? "Application Approved – FREE Course" : "Application Approved – Proceed to Payment";
        $message = "
Dear $name,

$email_msg

Reference No: $reference_no
Course: $course_name
Centre: $regional_centre

Payment Link: https://sltdigital.site/gem/CoursePay/proceed-to-pay.php?ref=$reference_no

Best regards,
Gem and Jewellery Research and Training Institute
        ";
        $headers = "From: no-reply@sltdigital.site\r\nContent-Type: text/plain; charset=UTF-8\r\n";
        mail($to, $subject, $message, $headers);
    }

    $conn->commit();

    $_SESSION['msg'] = $action === 'approve_free'
        ? "Approved as FREE – Due: Rs. 2000"
        : "Approved as PAYABLE – Due: Rs. " . number_format($due_amount, 2);

    header("Location: dashboard.php");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['msg'] = "Error: " . $e->getMessage();
    header("Location: dashboard.php");
    exit;
}
?>