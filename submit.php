<?php
session_start();
require_once __DIR__ . '/classes/db.php';

$logDir = __DIR__ . '/logs/';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
    chmod($logDir, 0755);
}

error_log(date('[Y-m-d H:i:s] ') . "submit.php: POST data: " . json_encode($_POST, JSON_PRETTY_PRINT), 3, $logDir . 'error.log');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Error: Invalid request method.");
}

$db = new Database();
$conn = $db->getConnection();

try {
    $conn->begin_transaction();

    // Sanitize inputs
    $name               = trim($_POST['name'] ?? '');
    $contact_number     = trim($_POST['contact_number'] ?? '');
    $gmail              = trim($_POST['gmail'] ?? '');
    $address            = trim($_POST['address'] ?? '');
    $regional_centre    = trim($_POST['regional_centre'] ?? '');
    $course_type        = trim($_POST['course_type'] ?? '');
    $course_name        = trim($_POST['course'] ?? '');
    $reg_fee            = (float)($_POST['reg_fee'] ?? 0);
    $course_fee         = (float)($_POST['course_fee'] ?? 0);
    $nic_passport       = trim($_POST['nic_passport'] ?? '');
    $education_background = trim($_POST['education_background'] ?? '');

    // Declarations
    $declaration = (isset($_POST['declaration']) && $_POST['declaration'] == '1') &&
                   (isset($_POST['declaration2']) && $_POST['declaration2'] == '1') ? 1 : 0;

    if (!$declaration) {
        throw new Exception("You must agree to both declarations.");
    }

    // Required fields
    if (empty($name) || empty($contact_number) || empty($regional_centre) || empty($course_name) || empty($nic_passport)) {
        throw new Exception("Please fill all required fields.");
    }

    // NIC File Upload
    $nic_file_path = '';
    if (!empty($_FILES['nic_file']['name']) && $_FILES['nic_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . "/Uploads/nic/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $filename = time() . "_" . basename($_FILES['nic_file']['name']);
        $nic_file_path = "Uploads/nic/" . $filename;
        $target_path = __DIR__ . '/' . $nic_file_path;

        $ext = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'])) {
            throw new Exception("Invalid file type. Only JPG, PNG, PDF allowed.");
        }

        if (!move_uploaded_file($_FILES['nic_file']['tmp_name'], $target_path)) {
            throw new Exception("Failed to upload NIC file.");
        }
    } else {
        throw new Exception("NIC/Passport file is required.");
    }

    // Generate Reference No
    $reference_no = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

    // Insert Student
    $stmt = $conn->prepare("
        INSERT INTO students 
        (reference_no, name, contact_number, gmail, address, nic_passport, nic_file, education_background, declaration, checked)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)
    ");
    $stmt->bind_param(
        "ssssssssi",
        $reference_no,
        $name,
        $contact_number,
        $gmail,
        $address,
        $nic_passport,
        $nic_file_path,
        $education_background,
        $declaration
    );
    $stmt->execute();
    $student_id = $conn->insert_id;
    $stmt->close();

    // Insert Application
    $stmt = $conn->prepare("
        INSERT INTO applications 
        (student_id, regional_centre, course_type, course_name, registration_fee, course_fee)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssdd", $student_id, $regional_centre, $course_type, $course_name, $reg_fee, $course_fee);
    $stmt->execute();
    $application_id = $conn->insert_id;
    $stmt->close();

    // TAILOR-MADE COURSES — DO NOT CREATE PAYMENT RECORD
    $tailorMadeCourses = [
        "Gem Related Certificate in Tailor – Made Courses",
        "Jewellery Certificate in Tailor – Made Courses"
    ];

    $is_tailor_made = in_array($course_name, $tailorMadeCourses);

    if (!$is_tailor_made) {
        $total_due = $reg_fee + $course_fee;

        $stmt = $conn->prepare("
            INSERT INTO payments 
            (application_id, amount, paid_amount, due_amount, status)
            VALUES (?, ?, 0, ?, 'pending')
        ");
        $stmt->bind_param("idd", $application_id, $total_due, $total_due);
        $stmt->execute();
        $stmt->close();
    }
    // Tailor-Made → No payment record → Admin sets fee later

    $conn->commit();

    // Email
    $msg = "Dear $name,\n\nYour application has been submitted!\n\n";
    $msg .= "Reference No: $reference_no\n";
    $msg .= "Course: $course_name\n";
    $msg .= "Centre: $regional_centre\n\n";

    if ($is_tailor_made) {
        $msg .= "This is a Tailor-Made course. The admin will contact you with the exact fee and payment details.\n";
    } else {
        $msg .= "Total Fee: Rs. " . number_format($reg_fee + $course_fee, 2) . "\n";
        $msg .= "Payment instructions will be sent after approval.\n";
    }

    $msg .= "\nThank you!\nGJRTI Team";

    $headers = "From: no-reply@sltdigital.site\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    mail($gmail, "Application Submitted - Ref: $reference_no", $msg, $headers);
    mail("yohanii725@gmail.com", "New Application - $reference_no", "New application from $name\nCourse: $course_name\nRef: $reference_no", $headers);

    $_SESSION['application_success'] = [
        'name' => $name,
        'ref' => $reference_no,
        'course' => $course_name
    ];

    header("Location: application-success.php?ref=$reference_no");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    error_log("submit.php ERROR: " . $e->getMessage());
    die("Application failed: " . $e->getMessage());
}
?>