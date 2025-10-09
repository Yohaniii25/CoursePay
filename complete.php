<?php
session_start();
require_once __DIR__ . '/classes/db.php';


if (!isset($_SESSION['session_id']) || !isset($_SESSION['reference_no']) || !isset($_SESSION['amount'])) {
    die("Error: Invalid session data.");
}

$session_id = $_SESSION['session_id'];
$reference_no = $_SESSION['reference_no'];
$amount = $_SESSION['amount'];


$db = new Database();
$conn = $db->getConnection();


$stmt = $conn->prepare("
    SELECT s.name, s.contact_number, s.address, s.gmail, a.regional_centre, a.course_name, a.registration_fee, a.course_fee, p.amount, p.status
    FROM students s
    JOIN applications a ON s.id = a.student_id
    JOIN payments p ON a.id = p.application_id
    WHERE s.reference_no = ?
");
$stmt->bind_param("s", $reference_no);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();
$stmt->close();

if (!$application) {
    $conn->close();
    die("Error: Application not found.");
}

if ($application['status'] !== 'pending') {
    $conn->close();
    die("Error: Payment already processed or invalid.");
}


$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://test-bankofceylon.mtf.gateway.mastercard.com/api/rest/version/100/merchant/TEST700182200500/order/$reference_no",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Basic bWVyY2hhbnQuVEVTVDcwMDE4MjIwMDUwMDpiMWUzZTE1NjU3MWNlNGFhZTRmNzMzZTVmMWY1MGYyMw=='
    ],
));
$response = curl_exec($curl);
if (curl_errno($curl)) {
    $conn->close();
    die("cURL error: " . curl_error($curl));
}
curl_close($curl);

$data = json_decode($response, true);
$payment_status = $data['result'] ?? 'ERROR';
$transaction_status = $data['order']['status'] ?? 'UNKNOWN';
$transaction_id = $data['transaction'][0]['id'] ?? null; 


if ($payment_status === 'SUCCESS' && $transaction_status === 'APPROVED' && $transaction_id) {
    $stmt = $conn->prepare("UPDATE payments p
        JOIN applications a ON p.application_id = a.id
        JOIN students s ON a.student_id = s.id
        SET p.status = 'completed', p.transaction_id = ?
        WHERE s.reference_no = ?");
    $stmt->bind_param("ss", $transaction_id, $reference_no);
    $stmt->execute();
    $stmt->close();
} else {
    $stmt = $conn->prepare("UPDATE payments p
        JOIN applications a ON p.application_id = a.id
        JOIN students s ON a.student_id = s.id
        SET p.status = 'failed'
        WHERE s.reference_no = ?");
    $stmt->bind_param("s", $reference_no);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    die("Error: Payment not successful. Status: $transaction_status");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Success</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-3xl mx-auto mt-10 bg-white shadow-lg rounded-xl p-8">
        <h1 class="text-2xl font-bold mb-6">Payment Successful</h1>
        <p class="text-lg font-semibold">Thank you for your payment!</p>
        <div class="mt-4 space-y-2">
            <p><strong>Reference Number:</strong> <?php echo htmlspecialchars($reference_no); ?></p>
            <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($transaction_id ?: 'N/A'); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($application['name']); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($application['contact_number'] ?: 'N/A'); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($application['gmail'] ?: 'N/A'); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($application['address'] ?: 'N/A'); ?></p>
            <p><strong>Regional Centre:</strong> <?php echo htmlspecialchars($application['regional_centre']); ?></p>
            <p><strong>Course:</strong> <?php echo htmlspecialchars($application['course_name']); ?></p>
            <p><strong>Registration Fee:</strong> Rs. <?php echo number_format($application['registration_fee'], 2); ?></p>
            <p><strong>Course Fee:</strong> Rs. <?php echo number_format($application['course_fee'], 2); ?></p>
            <p><strong>Total Amount Paid:</strong> Rs. <?php echo number_format($application['amount'], 2); ?></p>
        </div>
        <form action="generate-receipt.php" method="POST" class="mt-6">
            <input type="hidden" name="reference_no" value="<?php echo htmlspecialchars($reference_no); ?>">
            <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($transaction_id ?: ''); ?>">
            <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">
                Download Receipt (PDF)
            </button>
        </form>

        <!-- email send that payment successful -->
         <form action="send-email.php" method="POST" class="mt-6">
            <input type="hidden" name="reference_no" value="<?php echo htmlspecialchars($reference_no); ?>">
            <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($transaction_id ?: ''); ?>">
            <button type="submit" class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700">
                Send Payment Confirmation Email
            </button>
            </form>
    </div>
</body>
</html>