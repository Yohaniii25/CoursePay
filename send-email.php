<?php
session_start();
require_once __DIR__ . '/classes/db.php';
require_once __DIR__ . '/classes/Mailer.php';

// If $application is not already defined, attempt to fetch details using session or GET parameters
if (!isset($application) || !is_array($application)) {
    $ref = $reference_no ?? $_SESSION['reference_no'] ?? $_GET['reference_no'] ?? null;
    $app_id = $application_id ?? $_SESSION['application_id'] ?? $_GET['application_id'] ?? null;

    if ($ref || $app_id) {
        $db = new Database();
        $conn = $db->getConnection();

        if ($ref) {
            $stmt = $conn->prepare("
                SELECT s.name, s.gmail, a.course_name, a.regional_centre, p.amount, p.transaction_id, s.reference_no
                FROM students s
                JOIN applications a ON s.id = a.student_id
                LEFT JOIN payments p ON a.id = p.application_id
                WHERE s.reference_no = ?
                ORDER BY p.id DESC LIMIT 1
            ");
            $stmt->bind_param("s", $ref);
        } else {
            $stmt = $conn->prepare("
                SELECT s.name, s.gmail, a.course_name, a.regional_centre, p.amount, p.transaction_id, s.reference_no
                FROM applications a
                JOIN students s ON s.id = a.student_id
                LEFT JOIN payments p ON a.id = p.application_id
                WHERE a.id = ?
                ORDER BY p.id DESC LIMIT 1
            ");
            $stmt->bind_param("i", $app_id);
        }

        $stmt->execute();
        $application = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}

if (empty($application)) {
    die("Error: Application details not found or missing variable \$application.");
}

$reference_no   = $reference_no ?? $application['reference_no'] ?? '';
$transaction_id = $transaction_id ?? $application['transaction_id'] ?? '';
$to             = $application['gmail'] ?? '';

if (empty($to)) {
    die("Error: Recipient email address is missing.");
}

$subject = "Payment Confirmation - Reference #$reference_no";
$message = "
<html>
<body>
    <h2>Payment Confirmation</h2>
    <p>Dear " . htmlspecialchars($application['name'] ?? 'Student') . ",</p>
    <p>Thank you for your payment. Your transaction details are below:</p>
    <ul>
        <li><strong>Reference No:</strong> " . htmlspecialchars($reference_no) . "</li>
        <li><strong>Transaction ID:</strong> " . htmlspecialchars($transaction_id) . "</li>
        <li><strong>Regional Centre:</strong> " . htmlspecialchars($application['regional_centre'] ?? '') . "</li>
        <li><strong>Course:</strong> " . htmlspecialchars($application['course_name'] ?? '') . "</li>
        <li><strong>Total Amount Paid:</strong> Rs. " . htmlspecialchars(number_format((float)($application['amount'] ?? 0), 2)) . "</li>
    </ul>
    <p>Regards,<br>Gem Institute</p>
</body>
</html>
";

Mailer::send($to, $subject, $message, true, 'info@geminstitute.lk');


