<?php
require_once __DIR__ . '/../classes/db.php';


$db = new Database();
$conn = $db->getConnection();


$username = 'admin_gem';
$email = 'admin_gem@gmail.com';
$password = '7R%3Qk8x#';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);


$sql = "INSERT INTO admin (username, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("sss", $username, $email, $hashedPassword);

if ($stmt->execute()) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
