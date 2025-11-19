<?php
require_once __DIR__ . '/classes/db.php';

/*
|--------------------------------------------------------------------------
| Ensure Logs Directory Exists
|--------------------------------------------------------------------------
*/
$logDir = __DIR__ . '/logs/';

if (!is_dir($logDir)) {
    if (!mkdir($logDir, 0755, true)) {
        die("Error: Could not create logs directory.");
    }
    chmod($logDir, 0755);
}

// Log incoming POST data
error_log(
    date('[Y-m-d H:i:s] ') . "submit.php: POST data: " . json_encode($_POST, JSON_PRETTY_PRINT),
    3,
    $logDir . 'error.log'
);

/*
|--------------------------------------------------------------------------
| Validate Request Method
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log(
        date('[Y-m-d H:i:s] ') . "Invalid request method: " . $_SERVER['REQUEST_METHOD'],
        3,
        $logDir . 'error.log'
    );
    die("Error: Invalid request method.");
}

/*
|--------------------------------------------------------------------------
| Database Connection
|--------------------------------------------------------------------------
*/
$db = new Database();
$conn = $db->getConnection();

/*
|--------------------------------------------------------------------------
| Sanitize & Validate Inputs
|--------------------------------------------------------------------------
*/
$name                   = trim(filter_var($_POST['name'] ?? ''));
$contact_number         = trim(filter_var($_POST['contact_number'] ?? ''));
$gmail                  = filter_var($_POST['gmail'] ?? '');
$address                = trim(filter_var($_POST['address'] ?? ''));
$regional_centre        = trim(filter_var($_POST['regional_centre'] ?? ''));
$course_type            = trim(filter_var($_POST['course_type'] ?? ''));
$course_name            = trim(filter_var($_POST['course'] ?? ''));
$reg_fee                = (float) ($_POST['reg_fee'] ?? 0);
$course_fee             = (float) ($_POST['course_fee'] ?? 0);
$nic_passport           = trim(filter_var($_POST['nic_passport'] ?? ''));
$education_background   = trim(filter_var($_POST['education_background'] ?? ''));

/*
|--------------------------------------------------------------------------
| Validate Declarations
|--------------------------------------------------------------------------
*/
$decl1 = isset($_POST['declaration']) && $_POST['declaration'] == '1';
$decl2 = isset($_POST['declaration2']) && $_POST['declaration2'] == '1';

if (!$decl1 || !$decl2) {
    error_log(
        date('[Y-m-d H:i:s] ') . "Declarations missing: decl1=$decl1, decl2=$decl2",
        3,
        $logDir . 'error.log'
    );
    die("Error: You must agree to both declarations to submit the application.");
}

$declaration = 1;

/*
|--------------------------------------------------------------------------
| Required Field Validations
|--------------------------------------------------------------------------
*/
if (empty($name) || empty($contact_number) || empty($regional_centre) || empty($course_name) || empty($nic_passport)) {
    error_log(date('[Y-m-d H:i:s] ') . "Missing required fields.", 3, $logDir . 'error.log');
    die("Error: Please fill all required fields.");
}

/*
|--------------------------------------------------------------------------
| NIC/Passport File Upload
|--------------------------------------------------------------------------
*/
$nic_file_path = '';

if (!empty($_FILES['nic_file']['name']) && $_FILES['nic_file']['error'] === UPLOAD_ERR_OK) {

    $uploadDir = __DIR__ . "/Uploads/nic/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = time() . "_" . basename($_FILES['nic_file']['name']);
    $nic_file_path = "Uploads/nic/" . $filename;
    $target_path = __DIR__ . '/' . $nic_file_path;

    $fileType = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];

    if (!in_array($fileType, $allowedTypes)) {
        die("Error: Invalid file type. Only JPG, JPEG, PNG, and PDF are allowed.");
    }

    if (!move_uploaded_file($_FILES['nic_file']['tmp_name'], $target_path)) {
        error_log(date('[Y-m-d H:i:s] ') . "NIC upload failed.", 3, $logDir . 'error.log');
        die("Error: Failed to upload NIC/Passport file.");
    }
} else {
    die("Error: NIC/Passport file is required.");
}

/*
|--------------------------------------------------------------------------
| Generate Unique Reference Number
|--------------------------------------------------------------------------
*/
$reference_no = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

/*
|--------------------------------------------------------------------------
| Database Transaction (Insert Student + Application)
|--------------------------------------------------------------------------
*/
try {
    $conn->begin_transaction();

    /*
    |---------------------------------------
    | Insert Student
    |---------------------------------------
    */
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

    if (!$stmt->execute()) {
        throw new Exception("Student insert failed: " . $stmt->error);
    }

    $student_id = $conn->insert_id;
    $stmt->close();

    /*
    |---------------------------------------
    | Insert Application
    |---------------------------------------
    */
    $stmt = $conn->prepare("
        INSERT INTO applications 
        (student_id, regional_centre, course_type, course_name, registration_fee, course_fee)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssdd",
        $student_id,
        $regional_centre,
        $course_type,
        $course_name,
        $reg_fee,
        $course_fee
    );

    if (!$stmt->execute()) {
        throw new Exception("Application insert failed: " . $stmt->error);
    }

    $stmt->close();

    /*
    |---------------------------------------
    | Send Confirmation Emails
    |---------------------------------------
    */
    $total_fee = $reg_fee + $course_fee;
    $msg = "Dear $name,

Your application has been successfully submitted!

Reference No: $reference_no
Course: $course_name
Centre: $regional_centre
Total Fee: Rs. " . number_format($total_fee, 2) . "

You'll receive payment instructions after admin approval.

Thank you!";

    $headers = "From: no-reply@sltdigital.site\r\nContent-Type: text/plain; charset=UTF-8\r\n";

    mail($gmail, "Application Submitted - Ref: $reference_no", $msg, $headers);
    mail("sutharshankanna04@gmail.com", "New Application Received", "New Application\nRef: $reference_no\nName: $name\nCourse: $course_name", $headers);

    /*
    |---------------------------------------
    | Commit Transaction
    |---------------------------------------
    */
    $conn->commit();

    header("Location: application-success.php?ref=$reference_no");
    exit;

} catch (Exception $e) {

    $conn->rollback();

    error_log(
        date('[Y-m-d H:i:s] ') . "Transaction failed: " . $e->getMessage(),
        3,
        $logDir . 'error.log'
    );

    die("Application failed. Please try again later.");
}
?>
