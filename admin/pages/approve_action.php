<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../../classes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['action']) || !in_array($_POST['action'], ['approve_free', 'approve_payable'])) {
    header("Location: dashboard.php");
    exit;
}

try {
    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        throw new Exception("Database connection failed.");
    }

    $conn->begin_transaction();

    $student_id = (int)($_POST['student_id'] ?? 0);
    $gmail = trim($_POST['gmail'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $course_name = trim($_POST['course_name'] ?? '');
    $regional_centre = trim($_POST['regional_centre'] ?? '');
    $reference_no = trim($_POST['reference_no'] ?? '');
    $action = $_POST['action'];

    if (!$student_id) {
        throw new Exception("Invalid student ID.");
    }

    // Get application
    $stmt = $conn->prepare("SELECT id, course_type FROM applications WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $app = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$app) throw new Exception("Application not found.");
    $application_id = $app['id'];

    // DETERMINE DUE AMOUNT
    if ($action === 'approve_free') {
        $is_diploma = ($app['course_type'] ?? '') === 'Diploma Level Courses';
        $due_amount = $is_diploma ? 5000.00 : 2000.00;
        $charge_type = 'free';
        $email_msg = "FREE course approved – Registration Fee: Rs. " . number_format($due_amount, 2) . " only";
    } else {
        // FOR PAYABLE: USE THE LATEST due_amount FROM PAYMENTS (ADMIN SET)
        $stmt = $conn->prepare("
            SELECT due_amount 
            FROM payments 
            WHERE application_id = ? 
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->bind_param("i", $application_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $payment = $result->fetch_assoc();
        $stmt->close();

        if ($payment && $payment['due_amount'] > 0) {
            $due_amount = (float)$payment['due_amount'];
        } else {
            // Fallback: reg + course fee + refundable deposit (for normal courses)
            $stmt = $conn->prepare("SELECT registration_fee + course_fee + refundable_deposit AS total FROM applications WHERE id = ?");
            $stmt->bind_param("i", $application_id);
            $stmt->execute();
            $fallback = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            $due_amount = $fallback ? (float)$fallback['total'] : 0;
        }

        $charge_type = 'payable';
        $email_msg = "Application approved – Full Course Fee: Rs. " . number_format($due_amount, 2);
    }

    // Update charge_type
    $stmt = $conn->prepare("UPDATE applications SET charge_type = ? WHERE id = ?");
    $stmt->bind_param("si", $charge_type, $application_id);
    $stmt->execute();
    $stmt->close();

    // Mark student as approved
    $stmt = $conn->prepare("UPDATE students SET checked = 1 WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->close();

    // Create/Update payment record
    $stmt = $conn->prepare("
        INSERT INTO payments 
            (application_id, amount, paid_amount, due_amount, status, method)
        VALUES (?, ?, 0, ?, 'pending', 'Online Payment')
        ON DUPLICATE KEY UPDATE
            amount = VALUES(amount),
            paid_amount = 0,
            due_amount = VALUES(due_amount),
            status = 'pending'
    ");
    $stmt->bind_param("idd", $application_id, $due_amount, $due_amount);
    $stmt->execute();
    $stmt->close();

    // Commit database transaction first so approval is saved
    $conn->commit();

    // SEND ATTRACTIVE HTML EMAIL SAFELY
    if (!empty($gmail)) {
        $to = $gmail;
        $subject = $action === 'approve_free' ? "Application Approved: FREE Course - $course_name" : "Application Approved: Proceed to Payment - $course_name";
        
        try {
            if (file_exists(__DIR__ . '/../../classes/Mailer.php')) {
                require_once __DIR__ . '/../../classes/Mailer.php';
                if (class_exists('Mailer')) {
                    $html = Mailer::buildTemplate(
                        $action === 'approve_free' ? "Application Approved (FREE Course)" : "Application Approved – Proceed to Payment",
                        "Approved",
                        "green",
                        "Dear " . htmlspecialchars($name) . ",",
                        $action === 'approve_free'
                            ? "Congratulations! Your course application has been approved under the FREE scholarship category. Only the registration fee is required to finalize your admission."
                            : "Congratulations! Your course application has been approved by the admissions committee. Please proceed to complete your fee payment to secure your seat.",
                        [
                            'Reference Number' => "<strong style='color:#25116F; font-size:16px;'>$reference_no</strong>",
                            'Course'           => $course_name,
                            'Regional Centre'  => $regional_centre,
                            'Amount Payable'   => "<strong style='color:#16a34a; font-size:16px;'>Rs. " . number_format($due_amount, 2) . "</strong>"
                        ],
                        "Proceed to Payment",
                        "https://www.gjrti.gov.lk/CoursePay/proceed-to-pay.php?ref=" . urlencode($reference_no),
                        "You can pay conveniently online via Card/Internet Banking or upload a scanned copy/photo of your bank deposit slip."
                    );
                    Mailer::send($to, $subject, $html, true, 'no-reply@gjrti.gov.lk');
                }
            }
        } catch (Throwable $mailEx) {
            error_log("approve_action.php Mail Error: " . $mailEx->getMessage());
        }
    }

    $_SESSION['msg'] = $action === 'approve_free'
        ? "Approved as FREE – Due: Rs. " . number_format($due_amount, 2)
        : "Approved as PAYABLE – Due: Rs. " . number_format($due_amount, 2);

    header("Location: dashboard.php");
    exit;
} catch (Throwable $e) {
    if (isset($conn) && $conn instanceof mysqli && $conn->ping()) {
        @$conn->rollback();
    }
    error_log("approve_action.php Error: " . $e->getMessage());
    $_SESSION['msg'] = "Error: " . $e->getMessage();
    header("Location: dashboard.php");
    exit;
}
