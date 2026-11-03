<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$logDir = __DIR__ . '/logs/';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
    @chmod($logDir, 0755);
}

if (is_dir($logDir) && is_writable($logDir)) {
    @error_log(date('[Y-m-d H:i:s] ') . "submit.php: POST data: " . json_encode($_POST, JSON_PRETTY_PRINT), 3, $logDir . 'error.log');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Error: Invalid request method.");
}

// Detect when post_max_size was exceeded (PHP discards $_POST and $_FILES if total size exceeds limit)
if (empty($_POST) && isset($_SERVER['CONTENT_LENGTH']) && (int)$_SERVER['CONTENT_LENGTH'] > 0) {
    $postMaxSize = ini_get('post_max_size');
    die("Application failed: Uploaded file or form data is too large for the server. The server limit is {$postMaxSize}. Please upload a smaller file (under 5MB).");
}

require_once __DIR__ . '/classes/db.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        throw new Exception("Database connection failed.");
    }

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
    $refundable_deposit = (float)($_POST['refundable_deposit'] ?? 0);
    $nic_passport       = trim($_POST['nic_passport'] ?? '');
    $education_background = trim($_POST['education_background'] ?? '');

    // Declarations (support '1' or 'on')
    $dec1 = !empty($_POST['declaration']) && ($_POST['declaration'] == '1' || $_POST['declaration'] === 'on');
    $dec2 = !empty($_POST['declaration2']) && ($_POST['declaration2'] == '1' || $_POST['declaration2'] === 'on');

    if (!$dec1 || !$dec2) {
        throw new Exception("You must agree to both declarations.");
    }
    $declaration = 1;

    // Required fields
    if (empty($name) || empty($contact_number) || empty($regional_centre) || empty($course_name) || empty($nic_passport)) {
        throw new Exception("Please fill all required fields.");
    }

    // NIC File Upload
    $nic_file_path = '';
    if (!empty($_FILES['nic_file']['name']) && $_FILES['nic_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . "/Uploads/nic/";
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0777, true);

        $filename = time() . "_" . basename($_FILES['nic_file']['name']);
        $nic_file_path = "Uploads/nic/" . $filename;
        $target_path = __DIR__ . '/' . $nic_file_path;

        $ext = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'pdf'])) {
            throw new Exception("Invalid file type. Only JPG, PNG, PDF allowed.");
        }

        if (!move_uploaded_file($_FILES['nic_file']['tmp_name'], $target_path)) {
            throw new Exception("Failed to upload NIC file. Please check folder permissions.");
        }
    } else {
        if (isset($_FILES['nic_file']['error'])) {
            switch ($_FILES['nic_file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new Exception("File is too large. Server limit is " . ini_get('upload_max_filesize') . ". Please upload a smaller file.");
                case UPLOAD_ERR_PARTIAL:
                    throw new Exception("File was only partially uploaded. Please try again.");
                case UPLOAD_ERR_NO_FILE:
                    throw new Exception("NIC/Passport file is required.");
                default:
                    throw new Exception("Failed to upload NIC/Passport file (Error code: " . $_FILES['nic_file']['error'] . ").");
            }
        }
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
        (student_id, regional_centre, course_type, course_name, registration_fee, course_fee, refundable_deposit)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssddd", $student_id, $regional_centre, $course_type, $course_name, $reg_fee, $course_fee, $refundable_deposit);
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
        $total_due = $reg_fee + $course_fee + $refundable_deposit;

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

    // Prepare email message
    $msg = "Dear $name,\n\nYour application has been submitted!\n\n";
    $msg .= "Reference No: $reference_no\n";
    $msg .= "Course: $course_name\n";
    $msg .= "Centre: $regional_centre\n\n";

    if ($is_tailor_made) {
        $msg .= "This is a Tailor-Made course. The admin will contact you with the exact fee and payment details.\n";
    } else {
        $msg .= "Total Fee: Rs. " . number_format($reg_fee + $course_fee + $refundable_deposit, 2) . "\n";
        $msg .= "Payment instructions will be sent after approval.\n";
    }

    $msg .= "\nThank you!\nGJRTI Team";

    // Send emails safely: do not fail application if Mailer is missing or SMTP fails
    try {
        if (file_exists(__DIR__ . '/classes/Mailer.php')) {
            require_once __DIR__ . '/classes/Mailer.php';
            if (class_exists('Mailer')) {
                if (!empty($gmail)) {
                    Mailer::send($gmail, "Application Submitted - Ref: $reference_no", $msg, false);
                }
                Mailer::send("yohanii725@gmail.com", "New Application - $reference_no", "New application from $name\nCourse: $course_name\nRef: $reference_no", false);
            }
        }
    } catch (Throwable $mailEx) {
        error_log("submit.php Mail Warning: " . $mailEx->getMessage());
    }

    $_SESSION['application_success'] = [
        'name' => $name,
        'ref' => $reference_no,
        'course' => $course_name
    ];

    header("Location: application-success.php?ref=$reference_no");
    exit;
} catch (Throwable $e) {
    if (isset($conn) && $conn instanceof mysqli && $conn->ping()) {
        @$conn->rollback();
    }
    error_log("submit.php ERROR: " . $e->getMessage());
    die("Application failed: " . $e->getMessage());
}
