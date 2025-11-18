<?php
$reference_no = $_GET['ref'] ?? '';
if (empty($reference_no)) {
    die("Error: Invalid reference number.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Gem and Jewellery Research and Training Institute</title>
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
        <h1 class="text-2xl font-bold mb-6 text-blue-700">Payment Successful</h1>
        <div class="p-6 bg-green-50 border-l-4 border-green-500 rounded-lg mb-6">
            <p class="font-semibold text-green-700">Payment Received</p>
            <p class="text-gray-600 mt-2">Your bank slip payment for Reference No: <strong><?php echo htmlspecialchars($reference_no); ?></strong> has been successfully received.</p>
            <p class="text-gray-600 mt-2">You will receive an email with payment details soon.</p>
        </div>
        <a href="index.php" class="block text-center bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 rounded-lg mt-6">
            Return to Home
        </a>
    </div>

    <footer class="bg-black text-white text-sm py-4 text-left mt-10">
        © 2025 Gem and Jewellery Research and Training Institute. All rights reserved.
    </footer>
</body>
</html>