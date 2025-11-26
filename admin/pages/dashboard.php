<?php
session_start();
require_once __DIR__ . '/../../classes/db.php';

$db = new Database();
$conn = $db->getConnection();

if (isset($_POST['edit'])) {
    $student_id = (int)$_POST['student_id'];
    $next_payment_date = !empty($_POST['next_payment_date']) ? $_POST['next_payment_date'] : null;
    $due_amount = (float)$_POST['due_amount'];
    $student_id_manual = trim($_POST['student_id_manual']);

    // Update student
    $stmt = $conn->prepare("UPDATE students SET student_id_manual = ?, next_payment_date = ? WHERE id = ?");
    $stmt->bind_param("ssi", $student_id_manual, $next_payment_date, $student_id);
    $stmt->execute();
    $stmt->close();

    // Get application_id
    $stmt = $conn->prepare("SELECT id FROM applications WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $app = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $application_id = $app['id'];

    // Check if payment record exists
    $stmt = $conn->prepare("SELECT id FROM payments WHERE application_id = ?");
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $has_payment = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    if ($has_payment) {
        // Update existing payment record
        $stmt = $conn->prepare("UPDATE payments SET due_amount = ?, amount = ? WHERE application_id = ?");
        $stmt->bind_param("ddi", $due_amount, $due_amount, $application_id);
        $stmt->execute();
        $stmt->close();
    } else {
        // Create new payment record (for Tailor-Made courses)
        $stmt = $conn->prepare("
            INSERT INTO payments (application_id, amount, paid_amount, due_amount, status, method)
            VALUES (?, ?, 0, ?, 'pending', 'Admin Set')
        ");
        $stmt->bind_param("idd", $application_id, $due_amount, $due_amount);
        $stmt->execute();
        $stmt->close();
    }
    $_SESSION['msg'] = "Updated successfully!";
    header("Location: dashboard.php");
    exit;
}
if (isset($_POST['delete'])) {
    $student_id = (int)$_POST['student_id'];
    $stmt = $conn->prepare("DELETE FROM payments WHERE application_id IN (SELECT id FROM applications WHERE student_id = ?)");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->close();
    $stmt = $conn->prepare("DELETE FROM applications WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->close();
    $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['msg'] = "Student and all data deleted successfully!";
    header("Location: dashboard.php");
    exit;
}
if (isset($_POST['do_reject'])) {
    $student_id = (int)$_POST['reject_student_id'];
    $email = $_POST['reject_email'];
    $remark = nl2br(htmlspecialchars($_POST['remark']));

    // Get student name and ref for email
    $stmt = $conn->prepare("SELECT name, reference_no FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $name = $res['name'];
    $ref = $res['reference_no'];
    $stmt->close();

    // Send email
    $subject = "Application Rejected - Ref: $ref";
    $message = "
        <h3>Dear $name,</h3>
        <p>Your application has been <strong>rejected</strong>.</p>
        <p><strong>Reference No:</strong> $ref</p>
        <p><strong>Reason:</strong><br>$remark</p>
        <p>Thank you.<br>GJRTI Admissions</p>
    ";
    $headers = "From: no-reply@gjrti.lk\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    mail($email, $subject, $message, $headers);

    $conn->query("DELETE FROM students WHERE id = $student_id");

    $_SESSION['msg'] = "Application rejected & student notified.";
    header("Location: dashboard.php");
    exit;
}
// === VERIFY PAYMENT & SEND ENROLLMENT EMAIL ===
if (isset($_POST['verify_enroll'])) {
    $student_id = (int)$_POST['verify_student_id'];

    // Get student details
    $stmt = $conn->prepare("SELECT s.name, s.gmail, a.course_name, a.regional_centre, s.reference_no 
                            FROM students s 
                            JOIN applications a ON s.id = a.student_id 
                            WHERE s.id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $name = $res['name'];
    $email = $res['gmail'];
    $course = $res['course_name'];
    $centre = $res['regional_centre'];
    $ref = $res['reference_no'];

    // Mark as verified (we use checked = 2)
    $conn->query("UPDATE students SET checked = 2 WHERE id = $student_id");

    // Send enrollment email
    $subject = "Successfully Enrolled - Ref: $ref";
    $message = "
        <h2>Congratulations $name!</h2>
        <p>Your payment has been <strong>verified</strong> and you are now <strong>officially enrolled</strong> in:</p>
        <h3 style='color:#1d4ed8;'>$course</h3>
        <p><strong>Centre:</strong> $centre<br>
           <strong>Reference No:</strong> $ref</p>
        <p>Your classes will begin soon. We will contact you with the schedule.</p>
        <br>
        <p>Welcome to GJRTI Family!</p>
        <p><strong>GJRTI Admissions Team</strong></p>
    ";

    $headers  = "From: no-reply@gjrti.lk\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    mail($email, $subject, $message, $headers);

    $_SESSION['msg'] = "Payment verified & student enrolled successfully!";
    exit;
}

$sort           = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Base WHERE
$where = ["a.id IS NOT NULL"];
$params = [];
$types  = '';


// Build ORDER BY
switch ($sort) {
    case 'oldest':
        $order = "a.id ASC";
        break;
    case 'az':
        $order = "s.name ASC";
        break;
    case 'za':
        $order = "s.name DESC";
        break;
    case 'newest':
    default:
        $order = "a.id DESC";
        break;
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "
SELECT
    s.id AS student_id,
    s.name, s.gmail, s.contact_number, s.nic_file, s.reference_no,
    s.student_id_manual, s.next_payment_date, s.checked,
    a.id AS application_id, a.course_name, a.regional_centre,
    a.registration_fee, a.course_fee, a.charge_type,
    COALESCE(SUM(p.paid_amount), 0) AS total_paid,
    COALESCE(
        (SELECT due_amount FROM payments WHERE application_id = a.id ORDER BY id DESC LIMIT 1),
        (a.registration_fee + a.course_fee)
    ) AS remaining_due
FROM students s
JOIN applications a ON s.id = a.student_id
LEFT JOIN payments p ON a.id = p.application_id AND p.status = 'completed'
$where_clause
GROUP BY s.id, a.id
ORDER BY $order
";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

if (!$result) die("Query Error: " . $conn->error);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GJRTI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            height: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- [Your header - unchanged] -->
    <header class="bg-white shadow-sm sticky top-0 z-10">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <img src="../assets/GJRT_1.png" alt="Logo" class="h-16 sm:h-20">
                </div>
                <h1 class="text-xl sm:text-2xl font-bold text-purple-700">Gem Institute Admin</h1>
                <div class="flex gap-2 sm:gap-3">
                    <a href="/" class="bg-purple-600 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-purple-700 transition text-sm sm:text-base">
                        Visit Website
                    </a>
                    <a href="../logout.php" class="bg-red-500 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm sm:text-base">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </header>
    <main class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <?= htmlspecialchars($_SESSION['msg']) ?>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">Student Applications</h2>

            <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto">

                <form method="GET" class="flex flex-col sm:flex-row gap-3">

                    <select name="sort" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-purple-500 focus:border-purple-500">
                        <option value="newest" <?= (!isset($_GET['sort']) || $_GET['sort'] === 'newest') ? 'selected' : '' ?>>Newest First</option>
                        <option value="oldest" <?= (isset($_GET['sort']) && $_GET['sort'] === 'oldest') ? 'selected' : '' ?>>Oldest First</option>
                        <option value="az" <?= (isset($_GET['sort']) && $_GET['sort'] === 'az') ? 'selected' : '' ?>>Name A → Z</option>
                        <option value="za" <?= (isset($_GET['sort']) && $_GET['sort'] === 'za') ? 'selected' : '' ?>>Name Z → A</option>
                    </select>

                    <?php if (isset($_GET['course_category']) || isset($_GET['sort'])): ?>
                        <a href="dashboard.php" class="text-sm text-purple-600 hover:underline whitespace-nowrap">Clear filters</a>
                    <?php endif; ?>
                </form>

                <a href="export_csv.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition shadow-sm flex items-center gap-2 whitespace-nowrap">
                    Export CSV
                </a>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar" style="max-height: calc(100vh - 250px);">
                <table class="min-w-full divide-y divide-gray-200">
                    <!-- [Your thead - unchanged] -->
                    <thead class="bg-gradient-to-r from-purple-50 to-purple-100 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Name</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Contact</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Student ID</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ref No</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Course</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Centre</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Charge Type</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Due</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Next Pay</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">NIC</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-style text-gray-700 uppercase tracking-wider">All Payments</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Verified</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($row = $result->fetch_assoc()):
                            $display_id = $row['student_id_manual'] ?: "GJRTI" . str_pad($row['student_id'], 4, '0', STR_PAD_LEFT);
                            $first_paid = $row['total_paid'] >= ($row['registration_fee'] + ($row['course_fee'] * 0.5));
                            $second_pending = $row['remaining_due'] > 0 && $first_paid && $row['charge_type'] === 'payable';
                            $has_due = $row['remaining_due'] > 0;
                        ?>
                            <tr class="hover:bg-purple-50 transition">
                                <td class="px-4 py-4 text-sm font-medium text-gray-900 whitespace-nowrap"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap"><?= htmlspecialchars($row['gmail']) ?></td>
                                <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap"><?= htmlspecialchars($row['contact_number']) ?></td>
                                <td class="px-4 py-4 text-sm font-medium text-blue-700 whitespace-nowrap"><?= htmlspecialchars($display_id) ?></td>
                                <td class="px-4 py-4 text-sm font-medium text-purple-700 whitespace-nowrap"><?= htmlspecialchars($row['reference_no']) ?></td>
                                <td class="px-4 py-4 text-sm text-gray-600"><?= htmlspecialchars($row['course_name'] ?? '—') ?></td>
                                <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap"><?= htmlspecialchars($row['regional_centre'] ?? '—') ?></td>
                                <!-- Charge Type -->
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <?php if ($row['checked'] == 1): ?>
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full <?= $row['charge_type'] === 'free' ? 'bg-indigo-100 text-indigo-800' : 'bg-orange-100 text-orange-800' ?>">
                                            <?= $row['charge_type'] === 'free' ? 'Free (Rs. 2000)' : 'Payable' ?>
                                        </span>
                                    <?php else: ?>
                                        <form method="POST" action="set_charge_type.php" class="inline">
                                            <input type="hidden" name="application_id" value="<?= $row['application_id'] ?>">
                                            <select name="charge_type" onchange="this.form.submit()" class="text-xs rounded-md border-gray-300">
                                                <option value="" <?= !$row['charge_type'] ? 'selected' : '' ?>>--</option>
                                                <option value="payable" <?= $row['charge_type'] === 'payable' ? 'selected' : '' ?>>Payable</option>
                                                <option value="free" <?= $row['charge_type'] === 'free' ? 'selected' : '' ?>>Free (Rs. 2000)</option>
                                            </select>
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-sm font-bold whitespace-nowrap">
                                    <span class="text-red-600">Rs. <?= number_format($row['remaining_due'], 2) ?></span>
                                    <?php if ($second_pending): ?>
                                        <br><small class="text-orange-600 font-bold">2nd Due</small>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap">
                                    <?= $row['next_payment_date'] ? date('d/m/Y', strtotime($row['next_payment_date'])) : '—' ?>
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <?php if ($row['nic_file']): ?>
                                        <a href="/gem/CoursePay/<?= htmlspecialchars($row['nic_file']) ?>" download class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-3 py-1.5 rounded-md hover:bg-blue-200 text-xs">
                                            Download
                                        </a>
                                    <?php else: echo "—";
                                    endif; ?>
                                </td>
                                <!-- ALL PAYMENTS -->
                                <td class="px-4 py-4 text-center">
                                    <div class="flex flex-col gap-2">
                                        <?php
                                        $stmt2 = $conn->prepare("
                                        SELECT method, slip_file, installment_type, transaction_id, amount, created_at, status
                                        FROM payments WHERE application_id = ? ORDER BY id ASC
                                    ");
                                        $stmt2->bind_param("i", $row['application_id']);
                                        $stmt2->execute();
                                        $payments = $stmt2->get_result();
                                        $count = 0;
                                        while ($p = $payments->fetch_assoc()):
                                            $count++;
                                            if ($p['method'] === 'Upload Payslip' && $p['slip_file']): ?>
                                                <a href="/gem/CoursePay/<?= htmlspecialchars($p['slip_file']) ?>" target="_blank"
                                                    class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-3 py-1.5 rounded-md hover:bg-green-200 text-xs font-medium">
                                                    Payment <?= $count ?> (Slip)
                                                </a>
                                            <?php elseif ($p['method'] === 'Online Payment' && $p['transaction_id']): ?>
                                                <button onclick='openOnlineModal(<?= json_encode([
                                                                                        "type" => $count . " Payment (" . ucfirst($p['installment_type']) . ")",
                                                                                        "tid" => $p['transaction_id'],
                                                                                        "amount" => number_format($p['amount'], 2),
                                                                                        "date" => date("d/m/Y H:i", strtotime($p['created_at'])),
                                                                                        "status" => ucfirst($p['status'])
                                                                                    ]) ?>)'
                                                    class="bg-teal-100 text-teal-700 px-3 py-1.5 rounded-md hover:bg-teal-200 text-xs font-medium">
                                                    Payment <?= $count ?> (Online)
                                                </button>
                                            <?php endif; ?>
                                        <?php endwhile;
                                        $stmt2->close(); ?>
                                        <?php if ($count == 0): ?>
                                            <span class="text-gray-400 text-xs">No payments</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full <?= $row['remaining_due'] <= 0 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                        <?= $row['remaining_due'] <= 0 ? 'Completed' : 'Pending' ?>
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-col gap-2 min-w-40">

                                        <!-- Always show Edit & Delete -->
                                        <button onclick="openEdit(<?= $row['student_id'] ?>, '<?= $row['next_payment_date'] ?? '' ?>', <?= $row['remaining_due'] ?>, '<?= htmlspecialchars($row['student_id_manual'] ?? '') ?>', <?= $row['registration_fee'] ?>)"
                                            class="w-full min-w-36 px-3 py-2 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700 transition text-center">
                                            Edit
                                        </button>

                                        <form method="POST" class="w-full" onsubmit="return confirm('Delete this student and all data?')">
                                            <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                                            <button type="submit" name="delete"
                                                class="w-full min-w-36 px-3 py-2 bg-red-600 text-white text-xs font-medium rounded-md hover:bg-red-700 transition text-center">
                                                Delete
                                            </button>
                                        </form>

                                        <!-- If NOT approved yet (checked == 0) → show Approve/Reject -->
                                        <?php if ($row['checked'] == 0): ?>
                                            <div class="grid grid-cols-1 gap-2">
                                                <form method="POST" action="approve_action.php">
                                                    <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                                                    <input type="hidden" name="gmail" value="<?= htmlspecialchars($row['gmail']) ?>">
                                                    <input type="hidden" name="name" value="<?= htmlspecialchars($row['name']) ?>">
                                                    <input type="hidden" name="course_name" value="<?= htmlspecialchars($row['course_name']) ?>">
                                                    <input type="hidden" name="regional_centre" value="<?= htmlspecialchars($row['regional_centre']) ?>">
                                                    <input type="hidden" name="reference_no" value="<?= htmlspecialchars($row['reference_no']) ?>">
                                                    <input type="hidden" name="action" value="approve_free">
                                                    <button type="submit" class="w-full min-w-36 px-3 py-2 bg-indigo-600 text-white text-xs font-medium rounded-md hover:bg-indigo-700 text-center">
                                                        Approve FREE (Rs. 2000)
                                                    </button>
                                                </form>

                                                <form method="POST" action="approve_action.php">
                                                    <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                                                    <input type="hidden" name="gmail" value="<?= htmlspecialchars($row['gmail']) ?>">
                                                    <input type="hidden" name="name" value="<?= htmlspecialchars($row['name']) ?>">
                                                    <input type="hidden" name="course_name" value="<?= htmlspecialchars($row['course_name']) ?>">
                                                    <input type="hidden" name="regional_centre" value="<?= htmlspecialchars($row['regional_centre']) ?>">
                                                    <input type="hidden" name="reference_no" value="<?= htmlspecialchars($row['reference_no']) ?>">
                                                    <input type="hidden" name="action" value="approve_payable">
                                                    <button type="submit" class="w-full min-w-36 px-3 py-2 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700 text-center">
                                                        Approve PAYABLE
                                                    </button>
                                                </form>

                                                <button onclick="openReject('<?= $row['student_id'] ?>', '<?= htmlspecialchars($row['gmail']) ?>', '<?= htmlspecialchars($row['name']) ?>', '<?= $row['reference_no'] ?>')"
                                                    class="w-full min-w-36 px-3 py-2 bg-red-600 text-white text-xs font-medium rounded-md hover:bg-red-700 text-center">
                                                    Reject & Notify
                                                </button>
                                            </div>

                                            <!-- If approved (checked == 1) but not verified → show Approved + Reminder if needed -->
                                        <?php elseif ($row['checked'] == 1): ?>
                                            <div class="w-full min-w-36 px-3 py-2 bg-green-100 text-green-800 text-xs font-bold rounded-md text-center">
                                                Approved<br><small>(<?= $row['charge_type'] === 'free' ? 'FREE Rs. 2000' : 'Full Fee' ?>)</small>
                                            </div>

                                            <?php if ($has_due): ?>
                                                <a href="send_installment_reminder.php?ref=<?= urlencode($row['reference_no']) ?>"
                                                    class="w-full min-w-36 px-3 py-2 bg-orange-600 text-white text-xs font-bold rounded-md hover:bg-orange-700 text-center block">
                                                    Send Reminder
                                                </a>
                                            <?php endif; ?>

                                            <!-- If fully verified & enrolled (checked == 2) → show clean status -->
                                        <?php elseif ($row['checked'] == 2): ?>
                                            <div class="w-full min-w-36 px-3 py-2 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-md text-center border border-emerald-300">
                                                Fully Enrolled
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </td>
                                <!-- VERIFIED COLUMN -->
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <?php if ($row['remaining_due'] <= 0): ?>
                                        <?php $is_verified = ($row['checked'] ?? 0) == 2; ?>

                                        <?php if ($is_verified): ?>
                                            <span class="inline-block w-full min-w-32 px-3 py-1.5 bg-green-100 text-green-800 font-bold text-xs rounded-md">
                                                Verified
                                            </span>
                                        <?php else: ?>
                                            <button onclick="verifyPayment(<?= $row['student_id'] ?>, '<?= htmlspecialchars(addslashes($row['name'])) ?>', '<?= htmlspecialchars($row['gmail']) ?>', '<?= htmlspecialchars(addslashes($row['course_name'])) ?>', '<?= $row['reference_no'] ?>')"
                                                class="w-full min-w-32 bg-green-600 text-white px-3 py-1.5 rounded-md text-xs font-bold hover:bg-green-700 transition">
                                                Verify & Enroll
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">Pending Payment</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl p-6 w-96 max-w-full mx-4">
            <h3 class="text-xl font-bold mb-4 text-gray-800">Edit Student</h3>
            <form method="POST">
                <input type="hidden" name="student_id" id="edit_id">
                <input type="hidden" name="reg_fee" id="reg_fee">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Student ID (Manual)</label>
                    <input type="text" name="student_id_manual" id="edit_student_id_manual" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Next Payment Date</label>
                    <input type="date" name="next_payment_date" id="edit_date" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Full Course Fee (Rs.) <span class="text-red-600">*</span></label>
                    <input type="number" step="0.01" name="due_amount" id="edit_due" required
                        class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
                        placeholder="Enter total fee (e.g. 45000)">
                    <p class="text-xs text-gray-500 mt-1">
                        For Tailor-Made courses: Set the full agreed amount here<br>
                        For Free courses: Enter <strong>2000.00</strong>
                    </p>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="submit" name="edit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 font-medium">
                        Save Changes
                    </button>
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')"
                        class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="onlineModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl p-6 max-w-sm w-full">
            <h3 class="text-lg font-bold text-gray-800 mb-4" id="modalType">Payment Details</h3>
            <div class="space-y-3 text-sm">
                <div><strong>Transaction ID:</strong> <span id="modalTid" class="font-mono"></span></div>
                <div><strong>Amount Paid:</strong> Rs. <span id="modalAmount"></span></div>
                <div><strong>Date & Time:</strong> <span id="modalDate"></span></div>
                <div><strong>Status:</strong> <span id="modalStatus" class="px-2 py-1 rounded text-xs font-medium"></span></div>
            </div>
            <button onclick="document.getElementById('onlineModal').classList.add('hidden')"
                class="mt-6 w-full bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition">
                Close
            </button>
        </div>
    </div>

    <!--  Reject Modal -->
    <div id="rejectBox" class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full mx-4">
            <h3 class="text-lg font-bold text-red-600 mb-4">Reject Application</h3>
            <form method="POST" action="">
                <input type="hidden" name="reject_student_id" id="reject_id">
                <input type="hidden" name="reject_email" id="reject_email">

                <p class="mb-3 text-sm"><strong>Student:</strong> <span id="reject_name_display"></span></p>
                <p class="mb-4 text-sm"><strong>Ref:</strong> <span id="reject_ref_display"></span></p>

                <textarea name="remark" required placeholder="Write reason for rejection..."
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm" rows="4"></textarea>

                <div class="flex gap-3 mt-5">
                    <button type="submit" name="do_reject"
                        class="bg-red-600 text-white px-5 py-2 rounded hover:bg-red-700 text-sm">
                        Send & Delete
                    </button>
                    <button type="button" onclick="document.getElementById('rejectBox').classList.add('hidden')"
                        class="bg-gray-500 text-white px-5 py-2 rounded hover:bg-gray-600 text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function openEdit(id, date, due, sid, reg_fee) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_date').value = date;
            document.getElementById('edit_due').value = due;
            document.getElementById('edit_student_id_manual').value = sid;
            document.getElementById('reg_fee').value = reg_fee;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function openOnlineModal(data) {
            document.getElementById('modalType').textContent = data.type;
            document.getElementById('modalTid').textContent = data.tid;
            document.getElementById('modalAmount').textContent = data.amount;
            document.getElementById('modalDate').textContent = data.date;
            const s = document.getElementById('modalStatus');
            s.textContent = data.status;
            s.className = 'px-2 py-1 rounded text-xs font-medium ' + (data.status === 'Completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800');
            document.getElementById('onlineModal').classList.remove('hidden');
        }

        function openReject(id, email, name, ref) {
            document.getElementById('reject_id').value = id;
            document.getElementById('reject_email').value = email;
            document.getElementById('reject_name_display').textContent = name;
            document.getElementById('reject_ref_display').textContent = ref;
            document.getElementById('rejectBox').classList.remove('hidden');
        }
    </script>

    <script>
        function verifyPayment(id, name, email, course, ref) {
            if (!confirm(`Verify payment and enroll ${name} in "${course}"?\n\nThis will send enrollment confirmation email.`)) {
                return;
            }

            const formData = new FormData();
            formData.append('verify_student_id', id);
            formData.append('verify_enroll', '1');

            fetch('', {
                method: 'POST',
                body: formData
            }).then(() => {
                alert('Student successfully enrolled! Email sent.');
                location.reload();
            });
        }
    </script>
</body>

</html>
<?php $conn->close(); ?>