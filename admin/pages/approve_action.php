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
    $charge_type     = filter_var($_POST['charge_type'] ?? '');

    if (!$student_id || !in_array($action, ['approve', 'not_approved'])) {
        throw new Exception("Invalid input data.");
    }

    /* Validate charge_type is selected for approve action */
    if ($action === 'approve' && empty($charge_type)) {
        throw new Exception("Please select a charge type (Free or Payable).");
    }

    /* Get the real course fee from the application record */
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

    $total_amount = $app['registration_fee'] + $app['course_fee'];
    
    /* Determine due amount based on charge type */
    $due_amount = $total_amount;
    if ($action === 'approve' && $charge_type === 'free') {
        $due_amount = 2000;  // Fixed amount for free courses
    }

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

        // INSERT or UPDATE payments – use calculated due_amount
        $stmt = $conn->prepare("
            INSERT INTO payments 
                (application_id, amount, paid_amount, due_amount, status, method)
            VALUES (?, ?, 0, ?, 'pending', 'Online Payment')
            ON DUPLICATE KEY UPDATE
                amount = VALUES(amount),
                paid_amount = 0,
                due_amount = VALUES(due_amount),
                status = 'pending',
                method = 'Online Payment'
        ");
        $stmt->bind_param("idd", $application_id, $total_amount, $due_amount);
        $stmt->execute();
        $stmt->close();

        // ----- APPROVAL EMAIL (shows correct due amount based on charge type) -----
        if (!empty($gmail)) {
            $to      = $gmail;
            $subject = "Application Approved – Proceed to Payment";

            $message = "
Dear $name,

Congratulations! Your application for the course **$course_name** at **$regional_centre** has been **APPROVED**.

**Reference Number:** $reference_no  
**Total Course Fee:** Rs. " . number_format($total_amount, 2) . "
**Due Amount:** Rs. " . number_format($due_amount, 2) . "

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
       3. REJECT – delete everything + send rejection email
       ------------------------------------------------------------- */
    elseif ($action === 'not_approved') {
        $rejection_comment = filter_var($_POST['rejection_comment'] ?? '');
        
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

        // ----- REJECTION EMAIL -----
        if (!empty($gmail)) {
            $to      = $gmail;
            $subject = "Application Status – Unable to Proceed";

            $message = "
Dear $name,

Thank you for submitting your application for the course **$course_name** at **$regional_centre**.

Unfortunately, we are unable to process your application at this time.

**Reason:** $rejection_comment

If you have any questions or would like to discuss this further, please contact us:
- Email: admin@sltdigital.site
- Phone: [Contact Number]

We appreciate your interest in our institution and wish you the best of luck.

Best regards,  
Gem and Jewellery Research and Training Institute
";

            $headers  = "From: no-reply@sltdigital.site\r\n";
            $headers .= "Reply-To: no-reply@sltdigital.site\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            if (!mail($to, $subject, $message, $headers)) {
                error_log("approve_action.php – failed to send rejection email to $to");
            }
        }
    }

    /* -------------------------------------------------------------
       4. COMMIT & REDIRECT
       ------------------------------------------------------------- */
    $conn->commit();

    $_SESSION['msg'] = $action === 'approve'
        ? "Student approved – " . ($charge_type === 'free' ? "Free course (Due: Rs. 2000)" : "Due amount set to Rs. " . number_format($due_amount, 2))
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