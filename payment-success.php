<?php
$reference_no = filter_var($_GET['ref'] ?? '', FILTER_SANITIZE_STRING);
$amount = (float)($_GET['amount'] ?? 0);
$method = filter_var($_GET['method'] ?? '', FILTER_SANITIZE_STRING);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-xl p-8 max-w-md w-full">
        <div class="flex items-center mb-4">
            <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <h2 class="text-2xl font-bold text-green-600">Payment Successful</h2>
        </div>
        <p class="text-gray-600 mb-4">Your payment of Rs. <?php echo number_format($amount, 2); ?> via <?php echo htmlspecialchars($method); ?> has been processed successfully.</p>
        <p class="text-gray-600 mb-6">Reference Number: <strong><?php echo htmlspecialchars($reference_no); ?></strong></p>
        <p class="text-gray-600 mb-6">You will receive an email with verified payment details soon.</p>
        <a href="proceed-to-pay.php?ref=<?php echo htmlspecialchars($reference_no); ?>" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition text-center block">Back to Payment Page</a>
    </div>
</body>
</html>