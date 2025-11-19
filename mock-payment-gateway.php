<?php
require_once __DIR__ . '/classes/db.php';

$db = new Database();
$conn = $db->getConnection();

$reference_no = filter_var($_GET['ref'] ?? '');
$amount = (float)($_GET['amount'] ?? 0);
$success = filter_var($_GET['success'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $success === 'true') {
    try {
        if (!$conn->begin_transaction()) {
            throw new Exception("Failed to start transaction: " . $conn->error);
        }

        // Validate slip upload
        if (empty($_FILES['payment_slip']['name'])) {
            throw new Exception("Bank slip is required.");
        }
        $uploadDir = __DIR__ . "/uploads/slips/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename = time() . "_" . basename($_FILES['payment_slip']['name']);
        $slip_file_path = "uploads/slips/" . $filename;
        $fileType = strtolower(pathinfo($slip_file_path, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Invalid file type. Allowed types: JPG, JPEG, PNG, PDF.");
        }
        if (!move_uploaded_file($_FILES['payment_slip']['tmp_name'], __DIR__ . '/' . $slip_file_path)) {
            throw new Exception("Failed to upload bank slip.");
        }

        // Fetch payment and student details
        $stmt = $conn->prepare("
            SELECT p.id, p.amount, p.paid_amount, p.due_amount,
                   s.name, s.gmail, a.course_name, a.regional_centre
            FROM payments p
            JOIN applications a ON p.application_id = a.id
            JOIN students s ON a.student_id = s.id
            WHERE s.reference_no = ?
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $reference_no);
        if (!$stmt->execute()) {
            throw new Exception("Query failed: " . $stmt->error);
        }
        $result = $stmt->get_result();
        $payment = $result->fetch_assoc();
        $stmt->close();

        if (!$payment) {
            // Create new payment record
            $stmt = $conn->prepare("
                SELECT a.id, a.registration_fee, a.course_fee
                FROM applications a
                JOIN students s ON a.student_id = s.id
                WHERE s.reference_no = ?
            ");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("s", $reference_no);
            $stmt->execute();
            $result = $stmt->get_result();
            $app = $result->fetch_assoc();
            $stmt->close();

            $total_amount = $app['registration_fee'] + $app['course_fee'];
            $stmt = $conn->prepare("
                INSERT INTO payments (application_id, amount, paid_amount, due_amount, status, method)
                VALUES (?, ?, 0, ?, 'pending', 'Online Payment')
            ");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("idd", $app['id'], $total_amount, $total_amount);
            if (!$stmt->execute()) {
                throw new Exception("Failed to create payment record: " . $stmt->error);
            }
            $payment_id = $conn->insert_id;
            $payment = [
                'id' => $payment_id,
                'amount' => $total_amount,
                'paid_amount' => 0,
                'due_amount' => $total_amount,
                'name' => $app['name'] ?? 'Unknown',
                'gmail' => $app['gmail'] ?? '',
                'course_name' => $app['course_name'] ?? '',
                'regional_centre' => $app['regional_centre'] ?? ''
            ];
            $stmt->close();
        }

        // Validate payment amount
        if ($amount > $payment['due_amount']) {
            throw new Exception("Payment amount exceeds due amount.");
        }

        // Update payment record
        $new_paid_amount = $payment['paid_amount'] + $amount;
        $new_due_amount = $payment['amount'] - $new_paid_amount;
        $new_status = $new_due_amount <= 0 ? 'completed' : 'pending';
        $method = 'Online Payment';

        $stmt = $conn->prepare("
            UPDATE payments
            SET paid_amount = ?, due_amount = ?, status = ?, method = ?, slip_file = ?
            WHERE id = ?
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ddsssi", $new_paid_amount, $new_due_amount, $new_status, $method, $slip_file_path, $payment['id']);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update payment record: " . $stmt->error);
        }
        $stmt->close();

        // Send email to student
        $to = $payment['gmail'];
        $subject = "Payment Confirmation";
        $message = "
Dear {$payment['name']},

Your payment for the course '{$payment['course_name']}' at {$payment['regional_centre']} has been successfully processed.

Reference Number: $reference_no
Amount Paid: Rs. " . number_format($amount, 2) . "
Remaining Balance: Rs. " . number_format($new_due_amount, 2) . "
Payment Method: Online Payment

You will receive an email with verified payment details soon.

Best regards,
Gem and Jewellery Research and Training Institute
";
        $headers = "From: no-reply@sltdigital.site\r\n";
        $headers .= "Reply-To: no-reply@sltdigital.site\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        if (!mail($to, $subject, $message, $headers)) {
            error_log("Failed to send payment confirmation email to $to");
        }

        // Send email to admin
        $admin_email = 'yohanii725@gmail.com';
        $admin_subject = "Payment Received Notification";
        $admin_message = "
Dear Admin,

A payment has been received with the following details:

Student Name: {$payment['name']}
Reference Number: $reference_no
Course: {$payment['course_name']}
Regional Centre: {$payment['regional_centre']}
Amount Paid: Rs. " . number_format($amount, 2) . "
Remaining Balance: Rs. " . number_format($new_due_amount, 2) . "
Payment Method: Online Payment

Please verify the payment in the system.

Best regards,
Gem and Jewellery Research and Training Institute
";
        $admin_headers = "From: no-reply@sltdigital.site\r\n";
        $admin_headers .= "Reply-To: no-reply@sltdigital.site\r\n";
        $admin_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        if (!mail($admin_email, $admin_subject, $admin_message, $admin_headers)) {
            error_log("Failed to send payment notification email to $admin_email");
        }

        if (!$conn->commit()) {
            throw new Exception("Failed to commit transaction: " . $conn->error);
        }
        $conn->close();

        // Redirect to success page
        header("Location: payment-success.php?ref=$reference_no&amount=$amount&method=Online%20Payment");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();
        error_log("Error in mock-payment-gateway.php: " . $e->getMessage());
        die("Error: " . htmlspecialchars($e->getMessage()));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mock Payment Gateway</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-xl p-8 max-w-md w-full">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Mock Payment Gateway</h1>
        <p class="text-gray-600 mb-4">Amount: Rs. <?php echo number_format($amount, 2); ?></p>
        <p class="text-gray-600 mb-6">Reference Number: <?php echo htmlspecialchars($reference_no); ?></p>
        <?php if ($success !== 'true'): ?>
            <div class="flex gap-4 mb-4">
                <a href="mock-payment-gateway.php?ref=<?php echo htmlspecialchars($reference_no); ?>&amount=<?php echo $amount; ?>&success=true" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">Simulate Success</a>
                <a href="proceed-to-pay.php?ref=<?php echo htmlspecialchars($reference_no); ?>" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">Cancel</a>
            </div>
            <div id="slipDownload" class="mb-4">
                <button onclick="generateSlip()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Download Payment Slip</button>
            </div>
        <?php else: ?>
            <form action="mock-payment-gateway.php?ref=<?php echo htmlspecialchars($reference_no); ?>&amount=<?php echo $amount; ?>&success=true" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label for="payment_slip" class="block text-sm font-semibold text-gray-700 mb-2">Upload Payment Slip:</label>
                    <input type="file" id="payment_slip" name="payment_slip" accept=".jpg,.jpeg,.png,.pdf" required class="block w-full border border-gray-300 rounded-lg p-2">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition">Submit Payment Slip</button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        function generateSlip() {
            const slipContent = `
                Payment Slip
                ------------
                Reference Number: <?php echo htmlspecialchars($reference_no); ?>
                Amount: Rs. <?php echo number_format($amount, 2); ?>
                Date: <?php echo date('Y-m-d H:i:s'); ?>
                Recipient: Gem and Jewellery Research and Training Institute
                ------------
                Please upload this slip after payment.
            `;
            const blob = new Blob([slipContent], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'payment-slip-<?php echo htmlspecialchars($reference_no); ?>.txt';
            a.click();
            window.URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>