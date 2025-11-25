<?php
session_start();
require_once dirname(__FILE__) . '/classes/db.php';

$logDir = dirname(__FILE__) . '/logs/';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
    chmod($logDir, 0755);
}

$db = new Database();
$conn = $db->getConnection();

$reference_no = trim($_GET['ref'] ?? '');
$error = $_GET['error'] ?? '';

if (empty($reference_no)) {
    die("Error: Invalid reference number.");
}

// Fetch application + total paid so far
$stmt = $conn->prepare("
    SELECT 
        s.name, s.gmail, s.checked,
        a.id AS application_id,
        a.course_name, a.regional_centre,
        a.registration_fee, a.course_fee, a.charge_type,
        COALESCE(SUM(p.paid_amount), 0) AS total_paid
    FROM students s
    JOIN applications a ON s.id = a.student_id
    LEFT JOIN payments p ON a.id = p.application_id AND p.status = 'completed'
    WHERE s.reference_no = ?
    GROUP BY s.id, a.id
");
$stmt->bind_param("s", $reference_no);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$app) {
    die("Error: Invalid Reference ID.");
}
if (!$app['checked']) {
    die("Error: Your application is still pending approval. Please wait for admin approval.");
}

$total_required = $app['charge_type'] === 'free'
    ? $app['registration_fee']
    : $app['registration_fee'] + $app['course_fee'];

$already_paid = (float)$app['total_paid'];
$due_amount   = $total_required - $already_paid;

// Determine what has been paid
$first_installment_amount = $app['registration_fee'] + ($app['course_fee'] * 0.5);
$first_installment_paid = $already_paid >= $first_installment_amount - 0.01; // tolerance

$courseName = trim($app['course_name']);
$fullPaymentOnlyCourses = [
    "Gem-A Foundation Course",
    "Gem-A Diploma Course",
    "Gem Related Certificate in Tailor – Made Courses",
    "Jewellery Certificate in Tailor – Made Courses",
    "Certificate in Geuda Heat Treatment"
];
$isFullOnlyCourse = in_array($courseName, $fullPaymentOnlyCourses);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceed to Payment - GJRTI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-md py-4 px-6 flex items-center justify-between relative">
        <div class="flex items-center">
            <img src="https://sltdigital.site/gem/wp-content/uploads/2025/06/GJRT-1.png" alt="Logo" class="h-20 w-auto">
        </div>
        <h1 class="text-xl font-semibold text-indigo-800 text-center absolute left-1/2 transform -translate-x-1/2">
            Gem and Jewellery Research and Training Institute
        </h1>
        <a href="https://sltdigital.site/gem/" class="bg-[#25116F] text-white px-5 py-2 rounded-lg hover:opacity-90 transition">
            ← Back to Website
        </a>
    </header>

    <div class="max-w-3xl mx-auto mt-10 bg-white shadow-lg rounded-xl p-8">
        <h1 class="text-2xl font-bold mb-6 text-blue-700">Proceed to Payment</h1>

        <?php if (!empty($error)): ?>
            <div class="p-6 bg-red-50 border-l-4 border-red-500 rounded-lg mb-6">
                <p class="font-semibold text-red-700">Error</p>
                <p class="text-gray-600 mt-2"><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <div class="mb-6 space-y-2">
            <p><strong>Reference No:</strong> <?= htmlspecialchars($reference_no) ?></p>
            <p><strong>Student:</strong> <?= htmlspecialchars($app['name']) ?></p>
            <p><strong>Course:</strong> <?= htmlspecialchars($app['course_name']) ?></p>
            <p><strong>Centre:</strong> <?= htmlspecialchars($app['regional_centre']) ?></p>
            <p><strong>Registration Fee:</strong> Rs. <?= number_format($app['registration_fee'], 2) ?></p>
            <p><strong>Course Fee:</strong> Rs. <?= number_format($app['course_fee'], 2) ?></p>
            <p><strong>Total Required:</strong> Rs. <?= number_format($total_required, 2) ?></p>
            <p class="text-green-600 font-bold"><strong>Already Paid:</strong> Rs. <?= number_format($already_paid, 2) ?></p>
            <p class="text-red-600 font-bold"><strong>Due Amount:</strong> Rs. <?= number_format($due_amount, 2) ?></p>
        </div>

        <?php if ($due_amount > 0): ?>
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Select Payment Method</h2>
            <form action="process-payment.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="reference_no" value="<?= htmlspecialchars($reference_no) ?>">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method <span class="text-red-500">*</span></label>
                    <select name="payment_method" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Select method</option>
                        <option value="Online Payment">Online Payment (Card / Bank)</option>
                        <option value="Bank Slip">Bank Slip Upload</option>
                    </select>
                </div>

                <!-- PAYMENT OPTIONS — SMART LOGIC -->
                <div class="payment-options">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Option <span class="text-red-500">*</span></label>
                    <select name="payment_option" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Select option</option>

                        <?php if ($first_installment_paid || $isFullOnlyCourse): ?>
                            <!-- Only show 100% if first installment already done OR course doesn't allow installments -->
                            <option value="full">
                                Pay Full Remaining Amount – Rs. <?= number_format($due_amount, 2) ?>
                            </option>
                        <?php else: ?>
                            <!-- First time visitor → show both options -->
                            <option value="full">
                                Pay Full Amount Now – Rs. <?= number_format($due_amount, 2) ?>
                            </option>
                            <option value="50_percent">
                                Pay 50% Now (Reg Fee + 50% Course Fee) – Rs. <?= number_format($app['registration_fee'] + ($app['course_fee'] * 0.5), 2) ?>
                            </option>
                        <?php endif; ?>
                    </select>

                    <?php if (!$first_installment_paid && !$isFullOnlyCourse): ?>
                        <p class="text-sm text-gray-600 mt-3 bg-amber-50 p-3 rounded-lg">
                            <strong>50% Payment Plan:</strong><br>
                            • Full Registration Fee + 50% of Course Fee<br>
                            • Remaining 50% Course Fee must be paid within half the course duration<br>
                            <strong>Amount now:</strong> Rs. <?= number_format($app['registration_fee'] + ($app['course_fee'] * 0.5), 2) ?><br>
                            <strong>Remaining later:</strong> Rs. <?= number_format($app['course_fee'] * 0.5, 2) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="bank-slip-options hidden">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 mt-4">
                        Upload Payment Slip <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="payment_slip" accept=".jpg,.jpeg,.png,.pdf" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-lg text-lg">
                    Proceed to Payment →
                </button>
            </form>

        <?php else: ?>
            <div class="p-8 bg-green-50 border-l-4 border-green-500 rounded-lg text-center">
                <h3 class="text-2xl font-bold text-green-700 mb-3">Payment Completed!</h3>
                <p class="text-gray-700">Thank you! Your full course fee has been received.</p>
                <p class="mt-4">You will receive confirmation via email shortly.</p>
            </div>
        <?php endif; ?>

        <a href="index.php" class="block text-center mt-8 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 rounded-lg">
            ← Return to Home
        </a>
    </div>

    <footer class="bg-black text-white text-sm py-6 text-center mt-16">
        © 2025 Gem and Jewellery Research and Training Institute. All rights reserved.
    </footer>

    <script>
        const methodSelect = document.querySelector('[name="payment_method"]');
        const optionDiv = document.querySelector('.payment-options');
        const slipDiv = document.querySelector('.bank-slip-options');
        const optionSelect = document.querySelector('[name="payment_option"]');

        methodSelect?.addEventListener('change', function() {
            const isBank = this.value === 'Bank Slip';
            slipDiv.classList.toggle('hidden', !isBank);
            optionDiv.classList.toggle('hidden', this.value === '');
            if (isBank) document.querySelector('[name="payment_slip"]').required = true;
        });

        // Trigger on load if already selected
        if (methodSelect.value) methodSelect.dispatchEvent(new Event('change'));
    </script>
</body>
</html>