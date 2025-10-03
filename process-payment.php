<?php
session_start();
require_once __DIR__ . '/classes/db.php';

function ipg_start($reference_no, $amount, $description) {

    $db = new Database();
    $conn = $db->getConnection();


    $stmt = $conn->prepare("
        SELECT p.amount, p.status
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
    $payment = $result->fetch_assoc();
    $stmt->close();

    if (!$payment) {
        die("Error: Invalid Reference ID.");
    }
    if ($payment['status'] !== 'pending') {
        die("Error: Payment already processed or invalid.");
    }
    if ($amount != $payment['amount']) {
        die("Error: Amount mismatch.");
    }

    $stmt = $conn->prepare("UPDATE payments p
        JOIN applications a ON p.application_id = a.id
        JOIN students s ON a.student_id = s.id
        SET p.status = 'pending'
        WHERE s.reference_no = ?");
    $stmt->bind_param("s", $reference_no);
    $stmt->execute();
    $stmt->close();
    $conn->close();


    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://test-bankofceylon.mtf.gateway.mastercard.com/api/rest/version/100/merchant/TEST700182200500/session',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode([
            "apiOperation" => "INITIATE_CHECKOUT",
            "checkoutMode" => "WEBSITE",
            "interaction" => [
                "operation" => "PURCHASE",
                "merchant" => [
                    "name" => "Gem and Jewellery",
                    "url" => "https://sltdigital.site/gem/"
                ],
                "returnUrl" => "https://sltdigital.site/gem/complete.php"
            ],
            "order" => [
                "currency" => "LKR",
                "amount" => number_format($amount, 2, '.', ''),
                "id" => $reference_no,
                "description" => $description
            ]
        ]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Basic bWVyY2hhbnQuVEVTVDcwMDE4MjIwMDUwMDpiMWUzZTE1NjU3MWNlNGFhZTRmNzMzZTVmMWY1MGYyMw=='
        ],
    ));

    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        die("cURL error: " . curl_error($curl));
    }
    curl_close($curl);

    $data = json_decode($response, true);
    if (!isset($data['session']['id'])) {
        die("Error: Failed to retrieve session ID from MPGS.");
    }

    $session_id = $data['session']['id'];
    $_SESSION['session_id'] = $session_id;
    $_SESSION['reference_no'] = $reference_no;
    $_SESSION['amount'] = $amount;


    echo '
    <html>
        <head>
            <script src="https://test-bankofceylon.mtf.gateway.mastercard.com/static/checkout/checkout.min.js" data-error="errorCallback" data-cancel="cancelCallback"></script>
            <script type="text/javascript">
                function errorCallback(error) {
                    console.log(JSON.stringify(error));
                    alert("Payment error: " + JSON.stringify(error));
                }
                function cancelCallback() {
                    console.log("Payment cancelled");
                    alert("Payment cancelled");
                }
                Checkout.configure({
                    session: {
                        id: \'' . $session_id . '\'
                    }
                });
            </script>
        </head>
        <body>
            <div id="embed-target"></div>
            <script type="text/javascript">
                window.onload = function() {
                    Checkout.showPaymentPage();
                }
            </script>
        </body>
    </html>';
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reference_no = $_POST['reference_no'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $description = "Course Payment for Reference: $reference_no";

    if (empty($reference_no) || $amount <= 0) {
        die("Error: Invalid Reference ID or Amount.");
    }

    ipg_start($reference_no, $amount, $description);
} else {
    die("Error: Invalid request method.");
}
?>