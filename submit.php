<?php
session_start();
require_once __DIR__ . '/classes/db.php';

$db = new Database();
$conn = $db->getConnection();

function generateReferenceNo($length = 8): string
{
    return strtoupper(substr(md5(uniqid(rand(), true)), 0, $length));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        if (!$conn->begin_transaction()) {
            throw new Exception("Failed to start transaction: " . $conn->error);
        }

        $requiredFields = ['name', 'nic_passport', 'regional_centre', 'course_type', 'course', 'reg_fee', 'course_fee'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Please fill all required fields.");
            }
        }

        if (empty($_FILES['nic_file']['name'])) {
            throw new Exception("NIC/Passport copy must be uploaded.");
        }

        // Generate Reference Number
        $referenceNo = generateReferenceNo();

        $uploadDir = __DIR__ . "/uploads/nic/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename = time() . "_" . basename($_FILES['nic_file']['name']);
        $nicFilePath = "uploads/nic/" . $filename;
        $fileType = strtolower(pathinfo($nicFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];

        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Invalid file type. Allowed types: JPG, JPEG, PNG, PDF.");
        }

        if (!move_uploaded_file($_FILES['nic_file']['tmp_name'], __DIR__ . '/' . $nicFilePath)) {
            throw new Exception("Failed to upload NIC/Passport file.");
        }

        $stmt = $conn->prepare("
            INSERT INTO students (reference_no, name, nic_passport, contact_number, address, gmail, education_background, declaration, nic_file)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $name = filter_var($_POST['name']);
        $nic_passport = filter_var($_POST['nic_passport']);
        $contact_number = filter_var($_POST['contact_number'] ?? '');
        $address = filter_var($_POST['address'] ?? '');
        $gmail = filter_var($_POST['gmail'] ?? '');
        $education_background = filter_var($_POST['education_background'] ?? '');
        $declaration = isset($_POST['declaration']) ? 1 : 0;

        $stmt->bind_param(
            "sssssssis",
            $referenceNo,
            $name,
            $nic_passport,
            $contact_number,
            $address,
            $gmail,
            $education_background,
            $declaration,
            $nicFilePath
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert student record: " . $stmt->error);
        }

        $studentId = $conn->insert_id;
        $stmt->close();

        // Insert into applications table
        $stmt = $conn->prepare("
            INSERT INTO applications (student_id, regional_centre, course_type, course_name, registration_fee, course_fee)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $regional_centre = filter_var($_POST['regional_centre']);
        $course_type = filter_var($_POST['course_type']);
        $course_name = filter_var($_POST['course'] );
        $registration_fee = (float)$_POST['reg_fee'];
        $course_fee = (float)$_POST['course_fee'];

        $stmt->bind_param("isssdd", $studentId, $regional_centre, $course_type, $course_name, $registration_fee, $course_fee);

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert application record: " . $stmt->error);
        }

        $applicationId = $conn->insert_id;
        $stmt->close();

        // Insert into payments table
        $totalAmount = $registration_fee + $course_fee;
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
            throw new Exception("Failed to insert payment record: " . $stmt->error);
        }
        $stmt->close();

        // Store reference number in session
        $_SESSION['reference_no'] = $referenceNo;

        if (!empty($gmail)) {
            $to = $gmail;
            $subject = "Application Submission Confirmation";
            $message = "
Dear $name,

Thank you for submitting your application to the Gem and Jewellery Research and Training Institute.

Reference Number: $referenceNo
Course: $course_name
Regional Centre: $regional_centre
Total Fee: Rs. " . number_format($totalAmount, 2) . "

We will review your application and contact you shortly.

Best regards,
Gem and Jewellery Research and Training Institute
";
            $headers = "From: no-reply@sltdigital.site\r\n";
            $headers .= "Reply-To: no-reply@sltdigital.site\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            if (!mail($to, $subject, $message, $headers)) {
                error_log("Failed to send confirmation email to $to");
            }
        }

        // Send notification email to admin
        $admin_email = 'yohanii725@gmail.com';
        $admin_subject = "New Student Application Received";
        $admin_message = "
Dear Admin,

A new student application has been received with the following details:

Student Name: $name
Reference Number: $referenceNo
Course: $course_name
Regional Centre: $regional_centre
Total Fee: Rs. " . number_format($totalAmount, 2) . "
Contact Number: $contact_number
NIC/Passport: $nic_passport
Address: $address
Email: $gmail
Education Background: $education_background

Please review the application in the system.

Best regards,
Gem and Jewellery Research and Training Institute
";
        $admin_headers = "From: no-reply@sltdigital.site\r\n";
        $admin_headers .= "Reply-To: no-reply@sltdigital.site\r\n";
        $admin_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (!mail($admin_email, $admin_subject, $admin_message, $admin_headers)) {
            error_log("Failed to send notification email to $admin_email");
        }

        // Commit transaction
        if (!$conn->commit()) {
            throw new Exception("Failed to commit transaction: " . $conn->error);
        }

        $conn->close();

        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Application Submitted</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-100 flex items-center justify-center min-h-screen">
            <div class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center">
                <div class="bg-white rounded-lg p-8 max-w-md w-full shadow-lg">
                    <h2 class="text-2xl font-bold text-green-600 mb-4">Application Submitted Successfully</h2>
                    <p class="text-lg text-gray-700 mb-4">
                        Your Reference Number: <strong><?php echo htmlspecialchars($referenceNo); ?></strong>
                    </p>
                    <p class="text-gray-600 mb-6">
                        You will receive a confirmation email with your application details.
                    </p>
                    <button 
                        onclick="window.location.href='index.php'" 
                        class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition">
                        OK
                    </button>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        die("Error: " . htmlspecialchars($e->getMessage()));
    }
} else {
    die("Invalid request method.");
}
?>