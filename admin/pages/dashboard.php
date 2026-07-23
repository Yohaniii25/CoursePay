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

    require_once __DIR__ . '/../../classes/Mailer.php';
    Mailer::send($email, $subject, $message, true, 'no-reply@gjrti.lk');

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

    require_once __DIR__ . '/../../classes/Mailer.php';
    Mailer::send($email, $subject, $message, true, 'no-reply@gjrti.lk');

    $_SESSION['msg'] = "Payment verified & student enrolled successfully!";

    exit;
}

$sort   = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Base WHERE
$where = ["a.id IS NOT NULL"];
$params = [];
$types  = '';

if ($search !== '') {
    $where[] = "(s.name LIKE ? OR s.gmail LIKE ? OR s.contact_number LIKE ? OR s.reference_no LIKE ? OR s.student_id_manual LIKE ? OR a.course_name LIKE ? OR a.regional_centre LIKE ?)";
    $search_param = '%' . $search . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'sssssss';
}

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

// PAGINATION SETUP: 20 records per page
$records_per_page = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;

function get_page_url($page_num)
{
    $params = $_GET;
    $params['page'] = $page_num;
    return 'dashboard.php?' . http_build_query($params);
}

// Count total records
$count_sql = "
SELECT COUNT(DISTINCT s.id) AS total
FROM students s
JOIN applications a ON s.id = a.student_id
LEFT JOIN payments p ON a.id = p.application_id AND p.status = 'completed'
$where_clause
";

$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_records = (int)$count_stmt->get_result()->fetch_assoc()['total'];
$count_stmt->close();

$total_pages = max(1, ceil($total_records / $records_per_page));
if ($page > $total_pages) {
    $page = $total_pages;
}
$offset = ($page - 1) * $records_per_page;

$start_record = $total_records > 0 ? $offset + 1 : 0;
$end_record   = min($offset + $records_per_page, $total_records);

// Main SQL with LIMIT and OFFSET
$sql = "
SELECT
    s.id AS student_id,
    s.name, s.gmail, s.contact_number, s.nic_file, s.reference_no,
    s.student_id_manual, s.next_payment_date, s.checked,
    a.id AS application_id, a.course_name, a.regional_centre,
    a.registration_fee, a.course_fee, a.refundable_deposit, a.charge_type,
    COALESCE(SUM(p.paid_amount), 0) AS total_paid,
    COALESCE(
        (SELECT due_amount FROM payments WHERE application_id = a.id ORDER BY id DESC LIMIT 1),
        (a.registration_fee + a.course_fee + a.refundable_deposit)
    ) AS remaining_due
FROM students s
JOIN applications a ON s.id = a.student_id
LEFT JOIN payments p ON a.id = p.application_id AND p.status = 'completed'
$where_clause
GROUP BY s.id, a.id
ORDER BY $order
LIMIT ? OFFSET ?
";

$params_with_limit = $params;
$params_with_limit[] = $records_per_page;
$params_with_limit[] = $offset;
$types_with_limit  = $types . 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types_with_limit, ...$params_with_limit);
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
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>
    <header class="admin-header">
        <div class="admin-header-content">
            <div class="admin-logo-section">
                <img src="../assets/GJRT_1.png" alt="Logo" class="admin-logo-img">
                <span class="admin-header-title">GJRTI Admin Panel</span>
            </div>
            <div class="admin-nav-actions">
                <a href="/" class="btn-nav-website">
                    Visit Website
                </a>
                <a href="../logout.php" class="btn-nav-logout">
                    Logout
                </a>
            </div>
        </div>
    </header>
    <main class="admin-main">
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="alert-success">
                <?= htmlspecialchars($_SESSION['msg']) ?>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>
        <div class="dashboard-toolbar">
            <h2 class="dashboard-page-title">Student Applications</h2>

            <div class="toolbar-controls">
                <form method="GET" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <div style="position: relative; display: flex; align-items: center;">
                        <i class="fas fa-search" style="position: absolute; left: 0.75rem; color: #94a3b8; font-size: 0.85rem; pointer-events: none;"></i>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search name, email, ref, course..." class="sort-select" style="padding-left: 2.25rem; min-width: 240px;">
                    </div>

                    <select name="sort" onchange="this.form.submit()" class="sort-select">
                        <option value="newest" <?= ($sort === 'newest') ? 'selected' : '' ?>>Newest First</option>
                        <option value="oldest" <?= ($sort === 'oldest') ? 'selected' : '' ?>>Oldest First</option>
                        <option value="az" <?= ($sort === 'az') ? 'selected' : '' ?>>Name A → Z</option>
                        <option value="za" <?= ($sort === 'za') ? 'selected' : '' ?>>Name Z → A</option>
                    </select>

                    <button type="submit" class="btn-nav-website" style="padding: 0.5rem 1rem; border: none; cursor: pointer;">
                        Search
                    </button>

                    <?php if (!empty($search) || isset($_GET['sort']) || isset($_GET['course_category'])): ?>
                        <a href="dashboard.php" style="font-size: 0.85rem; color: #7e22ce; text-decoration: underline;">Clear filters</a>
                    <?php endif; ?>
                </form>

                <a href="export_csv.php" class="btn-export">
                    <i class="fas fa-file-csv"></i> Export CSV
                </a>
            </div>
        </div>
        <div class="table-card">
            <div class="table-scroll-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Student ID</th>
                            <th>Ref No</th>
                            <th>Course</th>
                            <th>Centre</th>
                            <th>Charge Type</th>
                            <th>Due</th>
                            <th>Next Pay</th>
                            <th>NIC</th>
                            <th>All Payments</th>
                            <th>Status</th>
                            <th style="text-align: center;">Actions</th>
                            <th style="text-align: center;">Verified</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($row = $result->fetch_assoc()):
                            $display_id = $row['student_id_manual'] ?: "GJRTI" . str_pad($row['student_id'], 4, '0', STR_PAD_LEFT);
                            $first_paid = $row['total_paid'] >= ($row['registration_fee'] + ($row['course_fee'] * 0.5) + (float)($row['refundable_deposit'] ?? 0));
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
                                        <a href="/CoursePay/<?= htmlspecialchars($row['nic_file']) ?>" download class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-3 py-1.5 rounded-md hover:bg-blue-200 text-xs">
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
                                                <a href="/CoursePay/<?= htmlspecialchars($p['slip_file']) ?>" target="_blank"
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

            <!-- PAGINATION CONTROLS -->
            <?php if ($total_records > 0): ?>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-600">
                        Showing <span class="font-semibold text-gray-900"><?= $start_record ?></span> to <span class="font-semibold text-gray-900"><?= $end_record ?></span> of <span class="font-semibold text-gray-900"><?= $total_records ?></span> records (20 per page)
                    </div>
                    <?php if ($total_pages > 1): ?>
                        <div class="inline-flex items-center space-x-1">
                            <!-- Previous Link -->
                            <?php if ($page > 1): ?>
                                <a href="<?= get_page_url($page - 1) ?>" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-purple-50 transition">
                                    &laquo; Prev
                                </a>
                            <?php else: ?>
                                <span class="px-3 py-1.5 border border-gray-200 rounded-md text-sm font-medium text-gray-400 bg-gray-100 cursor-not-allowed">
                                    &laquo; Prev
                                </span>
                            <?php endif; ?>

                            <!-- Page Numbers -->
                            <?php
                            $start_loop = max(1, $page - 2);
                            $end_loop   = min($total_pages, $page + 2);

                            if ($start_loop > 1) {
                                echo '<a href="' . get_page_url(1) . '" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-purple-50 transition">1</a>';
                                if ($start_loop > 2) {
                                    echo '<span class="px-2 py-1.5 text-sm text-gray-400">...</span>';
                                }
                            }

                            for ($i = $start_loop; $i <= $end_loop; $i++):
                                if ($i == $page): ?>
                                    <span class="px-3 py-1.5 border border-purple-600 bg-purple-600 text-white rounded-md text-sm font-semibold shadow-sm">
                                        <?= $i ?>
                                    </span>
                                <?php else: ?>
                                    <a href="<?= get_page_url($i) ?>" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-purple-50 transition">
                                        <?= $i ?>
                                    </a>
                            <?php endif;
                            endfor;

                            if ($end_loop < $total_pages) {
                                if ($end_loop < $total_pages - 1) {
                                    echo '<span class="px-2 py-1.5 text-sm text-gray-400">...</span>';
                                }
                                echo '<a href="' . get_page_url($total_pages) . '" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-purple-50 transition">' . $total_pages . '</a>';
                            }
                            ?>

                            <!-- Next Link -->
                            <?php if ($page < $total_pages): ?>
                                <a href="<?= get_page_url($page + 1) ?>" class="px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-purple-50 transition">
                                    Next &raquo;
                                </a>
                            <?php else: ?>
                                <span class="px-3 py-1.5 border border-gray-200 rounded-md text-sm font-medium text-gray-400 bg-gray-100 cursor-not-allowed">
                                    Next &raquo;
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <!-- Edit Modal -->
    <div id="editModal" class="modal-overlay hidden">
        <div class="modal-card">
            <h3 class="modal-title">Edit Student</h3>
            <form method="POST">
                <input type="hidden" name="student_id" id="edit_id">
                <input type="hidden" name="reg_fee" id="reg_fee">
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="input-label">Student ID (Manual)</label>
                    <input type="text" name="student_id_manual" id="edit_student_id_manual" class="login-input" style="padding-left: 0.75rem;">
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="input-label">Next Payment Date</label>
                    <input type="date" name="next_payment_date" id="edit_date" class="login-input" style="padding-left: 0.75rem;">
                </div>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="input-label">Full Course Fee (Rs.) <span class="required-star">*</span></label>
                    <input type="number" step="0.01" name="due_amount" id="edit_due" required
                        class="login-input" style="padding-left: 0.75rem;" placeholder="Enter total fee (e.g. 45000)">
                    <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">
                        For Tailor-Made courses: Set the full agreed amount here<br>
                        For Free courses: Enter <strong>2000.00</strong>
                    </p>
                </div>
                <div class="modal-actions">
                    <button type="submit" name="edit" class="btn-modal-save">
                        Save Changes
                    </button>
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="btn-modal-cancel">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Online Payment Details Modal -->
    <div id="onlineModal" class="modal-overlay hidden">
        <div class="modal-card">
            <h3 class="modal-title" id="modalType">Payment Details</h3>
            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.9rem;">
                <div><strong>Transaction ID:</strong> <span id="modalTid" style="font-family: monospace;"></span></div>
                <div><strong>Amount Paid:</strong> Rs. <span id="modalAmount"></span></div>
                <div><strong>Date & Time:</strong> <span id="modalDate"></span></div>
                <div><strong>Status:</strong> <span id="modalStatus" class="badge"></span></div>
            </div>
            <button onclick="document.getElementById('onlineModal').classList.add('hidden')" class="btn-modal-cancel" style="width: 100%; margin-top: 1.5rem;">
                Close
            </button>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectBox" class="modal-overlay hidden">
        <div class="modal-card">
            <h3 class="modal-title" style="color: #dc2626;">Reject Application</h3>
            <form method="POST" action="">
                <input type="hidden" name="reject_student_id" id="reject_id">
                <input type="hidden" name="reject_email" id="reject_email">

                <p style="font-size: 0.9rem; margin-bottom: 0.5rem;"><strong>Student:</strong> <span id="reject_name_display"></span></p>
                <p style="font-size: 0.9rem; margin-bottom: 1rem;"><strong>Ref:</strong> <span id="reject_ref_display"></span></p>

                <textarea name="remark" required placeholder="Write reason for rejection..." class="form-textarea" rows="4"></textarea>

                <div class="modal-actions">
                    <button type="submit" name="do_reject" class="btn-action-delete" style="padding: 0.6rem 1rem;">
                        Send & Delete
                    </button>
                    <button type="button" onclick="document.getElementById('rejectBox').classList.add('hidden')" class="btn-modal-cancel">
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