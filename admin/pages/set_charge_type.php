<?php
session_start();
require_once __DIR__ . '/../../classes/db.php';

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'])) {
    $application_id = (int)$_POST['application_id'];
    $charge_type = $_POST['charge_type'] === 'free' ? 'free' : 'payable';

    // Update the application
    $stmt = $conn->prepare("UPDATE applications SET charge_type = ? WHERE id = ?");
    $stmt->bind_param("si", $charge_type, $application_id);
    $stmt->execute();
    $stmt->close();

    // Get registration fee & course type for "free" case
    $stmt = $conn->prepare("SELECT registration_fee, course_type FROM applications WHERE id = ?");
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $app = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $reg_fee = $app['registration_fee'] ?? 0.0;
    $course_type = $app['course_type'] ?? '';

    if ($charge_type === 'free') {
        // Only registration fee is required (5000 for Diploma Level Courses, 2000 for Certificate level)
        $new_amount = ($course_type === 'Diploma Level Courses') ? 5000.00 : ($reg_fee ?: 2000.00);

        $update_stmt = $conn->prepare("
            UPDATE payments 
            SET amount = ?, 
                due_amount = GREATEST(? - COALESCE(paid_amount, 0), 0)
            WHERE application_id = ?
        ");
        $update_stmt->bind_param("ddi", $new_amount, $new_amount, $application_id);
        $update_stmt->execute();
        $update_stmt->close();
    }

    $_SESSION['msg'] = "Charge type updated successfully!";
}

header("Location: dashboard.php");
exit;