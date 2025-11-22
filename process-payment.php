<?php
session_start();
require_once __DIR__ . '/classes/db.php';

// Ensure logs directory exists
$logDir = __DIR__ . '/logs/';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
    chmod($logDir, 0755);
}

// Log session start and POST data
error_log(date('[Y-m-d H:i:s] ') . "process-payment.php: Session started, ID: " . session_id(), 3, $logDir . 'error.log');
error_log(date('[Y-m-d H:i:s] ') . "POST data: " . json_encode($_POST, JSON_PRETTY_PRINT), 3, $logDir . 'error.log');

function ipg_start($reference_no, $amount, $description)
{
    $db = new Database();
    $conn = $db->getConnection();

    // Fetch application and payment details
    $stmt = $conn->prepare("
        SELECT s.id AS student_id, s.name, s.gmail, s.checked, a.id AS application_id, a.course_name, a.regional_centre, a.registration_fee, a.course_fee,
               p.id AS payment_id, p.amount, p.paid_amount, p.due_amount, p.status
        FROM students s
        JOIN applications a ON s.id = a.student_id
        LEFT JOIN payments p ON a.id = p.application_id
        WHERE s.reference_no = ?
    ");
    if (!$stmt) {
        error_log(date('[Y-m-d H:i:s] ') . "Prepare failed: " . $conn->error, 3, $GLOBALS['logDir'] . 'error.log');
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $reference_no);
    if (!$stmt->execute()) {
        error_log(date('[Y-m-d H:i:s] ') . "Query failed: " . $stmt->error, 3, $GLOBALS['logDir'] . 'error.log');
        die("Query failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $application = $result->fetch_assoc();
    $stmt->close();

    if (!$application) {
        error_log(date('[Y-m-d H:i:s] ') . "Invalid Reference ID: $reference_no", 3, $GLOBALS['logDir'] . 'error.log');
        die("Error: Invalid Reference ID.");
    }

    if (!$application['checked']) {
        error_log(date('[Y-m-d H:i:s] ') . "Application not approved: $reference_no", 3, $GLOBALS['logDir'] . 'error.log');
        die("Error: Application not approved.");
    }

    $payment = $application['payment_id'] ? [
        'id' => $application['payment_id'],
        'amount' => $application['amount'],
        'paid_amount' => $application['paid_amount'],
        'due_amount' => $application['due_amount'],
        'status' => $application['status']
    ] : null;

    if ($payment && $payment['status'] !== 'pending') {
        error_log(date('[Y-m-d H:i:s] ') . "Payment already processed: status={$payment['status']}", 3, $GLOBALS['logDir'] . 'error.log');
        die("Error: Payment already processed or invalid.");
    }

    // Validate amount against due_amount
    $total_amount = $application['registration_fee'] + $application['course_fee'];
    $due_amount = $payment ? $payment['due_amount'] : $total_amount;
    error_log(date('[Y-m-d H:i:s] ') . "ipg_start: total_amount=$total_amount, due_amount=$due_amount, amount=$amount", 3, $GLOBALS['logDir'] . 'error.log');
    if ($amount <= 0 || $amount > $due_amount + 0.01) {
        error_log(date('[Y-m-d H:i:s] ') . "Invalid amount: $amount, Due: $due_amount", 3, $GLOBALS['logDir'] . 'error.log');
        die("Error: Invalid amount.");
    }

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
                "returnUrl" => "https://sltdigital.site/gem/CoursePay/complete.php"
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
        error_log(date('[Y-m-d H:i:s] ') . "cURL error: " . curl_error($curl), 3, $GLOBALS['logDir'] . 'error.log');
        die("cURL error: " . curl_error($curl));
    }
    curl_close($curl);

    $data = json_decode($response, true);
    error_log(date('[Y-m-d H:i:s] ') . "MPGS INITIATE_CHECKOUT Response: " . json_encode($data), 3, $GLOBALS['logDir'] . 'error.log');
    if (!isset($data['session']['id']) || !isset($data['successIndicator'])) {
        error_log(date('[Y-m-d H:i:s] ') . "Failed to retrieve session ID or successIndicator: " . json_encode($data), 3, $GLOBALS['logDir'] . 'error.log');
        die("Error: Failed to retrieve session ID or successIndicator from MPGS.");
    }

    $session_id = $data['session']['id'];
    $success_indicator = $data['successIndicator'];
    $_SESSION['session_id'] = $session_id;
    $_SESSION['reference_no'] = $reference_no;
    $_SESSION['amount'] = $amount;
    $_SESSION['success_indicator'] = $success_indicator;

    // Log session data
    error_log(date('[Y-m-d H:i:s] ') . "Session data set: " . json_encode($_SESSION), 3, $GLOBALS['logDir'] . 'error.log');

    // Ensure session is saved
    session_write_close();

    echo '
    <html>
        <head>
            <script src="https://test-bankofceylon.mtf.gateway.mastercard.com/static/checkout/checkout.min.js" data-error="errorCallback" data-cancel="cancelCallback"></script>
            <script type="text/javascript">
                function errorCallback(error) {
                    console.log(JSON.stringify(error));
                    alert("Payment error: " + JSON.stringify(error));
                    window.location.href = "proceed-to-pay.php?ref=' . urlencode($reference_no) . '&error=Payment%20failed";
                }
                function cancelCallback() {
                    console.log("Payment cancelled");
                    alert("Payment cancelled");
                    window.location.href = "proceed-to-pay.php?ref=' . urlencode($reference_no) . '&error=Payment%20cancelled";
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

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log(date('[Y-m-d H:i:s] ') . "Invalid request method: " . $_SERVER['REQUEST_METHOD'], 3, $logDir . 'error.log');
    die("Error: Invalid request method.");
}

$reference_no = filter_var($_POST['reference_no'] ?? '');
$payment_method = filter_var($_POST['payment_method'] ?? '');
$payment_option = filter_var($_POST['payment_option'] ?? '');

if (empty($reference_no) || empty($payment_method) || empty($payment_option)) {
    error_log(date('[Y-m-d H:i:s] ') . "Missing required fields: reference_no=$reference_no, payment_method=$payment_method, payment_option=$payment_option", 3, $logDir . 'error.log');
    header("Location: proceed-to-pay.php?ref=" . urlencode($reference_no) . "&error=Missing%20required%20fields");
    exit;
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

if (!$application) {
    error_log(date('[Y-m-d H:i:s] ') . "Invalid Reference ID: $reference_no", 3, $logDir . 'error.log');
    header("Location: proceed-to-pay.php?ref=" . urlencode($reference_no) . "&error=Invalid%20Reference%20ID");
    exit;
}

if (!$application['checked']) {
    error_log(date('[Y-m-d H:i:s] ') . "Application not approved: $reference_no", 3, $logDir . 'error.log');
    header("Location: proceed-to-pay.php?ref=" . urlencode($reference_no) . "&error=Application%20not%20approved");
    exit;
}

$total_amount = $application['registration_fee'] + $application['course_fee'];
$payment = $application['payment_id'] ? [
    'id' => $application['payment_id'],
    'amount' => $application['amount'],
    'paid_amount' => $application['paid_amount'],
    'due_amount' => $application['due_amount']
] : null;

try {
    if (!$conn->begin_transaction()) {
        throw new Exception("Failed to start transaction: " . $conn->error);
    }

    // Initialize payment record if not exists
    if (!$payment) {
        $stmt = $conn->prepare("
            INSERT INTO payments (application_id, amount, paid_amount, due_amount, status)
            VALUES (?, ?, 0, ?, 'pending')
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("idd", $application['application_id'], $total_amount, $total_amount);
        if (!$stmt->execute()) {
            throw new Exception("Failed to create payment record: " . $stmt->error);
        }
        $payment_id = $conn->insert_id;
        $payment = ['id' => $payment_id, 'amount' => $total_amount, 'paid_amount' => 0, 'due_amount' => $total_amount];
        $stmt->close();
    }

    // Calculate amount based on payment_option
    $due_amount = $payment['due_amount'];
    $amount_to_pay = $payment_option === '50_percent' ? $due_amount / 2 : $due_amount;
    error_log(date('[Y-m-d H:i:s] ') . "Payment calculation: due_amount=$due_amount, payment_option=$payment_option, amount_to_pay=$amount_to_pay", 3, $logDir . 'error.log');

    if ($amount_to_pay <= 0 || $amount_to_pay > $due_amount + 0.01) {
        throw new Exception("Invalid payment amount: $amount_to_pay, Due: $due_amount");
    }

    // Store payment option and amount for email and complete.php
    $_SESSION['payment_option'] = $payment_option;
    $_SESSION['amount_to_pay'] = $amount_to_pay;
    $remaining_amount = $payment_option === '50_percent' ? $due_amount / 2 : 0;

    if ($payment_method === 'Online Payment') {
        // Generate unique transaction ID
        $transaction_id = 'TXN-' . $reference_no . '-' . time() . '-' . uniqid();
        
        // Store payment details in session
        $_SESSION['reference_no'] = $reference_no;
        $_SESSION['amount'] = $amount_to_pay;
        $_SESSION['name'] = $application['name'];
        $_SESSION['gmail'] = $application['gmail'];
        $_SESSION['course_name'] = $application['course_name'];
        $_SESSION['regional_centre'] = $application['regional_centre'];
        $_SESSION['transaction_id'] = $transaction_id;
        error_log(date('[Y-m-d H:i:s] ') . "Online Payment: amount_to_pay=$amount_to_pay, transaction_id=$transaction_id stored in session", 3, $logDir . 'error.log');
        
        // Update payment record with transaction_id
        $stmt = $conn->prepare("UPDATE payments SET transaction_id = ? WHERE id = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("si", $transaction_id, $payment['id']);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update transaction_id: " . $stmt->error);
        }
        $stmt->close();

        if (!$conn->commit()) {
            throw new Exception("Failed to commit transaction: " . $conn->error);
        }
        $conn->close();

        ipg_start($reference_no, $amount_to_pay, "Payment for Reference: $reference_no");
        exit;
    } elseif ($payment_method === 'Bank Slip') {
        if (empty($_FILES['payment_slip']['name'])) {
            throw new Exception("Bank slip is required.");
        }

        $uploadDir = __DIR__ . "/Uploads/slips/";
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new Exception("Failed to create upload directory: $uploadDir");
            }
        }
        $filename = time() . "_" . basename($_FILES['payment_slip']['name']);
        $slip_file_path = "Uploads/slips/" . $filename;
        $fileType = strtolower(pathinfo($slip_file_path, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Invalid file type. Allowed types: JPG, JPEG, PNG, PDF.");
        }
        if (!move_uploaded_file($_FILES['payment_slip']['tmp_name'], __DIR__ . '/' . $slip_file_path)) {
            throw new Exception("Failed to upload bank slip.");
        }

        // Generate unique transaction ID for bank slip
        $transaction_id = 'SLIP-' . $reference_no . '-' . time() . '-' . uniqid();
        
        // Update payment record
        $new_paid_amount = $payment['paid_amount'] + $amount_to_pay;
        $new_due_amount = $payment['amount'] - $new_paid_amount;
        $new_status = $new_due_amount <= 0 ? 'completed' : 'pending';
        error_log(date('[Y-m-d H:i:s] ') . "Bank Slip: updating payment - paid_amount=$new_paid_amount, due_amount=$new_due_amount, status=$new_status, transaction_id=$transaction_id", 3, $logDir . 'error.log');

        $stmt = $conn->prepare("
            UPDATE payments
            SET paid_amount = ?, due_amount = ?, status = ?, method = ?, slip_file = ?, transaction_id = ?
            WHERE id = ?
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $method = 'Bank Slip';
        $stmt->bind_param("ddssssi", $new_paid_amount, $new_due_amount, $new_status, $method, $slip_file_path, $transaction_id, $payment['id']);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update payment record: " . $stmt->error);
        }
        $stmt->close();

        // Send emails
        $to = $application['gmail'];
        $subject = "Payment Receipt - Bank Slip";
        $message = "
Dear {$application['name']},

Your bank slip for the course '{$application['course_name']}' at {$application['regional_centre']} has been received.

Reference Number: $reference_no
Transaction ID: $transaction_id
Amount Paid: Rs. " . number_format($amount_to_pay, 2) . "
Remaining Balance: Rs. " . number_format($new_due_amount, 2) . "
Payment Method: Bank Slip
";
        if ($payment_option === '50_percent') {
            $message .= "
The remaining 50% (Rs. " . number_format($remaining_amount, 2) . ") should be paid within half the time of the course (e.g., if the course is 6 months, after three months payment should be completed).
";
        }
        $message .= "
Our Team will inform you about further details.

Best regards,
Gem and Jewellery Research and Training Institute
";
        $headers = "From: no-reply@sltdigital.site\r\n";
        $headers .= "Reply-To: no-reply@sltdigital.site\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        if (!mail($to, $subject, $message, $headers)) {
            error_log(date('[Y-m-d H:i:s] ') . "Failed to send slip payment receipt email to $to", 3, $logDir . 'error.log');
        }

        $admin_email = 'sutharshankanna04@gmail.com';
        $admin_subject = "Bank Slip Payment Received Notification";
        $admin_message = "
Dear Admin,

A bank slip payment has been received with the following details:

Student Name: {$application['name']}
Reference Number: $reference_no
Transaction ID: $transaction_id
Course: {$application['course_name']}
Regional Centre: {$application['regional_centre']}
Amount Paid: Rs. " . number_format($amount_to_pay, 2) . "
Remaining Balance: Rs. " . number_format($new_due_amount, 2) . "
Payment Method: Bank Slip
";
        if ($payment_option === '50_percent') {
            $admin_message .= "
The remaining 50% (Rs. " . number_format($remaining_amount, 2) . ") should be paid within half the time of the course (e.g., if the course is 6 months, after three months payment should be completed).
";
        }
        $admin_message .= "
Please verify the payment in the system.

Best regards,
Gem and Jewellery Research and Training Institute
";
        $admin_headers = "From: no-reply@sltdigital.site\r\n";
        $admin_headers .= "Reply-To: no-reply@sltdigital.site\r\n";
        $admin_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        if (!mail($admin_email, $admin_subject, $admin_message, $admin_headers)) {
            error_log(date('[Y-m-d H:i:s] ') . "Failed to send slip payment notification email to $admin_email", 3, $logDir . 'error.log');
        }

        if (!$conn->commit()) {
            throw new Exception("Failed to commit transaction: " . $conn->error);
        }
        $conn->close();

        header("Location: payment-success.php?ref=$reference_no");
        exit;
    } else {
        throw new Exception("Invalid payment method: $payment_method");
    }
} catch (Exception $e) {
    $conn->rollback();
    $conn->close();
    error_log(date('[Y-m-d H:i:s] ') . "Error in process-payment.php: " . $e->getMessage(), 3, $logDir . 'error.log');
    header("Location: proceed-to-pay.php?ref=" . urlencode($reference_no) . "&error=" . urlencode($e->getMessage()));
    exit;
}
