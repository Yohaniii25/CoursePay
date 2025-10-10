<?php
require_once __DIR__ . '/../../classes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
    $student_id = intval($_POST['student_id']);

    $db = new Database();
    $conn = $db->getConnection();

    $sql = "UPDATE students SET checked = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();
        header("Location: dashboard.php");
        exit();
    } else {
        die("Database error: " . $conn->error);
    }
}
?>
