<?php
session_start();
require_once __DIR__ . '/../classes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        header("Location: index.php?error=Email and password are required");
        exit;
    }

    $database = new Database();
    $conn = $database->getConnection();


    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user'] = [
            'email' => $user['email'],
            'username' => $user['username'] ?? $user['email'] 
        ];
        header("Location: ./pages/dashboard.php");
        exit;
    } else {
        header("Location: index.php?error=Invalid email or password");
        exit;
    }
} else {
    header("Location: index.php?error=Invalid request method");
    exit;
}
?>