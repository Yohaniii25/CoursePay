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

    // Get registration fee for "free" case
    $stmt = $conn->prepare("SELECT registration_fee FROM applications WHERE id = ?");
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $stmt->bind_result($reg_fee);
    $stmt->fetch();
    $stmt->close();

    if ($charge_type === 'free') {
        // Only registration fee is required
        $new_amount = $reg_fee; // usually 2000

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