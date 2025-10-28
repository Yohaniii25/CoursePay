<?php
require_once dirname(__FILE__) . '/classes/db.php';

// Ensure logs directory exists
$logDir = dirname(__FILE__) . '/logs/';
if (!is_dir($logDir)) {
    if (!mkdir($logDir, 0755, true)) {
        die("Error: Failed to create logs directory: " . $logDir);
    }
    chmod($logDir, 0755);
}

// Log POST data
error_log(date('[Y-m-d H:i:s] ') . "submit.php: POST data: " . json_encode($_POST, JSON_PRETTY_PRINT), 3, $logDir . 'error.log');

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log(date('[Y-m-d H:i:s] ') . "Invalid request method: " . $_SERVER['REQUEST_METHOD'], 3, $logDir . 'error.log');
    die("Error: Invalid request method.");
}

// Sanitize and validate input
$name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING);
$contact_number = filter_var($_POST['contact_number'] ?? '', FILTER_SANITIZE_STRING);
$gmail = filter_var($_POST['gmail'] ?? '', FILTER_SANITIZE_EMAIL);
$address = filter_var($_POST['address'] ?? '', FILTER_SANITIZE_STRING);
$regional_centre = filter_var($_POST['regional_centre'] ?? '', FILTER_SANITIZE_STRING);
$course_type = filter_var($_POST['course_type'] ?? '', FILTER_SANITIZE_STRING);
$course_name = filter_var($_POST['course'] ?? '', FILTER_SANITIZE_STRING);
$reg_fee = (float)($_POST['reg_fee'] ?? 0);
$course_fee = (float)($_POST['course_fee'] ?? 0);
$nic_passport = filter_var($_POST['nic_passport'] ?? '', FILTER_SANITIZE_STRING);
$education_background = filter_var($_POST['education_background'] ?? '', FILTER_SANITIZE_STRING);
$declaration = isset($_POST['declaration']) && $_POST['declaration'] == '1';

if (empty($name) || empty($contact_number) || empty($regional_centre) || empty($course_name) || empty($nic_passport) || !$declaration) {
    error_log(date('[Y-m-d H:i:s] ') . "Missing required fields: name=$name, contact_number=$contact_number, regional_centre=$regional_centre, course_name=$course_name, nic_passport=$nic_passport, declaration=$declaration", 3, $logDir . 'error.log');
    die("Error: Please fill all required fields.");
}

// Handle NIC/Passport file upload
$nic_file_path = '';
if (!empty($_FILES['nic_file']['name'])) {
    $uploadDir = dirname(__FILE__) . "/Uploads/nic/";
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            error_log(date('[Y-m-d H:i:s] ') . "Failed to create NIC upload directory: $uploadDir", 3, $logDir . 'error.log');
            die("Error: Failed to create NIC upload directory.");
        }
    }
    $filename = time() . "_" . basename($_FILES['nic_file']['name']);
    $nic_file_path = "Uploads/nic/" . $filename;
    $fileType = strtolower(pathinfo($nic_file_path, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
    if (!in_array($fileType, $allowedTypes)) {
        error_log(date('[Y-m-d H:i:s] ') . "Invalid NIC file type: $fileType", 3, $logDir . 'error.log');
        die("Error: Invalid NIC file type. Allowed types: JPG, JPEG, PNG, PDF.");
    }
    if (!move_uploaded_file($_FILES['nic_file']['tmp_name'], dirname(__FILE__) . '/' . $nic_file_path)) {
        error_log(date('[Y-m-d H:i:s] ') . "Failed to upload NIC file: " . $_FILES['nic_file']['name'], 3, $logDir . 'error.log');
        die("Error: Failed to upload NIC file.");
    }
} else {
    error_log(date('[Y-m-d H:i:s] ') . "NIC file is required", 3, $logDir . 'error.log');
    die("Error: NIC file is required.");
}

// Generate unique reference number
$reference_no = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

try {
    if (!$conn->begin_transaction()) {
        throw new Exception("Failed to start transaction: " . $conn->error);
    }

    // Insert into students table
    $stmt = $conn->prepare("
        INSERT INTO students (reference_no, name, contact_number, gmail, address, nic_passport, nic_file, education_background)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssssssss", $reference_no, $name, $contact_number, $gmail, $address, $nic_passport, $nic_file_path, $education_background);
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert student: " . $stmt->error);
    }
    $student_id = $conn->insert_id;
    $stmt->close();

    // Insert into applications table
    $stmt = $conn->prepare("
        INSERT INTO applications (student_id, regional_centre, course_type, course_name, registration_fee, course_fee)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("isssdd", $student_id, $regional_centre, $course_type, $course_name, $reg_fee, $course_fee);
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert application: " . $stmt->error);
    }
    $application_id = $conn->insert_id;
    $stmt->close();

    // Do NOT insert into payments table here
    // Payment record will be created in process-payment.php or complete.php

    // Send email to student
    $to = $gmail;
    $subject = "Application Submitted Successfully";
    $message = "
Dear $name,

Your application for the course '$course_name' at $regional_centre has been successfully submitted.

Reference Number: $reference_no
Course Fee: Rs. " . number_format($course_fee, 2) . "
Registration Fee: Rs. " . number_format($reg_fee, 2) . "
Total Fee: Rs. " . number_format($reg_fee + $course_fee, 2) . "

Please use the following link to proceed with payment:
http://localhost/CoursePay/proceed-to-pay.php?ref=$reference_no

Best regards,
Gem and Jewellery Research and Training Institute
";
    $headers = "From: no-reply@sltdigital.site\r\n";
    $headers .= "Reply-To: no-reply@sltdigital.site\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    if (!mail($to, $subject, $message, $headers)) {
        error_log(date('[Y-m-d H:i:s] ') . "Failed to send application confirmation email to $to", 3, $logDir . 'error.log');
    }

    // Send email to admin
    $admin_email = 'yohanii725@gmail.com';
    $admin_subject = "New Application Received";
    $admin_message = "
Dear Admin,

A new application has been received with the following details:

Student Name: $name
Reference Number: $reference_no
Course: $course_name
Regional Centre: $regional_centre
Course Fee: Rs. " . number_format($course_fee, 2) . "
Registration Fee: Rs. " . number_format($reg_fee, 2) . "
Total Fee: Rs. " . number_format($reg_fee + $course_fee, 2) . "

Please verify the application in the system.

Best regards,
Gem and Jewellery Research and Training Institute
";
    $admin_headers = "From: no-reply@sltdigital.site\r\n";
    $admin_headers .= "Reply-To: no-reply@sltdigital.site\r\n";
    $admin_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    if (!mail($admin_email, $admin_subject, $admin_message, $admin_headers)) {
        error_log(date('[Y-m-d H:i:s] ') . "Failed to send application notification email to $admin_email", 3, $logDir . 'error.log');
    }

    if (!$conn->commit()) {
        throw new Exception("Failed to commit transaction: " . $conn->error);
    }

    $conn->close();
    header("Location: application-success.php?ref=$reference_no");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    $conn->close();
    error_log(date('[Y-m-d H:i:s] ') . "Error in submit.php: " . $e->getMessage(), 3, $logDir . 'error.log');
    die("Error: " . htmlspecialchars($e->getMessage()));
}
?>