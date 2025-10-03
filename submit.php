<?php
session_start(); 
require_once __DIR__ . '/classes/db.php';

$db = new Database();
$conn = $db->getConnection();

function generateReferenceNo($length = 8) {
    return strtoupper(substr(md5(uniqid(rand(), true)), 0, $length));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
   
        if (!$conn->begin_transaction()) {
            throw new Exception("Failed to start transaction: " . $conn->error);
        }


        if (empty($_POST['name']) || empty($_POST['nic_passport']) || 
            empty($_POST['regional_centre']) || empty($_POST['course_type']) || 
            empty($_POST['course']) || empty($_POST['reg_fee']) || 
            empty($_POST['course_fee'])) {
            throw new Exception("All required fields must be filled.");
        }

        $referenceNo = generateReferenceNo();


        $nicFilePath = null;
        if (!empty($_FILES['nic_file']['name'])) {
            $uploadDir = __DIR__ . "/uploads/nic/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $nicFilePath = "Uploads/nic/" . time() . "_" . basename($_FILES['nic_file']['name']);
            if (!move_uploaded_file($_FILES['nic_file']['tmp_name'], __DIR__ . '/' . $nicFilePath)) {
                throw new Exception("Failed to upload NIC file.");
            }
        } else {
            throw new Exception("NIC file is required.");
        }

        $stmt = $conn->prepare("
            INSERT INTO students (reference_no, name, nic_passport, contact_number, address, gmail, education_background, declaration, nic_file)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $name = $_POST['name'];
        $nic_passport = $_POST['nic_passport'];
        $contact_number = $_POST['contact_number'] ?? null;
        $address = $_POST['address'] ?? null;
        $gmail = $_POST['gmail'] ?? null;
        $education_background = $_POST['education_background'] ?? null;
        $declaration = isset($_POST['declaration']) ? 1 : 0;
        $stmt->bind_param("sssssssis", $referenceNo, $name, $nic_passport, $contact_number, $address, $gmail, $education_background, $declaration, $nicFilePath);
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert student: " . $stmt->error);
        }
        $studentId = $conn->insert_id;
        $stmt->close();

        $stmt = $conn->prepare("
            INSERT INTO applications (student_id, regional_centre, course_type, course_name, registration_fee, course_fee)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $regional_centre = $_POST['regional_centre'];
        $course_type = $_POST['course_type'];
        $course_name = $_POST['course'];
        $registration_fee = (float)$_POST['reg_fee'];
        $course_fee = (float)$_POST['course_fee'];
        $stmt->bind_param("isssdd", $studentId, $regional_centre, $course_type, $course_name, $registration_fee, $course_fee);
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert application: " . $stmt->error);
        }
        $applicationId = $conn->insert_id;
        $stmt->close();

        $totalAmount = (float)$_POST['reg_fee'] + (float)$_POST['course_fee'];

        $stmt = $conn->prepare("
            INSERT INTO payments (application_id, amount, method, status)
            VALUES (?, ?, ?, ?)
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $method = 'Online Payment';
        $status = 'pending';
        $stmt->bind_param("idss", $applicationId, $totalAmount, $method, $status);
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert payment: " . $stmt->error);
        }
        $stmt->close();

        $_SESSION['reference_no'] = $referenceNo;


        if (!$conn->commit()) {
            throw new Exception("Failed to commit transaction: " . $conn->error);
        }

        header("Location: proceed-to-pay.php");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        die("Error: " . $e->getMessage());
    } finally {
        $conn->close();
    }
}
?>