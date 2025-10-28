<?php
session_start();
require_once dirname(__FILE__) . '/classes/db.php';

$logDir = dirname(__FILE__) . '/logs/';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
    chmod($logDir, 0755);
}

error_log(date('[Y-m-d H:i:s] ') . "proceed-to-pay.php: GET data: " . json_encode($_GET, JSON_PRETTY_PRINT), 3, $logDir . 'error.log');

$db = new Database();
$conn = $db->getConnection();

$reference_no = filter_var($_GET['ref'] ?? '', FILTER_SANITIZE_STRING);
$error = $_GET['error'] ?? '';

if (empty($reference_no)) {
    die("Error: Invalid reference number.");
}

// Fetch application details
$stmt = $conn->prepare("
    SELECT s.id AS student_id, s.name, s.gmail, s.checked, a.id AS application_id, a.course_name, a.regional_centre, a.registration_fee, a.course_fee,
           p.id AS payment_id, p.amount, p.paid_amount, p.due_amount
    FROM students s
    JOIN applications a ON s.id = a.student_id
    LEFT JOIN payments p ON a.id = p.application_id
    WHERE s.reference_no = ?
");
if (!$stmt) {
    error_log(date('[Y-m-d H:i:s] ') . "Prepare failed: " . $conn->error, 3, $logDir . 'error.log');
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $reference_no);
if (!$stmt->execute()) {
    error_log(date('[Y-m-d H:i:s] ') . "Query failed: " . $stmt->error, 3, $logDir . 'error.log');
    die("Query failed: " . $stmt->error);
}
$result = $stmt->get_result();
$application = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$application) {
    error_log(date('[Y-m-d H:i:s] ') . "Invalid Reference ID: $reference_no", 3, $logDir . 'error.log');
    die("Error: Invalid Reference ID.");
}

if (!$application['checked']) {
    error_log(date('[Y-m-d H:i:s] ') . "Application not approved: $reference_no", 3, $logDir . 'error.log');
    die("Error: Your application is still pending approval. Please wait for admin approval.");
}

$total_amount = $application['registration_fee'] + $application['course_fee'];
$due_amount = $application['due_amount'] ?? $total_amount;
$paid_amount = $application['paid_amount'] ?? 0;
$course_fee = $application['course_fee'];
$course_due_amount = $due_amount - $application['registration_fee'];
if ($course_due_amount < 0) $course_due_amount = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceed to Payment - Gem and Jewellery Research and Training Institute</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md py-4 px-6 flex items-center justify-between relative">
        <div class="flex items-center">
            <img src="https://sltdigital.site/gem/wp-content/uploads/2025/06/GJRT-1.png"
                 alt="Gem and Jewellery Research and Training Institute Logo"
                 class="h-20 w-auto">
        </div>
        <h1 class="text-xl font-semibold text-indigo-800 text-center absolute left-1/2 transform -translate-x-1/2">
            Gem and Jewellery Research and Training Institute
        </h1>
        <a href="https://sltdigital.site/gem/"
           class="bg-[#25116F] text-white px-5 py-2 rounded-lg hover:opacity-90 transition">
            ← Back to Website
        </a>
    </header>

    <div class="max-w-3xl mx-auto mt-10 bg-white shadow-lg rounded-xl p-8">
        <h1 class="text-2xl font-bold mb-6 text-blue-700">Proceed to Payment</h1>

        <?php if (!empty($error)): ?>
            <div class="p-6 bg-red-50 border-l-4 border-red-500 rounded-lg mb-6">
                <p class="font-semibold text-red-700">Error</p>
                <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <div class="mb-6">
            <p class="text-gray-600">Reference Number: <strong><?php echo htmlspecialchars($reference_no); ?></strong></p>
            <p class="text-gray-600">Course: <strong><?php echo htmlspecialchars($application['course_name']); ?></strong></p>
            <p class="text-gray-600">Regional Centre: <strong><?php echo htmlspecialchars($application['regional_centre']); ?></strong></p>
            <p class="text-gray-600">Registration Fee: <strong>Rs. <?php echo number_format($application['registration_fee'], 2); ?></strong></p>
            <p class="text-gray-600">Course Fee: <strong>Rs. <?php echo number_format($course_fee, 2); ?></strong></p>
            <p class="text-gray-600">Total Amount: <strong>Rs. <?php echo number_format($total_amount, 2); ?></strong></p>
            <p class="text-gray-600">Paid Amount: <strong>Rs. <?php echo number_format($paid_amount, 2); ?></strong></p>
            <p class="text-gray-600">Due Amount: <strong>Rs. <?php echo number_format($due_amount, 2); ?></strong></p>
        </div>

        <?php if ($due_amount > 0): ?>
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Select Payment Method</h2>
            <form action="process-payment.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="reference_no" value="<?php echo htmlspecialchars($reference_no); ?>">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method <span class="text-red-500">*</span></label>
                    <select name="payment_method" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Select a method</option>
                        <option value="Online Payment">Online Payment</option>
                        <option value="Bank Slip">Bank Slip</option>
                    </select>
                </div>
                <div class="payment-options">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Option <span class="text-red-500">*</span></label>
                    <select name="payment_option" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Select an option</option>
                        <option value="full">100% Course Fee (Rs. <?php echo number_format($course_due_amount, 2); ?>)</option>
                        <?php if ($course_due_amount > 0): ?>
                            <option value="50_percent">50% Course Fee (Rs. <?php echo number_format($course_due_amount / 2, 2); ?>)</option>
                        <?php endif; ?>
                    </select>
                    <p class="text-sm text-gray-600 mt-2 fifty-percent-message hidden">
                        The 50% due amount should be paid within half the time of the course (e.g., if the course is 6 months, after three months payment should be completed).
                        Amount to be paid: <strong>Rs. <?php echo number_format($course_due_amount / 2, 2); ?></strong>
                    </p>
                </div>
                <div class="bank-slip-options hidden">
                    <label for="payment_slip" class="block text-sm font-semibold text-gray-700 mb-2 mt-4">Upload Payment Slip <span class="text-red-500">*</span></label>
                    <input type="file" name="payment_slip" accept=".jpg,.jpeg,.png,.pdf"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg">
                    Proceed with Payment
                </button>
            </form>
        <?php else: ?>
            <div class="p-6 bg-green-50 border-l-4 border-green-500 rounded-lg">
                <p class="font-semibold text-green-700">Payment Completed</p>
                <p class="text-gray-600 mt-2">No further payments are required.</p>
            </div>
        <?php endif; ?>

        <a href="index.php" class="block text-center bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 rounded-lg mt-6">
            Return to Home
        </a>
    </div>

    <footer class="bg-black text-white text-sm py-4 text-left mt-10">
        © 2025 Gem and Jewellery Research and Training Institute. All rights reserved.
    </footer>

    <script>
        const paymentMethod = document.querySelector('[name="payment_method"]');
        const paymentOptions = document.querySelector('.payment-options');
        const bankSlipOptions = document.querySelector('.bank-slip-options');
        const paymentOption = document.querySelector('[name="payment_option"]');
        const fiftyPercentMessage = document.querySelector('.fifty-percent-message');

        paymentMethod.addEventListener('change', function () {
            paymentOptions.classList.toggle('hidden', this.value === '');
            bankSlipOptions.classList.toggle('hidden', this.value !== 'Bank Slip');
            paymentOption.dispatchEvent(new Event('change')); // Trigger message visibility
        });

        paymentOption.addEventListener('change', function () {
            fiftyPercentMessage.classList.toggle('hidden', this.value !== '50_percent');
            // Require file upload for Bank Slip
            const fileInput = document.querySelector('[name="payment_slip"]');
            if (paymentMethod.value === 'Bank Slip') {
                fileInput.required = true;
            } else {
                fileInput.required = false;
            }
        });

        // Trigger initial state
        if (paymentMethod.value) {
            paymentMethod.dispatchEvent(new Event('change'));
        }
    </script>
</body>
</html>