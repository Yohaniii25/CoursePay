<?php
session_start();
require_once dirname(__FILE__) . '/classes/db.php';
$db = new Database();
$conn = $db->getConnection();

$reference_no = trim($_GET['ref'] ?? '');
if (empty($reference_no)) die("Invalid reference.");

$stmt = $conn->prepare("
    SELECT s.name, a.course_name, a.regional_centre,
           COALESCE((SELECT due_amount FROM payments WHERE application_id = a.id ORDER BY id DESC LIMIT 1), 0) AS remaining_due
    FROM students s
    JOIN applications a ON s.id = a.student_id
    WHERE s.reference_no = ?
");
$stmt->bind_param("s", $reference_no);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conn->close();

if (!$app || $app['remaining_due'] <= 0) {
    echo "<div style='text-align:center; padding:50px; font-family:Arial;'>
            <h1 style='color:green;'>Payment Completed!</h1>
            <p>Thank you! Your full course fee has been received.</p>
            <a href='https://sltdigital.site/gem/' style='color:#6B46C1;'>Back to Website</a>
          </div>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Second Installment - GJRTI</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; padding: 20px; }
        .container { max-width: 500px; margin: 40px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); text-align: center; }
        .logo { height: 80px; margin-bottom: 20px; }
        h1 { color: #6B46C1; margin-bottom: 20px; }
        .due { font-size: 36px; font-weight: bold; color: #E53E3E; margin: 20px 0; }
        select, input[type="file"], button { width: 100%; padding: 14px; margin: 12px 0; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; }
        button { background: #6B46C1; color: white; font-weight: bold; cursor: pointer; }
        button:hover { background: #553C9A; }
        .slip-upload { display: none; }
        a { color: #6B46C1; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://sltdigital.site/gem/wp-content/uploads/2025/06/GJRT-1.png" alt="Logo" class="logo">
        <h1>Second Installment Due</h1>
        <p><strong>Reference:</strong> <?= htmlspecialchars($reference_no) ?></p>
        <p><strong>Student:</strong> <?= htmlspecialchars($app['name']) ?></p>
        <p><strong>Course:</strong> <?= htmlspecialchars($app['course_name']) ?></p>

        <div class="due">Rs. <?= number_format($app['remaining_due'], 2) ?></div>

        <form action="process-payment.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="reference_no" value="<?= htmlspecialchars($reference_no) ?>">
            <input type="hidden" name="payment_option" value="full">

            <select name="payment_method" required onchange="toggleSlip(this.value)">
                <option value="">Choose Payment Method</option>
                <option value="Online Payment">Pay Online (Card / Bank)</option>
                <option value="Bank Slip">Upload Bank Slip</option>
            </select>

            <div class="slip-upload" id="slipUpload">
                <input type="file" name="payment_slip" accept="image/*,.pdf">
            </div>

            <button type="submit">Pay Now → Rs. <?= number_format($app['remaining_due'], 2) ?></button>
        </form>

        <br>
        <a href="https://sltdigital.site/gem/">Back to Website</a>
    </div>

    <script>
        function toggleSlip(value) {
            const slipDiv = document.getElementById('slipUpload');
            const fileInput = slipDiv.querySelector('input');
            if (value === 'Bank Slip') {
                slipDiv.style.display = 'block';
                fileInput.required = true;
            } else {
                slipDiv.style.display = 'none';
                fileInput.required = false;
            }
        }
    </script>
</body>
</html>