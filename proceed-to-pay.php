<?php
session_start();
require_once dirname(__FILE__) . '/classes/db.php';
$db = new Database();
$conn = $db->getConnection();

$reference_no = trim($_GET['ref'] ?? '');
if (empty($reference_no)) die("Invalid reference.");

// GET APPLICATION + FEES + PAYMENT STATUS
$stmt = $conn->prepare("
    SELECT 
        s.name, s.checked,
        a.id AS application_id, a.course_name, a.regional_centre, a.charge_type,
        a.registration_fee, a.course_fee,
        COALESCE(p.due_amount, (a.registration_fee + a.course_fee)) AS amount_to_pay,
        COALESCE(SUM(p2.paid_amount), 0) AS total_paid
    FROM students s
    JOIN applications a ON s.id = a.student_id
    LEFT JOIN payments p ON a.id = p.application_id 
        AND p.id = (SELECT MAX(id) FROM payments WHERE application_id = a.id)
    LEFT JOIN payments p2 ON a.id = p2.application_id AND p2.status = 'completed'
    WHERE s.reference_no = ?
    GROUP BY s.id, a.id
");
$stmt->bind_param("s", $reference_no);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$app || !$app['checked']) die("Application not approved.");

$total_paid = (float)$app['total_paid'];
$reg_fee = (float)$app['registration_fee'];
$course_fee = (float)$app['course_fee'];
$amount_to_pay = (float)$app['amount_to_pay'];
$course_name = $app['course_name'];
$charge_type = $app['charge_type'] ?? 'payable';

// Redirect if already paid
if ($total_paid > 0) {
    header("Location: second-installment.php?ref=" . urlencode($reference_no));
    exit;
}

// COURSES THAT DO NOT ALLOW 50% (FULL PAYMENT ONLY)
$noFiftyPercentCourses = [
    "Certificate in Geuda Heat Treatment",
    "Jewellery Certificate in Tailor – Made Courses",
    "Gem Related Certificate in Tailor – Made Courses",
    "Gem-A Foundation Course",
    "Gem-A Diploma Course"
];

$allowFiftyPercent = !in_array($course_name, $noFiftyPercentCourses);
$is_free_course = $charge_type === 'free';

// Calculate 50% amount = Reg Fee + 50% Course Fee
$fifty_percent_amount = $reg_fee + ($course_fee * 0.5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceed to Payment - GJRTI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-12 px-4">
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-2xl p-10">
        <div class="text-center mb-8">
            <img src="https://sltdigital.site/gem/wp-content/uploads/2025/06/GJRT-1.png" alt="Logo" class="h-20 mx-auto mb-4">
            <h1 class="text-4xl font-bold text-purple-700">Proceed to Payment</h1>
        </div>

        <div class="bg-gray-50 rounded-xl p-6 mb-8 text-lg">
            <p><strong>Reference No:</strong> <span class="text-purple-700 font-bold"><?= htmlspecialchars($reference_no) ?></span></p>
            <p><strong>Student:</strong> <?= htmlspecialchars($app['name']) ?></p>
            <p><strong>Course:</strong> <?= htmlspecialchars($course_name) ?></p>
            <p><strong>Centre:</strong> <?= htmlspecialchars($app['regional_centre']) ?></p>

            <div class="text-3xl font-bold mt-6 text-center <?= $is_free_course ? 'text-indigo-600' : 'text-red-600' ?>">
                Amount Due: Rs. <?= number_format($amount_to_pay, 2) ?>
                <?php if ($is_free_course): ?>
                    <br><small class="text-indigo-600 font-bold">(FREE Course - Registration Fee Only)</small>
                <?php endif; ?>
            </div>
        </div>

        <form action="process-payment.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="reference_no" value="<?= htmlspecialchars($reference_no) ?>">

            <div>
                <label class="block text-lg font-semibold text-gray-700 mb-2">Payment Method</label>
                <select name="payment_method" required class="w-full p-4 border-2 border-gray-300 rounded-lg text-lg focus:border-purple-500 focus:ring-purple-500">
                    <option value="">Select method</option>
                    <option value="Online Payment">Online Payment (Card / Bank)</option>
                    <option value="Bank Slip">Bank Slip Upload</option>
                </select>
            </div>

            <?php if ($allowFiftyPercent && !$is_free_course): ?>
                <div>
                    <label class="block text-lg font-semibold text-gray-700 mb-2">Payment Option</label>
                    <select name="payment_option" required class="w-full p-4 border-2 border-gray-300 rounded-lg text-lg focus:border-purple-500 focus:ring-purple-500">
                        <option value="">Select option</option>
                        <option value="50_percent">
                            Pay 50% Now – Rs. <?= number_format($fifty_percent_amount, 2) ?>
                            <small class="block text-gray-500">(Reg Fee + 50% Course Fee)</small>
                        </option>
                        <option value="full">Pay Full Amount – Rs. <?= number_format($amount_to_pay, 2) ?></option>
                    </select>
                    <p class="text-sm text-gray-600 mt-3 bg-amber-50 p-4 rounded-lg">
                        <strong>50% Plan:</strong> Pay Registration Fee + 50% of Course Fee now.<br>
                        Remaining 50% due within half course duration.
                    </p>
                </div>
            <?php else: ?>
                <input type="hidden" name="payment_option" value="full">
                <?php if (!$is_free_course): ?>
                    <div class="bg-orange-50 border-2 border-orange-300 rounded-lg p-6 text-center">
                        <p class="text-xl font-bold text-orange-800">Full Payment Required</p>
                        <p class="text-lg">This course does not allow 50% payment option.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="bank-slip-options hidden">
                <label class="block text-lg font-semibold text-gray-700 mb-2">Upload Bank Slip</label>
                <input type="file" name="payment_slip" accept="image/*,.pdf" class="w-full p-4 border-2 border-dashed border-gray-400 rounded-lg">
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-bold text-2xl py-6 rounded-xl shadow-lg">
                <?= $is_free_course ? 'Pay Rs. 2000 Now' : 'Pay Now' ?>
            </button>
        </form>

        <div class="text-center mt-10">
            <a href="https://sltdigital.site/gem/" class="text-purple-600 hover:underline text-lg font-medium">
                ← Back to Website
            </a>
        </div>
    </div>

    <script>
        document.querySelector('[name="payment_method"]')?.addEventListener('change', function() {
            const isSlip = this.value === 'Bank Slip';
            document.querySelector('.bank-slip-options').classList.toggle('hidden', !isSlip);
            document.querySelector('[name="payment_slip"]').required = isSlip;
        });
    </script>
</body>
</html>