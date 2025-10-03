<?php
session_start();
require_once __DIR__ . '/classes/db.php';

$db = new Database();
$conn = $db->getConnection();


if (!isset($_SESSION['reference_no']) || empty($_SESSION['reference_no'])) {
    $reference_no = ''; 
} else {
    $reference_no = $_SESSION['reference_no'];
}


$application = null;
if (!empty($reference_no)) {
    $stmt = $conn->prepare("
        SELECT s.reference_no, s.name, a.regional_centre, a.course_name, a.registration_fee, a.course_fee, p.amount
        FROM students s
        JOIN applications a ON s.id = a.student_id
        JOIN payments p ON a.id = p.application_id
        WHERE s.reference_no = ?
    ");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $reference_no);
    if (!$stmt->execute()) {
        die("Query failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $application = $result->fetch_assoc();
    $stmt->close();
}


unset($_SESSION['reference_no']);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceed to Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen py-12 px-4">
    <div class="max-w-3xl mx-auto">
        
  
        <div class="bg-white shadow-lg rounded-xl p-8 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-3xl font-bold text-gray-800">Proceed to Payment</h1>
                <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
            </div>

            <?php if ($application): ?>
      
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 p-6 rounded-lg mb-6">
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-2">Your Reference Number</p>
                    <p class="text-3xl font-bold text-blue-600 tracking-wider"><?php echo htmlspecialchars($application['reference_no']); ?></p>
                    <p class="mt-3 text-sm text-gray-600">Please save this reference number for your records and use it to track your payment.</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        How to Pay
                    </h2>
                    <ol class="space-y-3">
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">1</span>
                            <span class="text-gray-700 pt-0.5"><strong>Enter Reference ID:</strong> Input your Reference ID in the field below (pre-filled if provided).</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">2</span>
                            <span class="text-gray-700 pt-0.5"><strong>Submit Form:</strong> Click the "Proceed to Payment" button to continue.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3">3</span>
                            <span class="text-gray-700 pt-0.5"><strong>Complete Payment:</strong> Follow the instructions on the payment page to finalize your transaction.</span>
                        </li>
                    </ol>
                </div>

  
                <div class="border border-gray-200 rounded-lg overflow-hidden mb-6">
                    <div class="bg-gradient-to-r from-gray-100 to-gray-50 px-6 py-3 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800">Application Details</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <div class="px-6 py-4 flex justify-between items-center hover:bg-gray-50 transition">
                            <span class="text-sm font-semibold text-gray-600">Name</span>
                            <span class="text-sm text-gray-800 font-medium"><?php echo htmlspecialchars($application['name']); ?></span>
                        </div>
                        <div class="px-6 py-4 flex justify-between items-center hover:bg-gray-50 transition">
                            <span class="text-sm font-semibold text-gray-600">Regional Centre</span>
                            <span class="text-sm text-gray-800 font-medium"><?php echo htmlspecialchars($application['regional_centre']); ?></span>
                        </div>
                        <div class="px-6 py-4 flex justify-between items-center hover:bg-gray-50 transition">
                            <span class="text-sm font-semibold text-gray-600">Course</span>
                            <span class="text-sm text-gray-800 font-medium"><?php echo htmlspecialchars($application['course_name']); ?></span>
                        </div>
                        <div class="px-6 py-4 flex justify-between items-center hover:bg-gray-50 transition">
                            <span class="text-sm font-semibold text-gray-600">Registration Fee</span>
                            <span class="text-sm text-gray-800 font-medium">Rs. <?php echo number_format($application['registration_fee'], 2); ?></span>
                        </div>
                        <div class="px-6 py-4 flex justify-between items-center hover:bg-gray-50 transition">
                            <span class="text-sm font-semibold text-gray-600">Course Fee</span>
                            <span class="text-sm text-gray-800 font-medium">Rs. <?php echo number_format($application['course_fee'], 2); ?></span>
                        </div>
                        <div class="px-6 py-4 flex justify-between items-center bg-blue-50">
                            <span class="text-base font-bold text-gray-800">Total Amount</span>
                            <span class="text-xl font-bold text-blue-600">Rs. <?php echo number_format($application['amount'], 2); ?></span>
                        </div>
                    </div>
                </div>

            <?php else: ?>
   
                <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-lg mb-6">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-red-800">Application Not Found</p>
                            <p class="text-sm text-red-700 mt-1">Please enter a valid Reference ID below to proceed.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form action="process-payment.php" method="POST" class="space-y-6">
                <div>
                    <label for="reference_no" class="block text-sm font-semibold text-gray-700 mb-2">
                        Reference ID <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="reference_no" name="reference_no" 
                           value="<?php echo htmlspecialchars($reference_no); ?>"
                           required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 outline-none text-lg font-mono tracking-wider"
                           placeholder="Enter your reference number">
                </div>
                
                <?php if ($application): ?>
                    <input type="hidden" name="amount" value="<?php echo $application['amount']; ?>">
                <?php endif; ?>
                
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold py-4 px-6 rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 transform transition duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-lg flex items-center justify-center space-x-2">
                    <span>Proceed to Payment</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </button>
            </form>
        </div>


        <div class="text-center text-sm text-gray-600">
            <p>Need help? Contact our support team for assistance.</p>
        </div>
    </div>
</body>
</html>