<?php
session_start();
require_once __DIR__ . '/../../classes/db.php';
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

try {
    $conn->begin_transaction();

    $student_id      = filter_var($_POST['student_id']);
    $action          = filter_var($_POST['action']);
    $gmail           = filter_var($_POST['gmail'] ?? '');
    $name            = filter_var($_POST['name'] ?? '');
    $course_name     = filter_var($_POST['course_name'] ?? '');
    $regional_centre = filter_var($_POST['regional_centre'] ?? '');
    $reference_no    = filter_var($_POST['reference_no'] ?? '');

    if (!$student_id || !in_array($action, ['approve', 'not_approved'])) {
        throw new Exception("Invalid input data.");
    }

    /* -------------------------------------------------------------
       1. GET THE REAL COURSE FEE FROM THE APPLICATION RECORD
       ------------------------------------------------------------- */
    $stmt = $conn->prepare("
        SELECT a.registration_fee, a.course_fee
        FROM applications a
        WHERE a.student_id = ?
    ");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $app = $res->fetch_assoc();
    $stmt->close();

    if (!$app) {
        throw new Exception("Application not found for student $student_id");
    }

    $total_amount = $app['registration_fee'] + $app['course_fee'];   // <-- REAL amount

    /* -------------------------------------------------------------
       2. APPROVE – set checked = 1 + create/update payment row
       ------------------------------------------------------------- */
    if ($action === 'approve') {

        // Mark student as approved
        $stmt = $conn->prepare("UPDATE students SET checked = 1 WHERE id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();

        // Get application_id
        $stmt = $conn->prepare("SELECT id FROM applications WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $appRow = $res->fetch_assoc();
        $stmt->close();
        $application_id = $appRow['id'];

        // INSERT or UPDATE payments – full due amount
        $stmt = $conn->prepare("
            INSERT INTO payments 
                (application_id, amount, paid_amount, due_amount, status, method)
            VALUES (?, ?, 0, ?, 'pending', 'Online Payment')
            ON DUPLICATE KEY UPDATE
                amount = VALUES(amount),
                paid_amount = 0,
                due_amount = VALUES(amount),
                status = 'pending',
                method = 'Online Payment'
        ");
        $stmt->bind_param("idd", $application_id, $total_amount, $total_amount);
        $stmt->execute();
        $stmt->close();

        // ----- APPROVAL EMAIL (now shows correct total) -----
        if (!empty($gmail)) {
            $to      = $gmail;
            $subject = "Application Approved – Proceed to Payment";

            $message = "
Dear $name,

Congratulations! Your application for the course **$course_name** at **$regional_centre** has been **APPROVED**.

**Reference Number:** $reference_no  
**Total Course Fee:** Rs. " . number_format($total_amount, 2) . "

Please complete the payment to confirm your enrollment:

[Pay Now →](https://sltdigital.site/gem/CoursePay/proceed-to-pay.php?ref=$reference_no)

We look forward to having you in class!

Best regards,  
Gem and Jewellery Research and Training Institute
";

            $headers  = "From: no-reply@sltdigital.site\r\n";
            $headers .= "Reply-To: no-reply@sltdigital.site\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            if (!mail($to, $subject, $message, $headers)) {
                error_log("approve_action.php – failed to send approval email to $to");
            }
        }
    }

    /* -------------------------------------------------------------
       3. REJECT – delete everything
       ------------------------------------------------------------- */
    elseif ($action === 'not_approved') {
        $stmt = $conn->prepare("
            DELETE p FROM payments p
            INNER JOIN applications a ON p.application_id = a.id
            WHERE a.student_id = ?
        ");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM applications WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();
    }

    /* -------------------------------------------------------------
       4. COMMIT & REDIRECT
       ------------------------------------------------------------- */
    $conn->commit();

    $_SESSION['msg'] = $action === 'approve'
        ? "Student approved – payment due set to Rs. " . number_format($total_amount, 2)
        : "Student rejected and removed.";

    header("Location: dashboard.php");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    error_log("approve_action.php ERROR: " . $e->getMessage());
    $_SESSION['msg'] = "Error: " . $e->getMessage();
    header("Location: dashboard.php");
    exit;
}
?>