<?php
session_start();
require_once __DIR__ . '/../../classes/db.php';
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit'])) {
        $student_id = (int)$_POST['student_id'];
        $next_payment_date = !empty($_POST['next_payment_date']) ? $_POST['next_payment_date'] : null;
        $due_amount = (float)$_POST['due_amount'];
        $student_id_manual = trim($_POST['student_id_manual']);

        $stmt = $conn->prepare("UPDATE students SET student_id_manual = ?, next_payment_date = ? WHERE id = ?");
        $stmt->bind_param("ssi", $student_id_manual, $next_payment_date, $student_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE payments p JOIN applications a ON p.application_id = a.id SET p.due_amount = ? WHERE a.student_id = ?");
        $stmt->bind_param("di", $due_amount, $student_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['msg'] = "Updated successfully!";
        header("Location: dashboard.php");
        exit;
    }

    if (isset($_POST['delete'])) {
        $student_id = (int)$_POST['student_id'];
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['msg'] = "Student deleted!";
        header("Location: dashboard.php");
        exit;
    }
}

// FIXED QUERY — includes slip_file and installment_type
$sql = "
SELECT
    s.id AS student_id,
    s.name,
    s.gmail,
    s.contact_number,
    s.nic_file,
    s.reference_no,
    s.student_id_manual,
    s.next_payment_date,
    s.checked,
    a.id AS application_id,
    a.course_name,
    a.regional_centre,
    a.registration_fee,
    a.course_fee,
    a.charge_type,
    p.amount AS total_billed,
    COALESCE(p.paid_amount, 0) AS paid_amount,
    COALESCE(p.due_amount, 0) AS due_amount,
    p.status AS payment_status,
    p.slip_file,
    p.installment_type
FROM students s
LEFT JOIN applications a ON s.id = a.student_id
LEFT JOIN payments p ON a.id = p.application_id
WHERE a.id IS NOT NULL
ORDER BY a.id DESC
";

$result = $conn->query($sql);
if (!$result) {
    die("Query Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        <!-- Success Message -->
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <?= htmlspecialchars($_SESSION['msg']) ?>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">Student Applications</h2>
            <a href="export_csv.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition shadow-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Export CSV
            </a>
        </div>

        <!-- Scrollable Table Container -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar" style="max-height: calc(100vh - 250px);">
                <table class="min-w-full divide-y divide-gray-200">
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
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Slip</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php
                            $display_id = $row['student_id_manual'] ?: "GJRTI" . str_pad($row['student_id'], 4, '0', STR_PAD_LEFT);
                            $first_paid = $row['paid_amount'] >= ($row['registration_fee'] + ($row['course_fee'] * 0.5));
                            $second_pending = $row['due_amount'] > 0 && $first_paid && $row['charge_type'] === 'payable';
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
                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full 
                                            <?= $row['charge_type'] === 'free' ? 'bg-indigo-100 text-indigo-800' : 'bg-orange-100 text-orange-800' ?>">
                                            <?= $row['charge_type'] === 'free' ? 'Free (Registration Fee Only)' : 'Payable (Full)' ?>
                                        </span>
                                    <?php else: ?>
                                        <form method="POST" action="set_charge_type.php" class="inline">
                                            <input type="hidden" name="application_id" value="<?= $row['application_id'] ?>">
                                            <select name="charge_type" onchange="this.form.submit()"
                                                class="text-xs rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                                <option value="" <?= !$row['charge_type'] ? 'selected' : '' ?>>-- Select --</option>
                                                <option value="payable" <?= $row['charge_type'] === 'payable' ? 'selected' : '' ?>>Payable (Full)</option>
                                                <option value="free" <?= $row['charge_type'] === 'free' ? 'selected' : '' ?>>Free (Reg Only)</option>
                                            </select>
                                        </form>
                                    <?php endif; ?>
                                </td>

                                <!-- Due Amount -->
                                <td class="px-4 py-4 text-sm font-bold whitespace-nowrap">
                                    <?php
                                    if ($row['charge_type'] === 'free') {
                                        $due = max(0, $row['registration_fee'] - $row['paid_amount']);
                                        echo '<span class="text-indigo-700">Rs. ' . number_format($due, 2) . '<br><small class="font-normal text-indigo-600">(Reg Fee Only)</small></span>';
                                    } else {
                                        $full = $row['registration_fee'] + $row['course_fee'];
                                        $due = max(0, $full - $row['paid_amount']);
                                        echo '<span class="text-red-600">Rs. ' . number_format($due, 2) . '</span>';
                                        if ($second_pending) {
                                            echo '<br><small class="font-normal text-orange-600 font-bold">2nd Installment Due</small>';
                                        }
                                    }
                                    ?>
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap">
                                    <?= $row['next_payment_date'] ? date('d/m/Y', strtotime($row['next_payment_date'])) : '—' ?>
                                </td>

                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <?php if ($row['nic_file']): ?>
                                        <a href="/coursePay/<?= htmlspecialchars($row['nic_file']) ?>" download
                                            class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-3 py-1.5 rounded-md hover:bg-blue-200 transition text-xs font-medium">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                            Download
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>

                                <!-- SLIPS or ONLINE PAYMENT DETAILS — SMART COLUMN -->
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <div class="flex flex-col gap-2">

                                        <?php if ($row['method'] === 'Upload Payslip' || $row['method'] == ''): ?>
                                            <!-- Traditional Slip Uploads (keep exactly as you have) -->
                                            <?php if ($row['slip_file']): ?>
                                                <a href="/gem/CoursePay/<?= htmlspecialchars($row['slip_file']) ?>" target="_blank"
                                                    class="inline-flex items-center justify-center gap-1 bg-green-100 text-green-700 px-3 py-1.5 rounded-md hover:bg-green-200 transition text-xs font-medium">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                    </svg>
                                                    1st Slip
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($second_pending): ?>
                                                <span class="text-xs text-orange-600 font-bold">2nd Slip Pending</span>
                                            <?php endif; ?>

                                        <?php else: ?>
                                            <!-- ONLINE PAYMENT — Show View Buttons -->
                                            <?php
                                            // Get first and second online payments
                                            $stmt = $conn->prepare("
                SELECT installment_type, transaction_id, amount, created_at, status 
                FROM payments 
                WHERE application_id = ? AND method = 'Online Payment'
                ORDER BY created_at ASC
            ");
                                            $stmt->bind_param("i", $row['application_id']);
                                            $stmt->execute();
                                            $pay_res = $stmt->get_result();
                                            $online_payments = $pay_res->fetch_all(MYSQLI_ASSOC);
                                            $stmt->close();

                                            $first_online = null;
                                            $second_online = null;
                                            foreach ($online_payments as $p) {
                                                if ($p['installment_type'] === 'first' || $p['installment_type'] === 'full') $first_online = $p;
                                                if ($p['installment_type'] === 'second') $second_online = $p;
                                            }
                                            ?>

                                            <!-- 1st Installment View Button -->
                                            <?php if ($first_online): ?>
                                                <button onclick='openOnlineModal(<?= json_encode([
                                                                                        "type" => "1st Installment",
                                                                                        "tid" => $first_online["transaction_id"],
                                                                                        "amount" => number_format($first_online["amount"], 2),
                                                                                        "date" => date("d/m/Y H:i", strtotime($first_online["created_at"])),
                                                                                        "status" => ucfirst($first_online["status"])
                                                                                    ]) ?>)'
                                                    class="inline-flex items-center justify-center gap-1 bg-teal-100 text-teal-700 px-3 py-1.5 rounded-md hover:bg-teal-200 transition text-xs font-medium">
                                                    View 1st Installment
                                                </button>
                                            <?php endif; ?>

                                            <!-- 2nd Installment View Button -->
                                            <?php if ($second_online): ?>
                                                <button onclick='openOnlineModal(<?= json_encode([
                                                                                        "type" => "2nd Installment",
                                                                                        "tid" => $second_online["transaction_id"],
                                                                                        "amount" => number_format($second_online["amount"], 2),
                                                                                        "date" => date("d/m/Y H:i", strtotime($second_online["created_at"])),
                                                                                        "status" => ucfirst($second_online["status"])
                                                                                    ]) ?>)'
                                                    class="inline-flex items-center justify-center gap-1 bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded-md hover:bg-indigo-200 transition text-xs font-medium">
                                                    View 2nd Installment
                                                </button>
                                            <?php elseif ($second_pending && $first_online): ?>
                                                <span class="text-xs text-orange-600 font-bold">2nd Payment Pending</span>
                                            <?php endif; ?>

                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <?php
                                    $status = $row['payment_status'] ?? 'pending';
                                    $color = $status === 'completed' ? 'bg-green-100 text-green-800' : ($status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                    ?>
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full <?= $color ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-2">
                                        <button onclick="openEdit(<?= $row['student_id'] ?>, '<?= $row['next_payment_date'] ?? '' ?>', <?= $row['due_amount'] ?>, '<?= htmlspecialchars($row['student_id_manual'] ?? '') ?>')"
                                            class="w-full bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition">
                                            Edit
                                        </button>

                                        <form method="POST" class="w-full" onsubmit="return confirm('Are you sure you want to delete this student?')">
                                            <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                                            <button type="submit" name="delete"
                                                class="w-full bg-red-600 text-white px-3 py-1.5 rounded-md text-xs font-medium hover:bg-red-700 transition">
                                                Delete
                                            </button>
                                        </form>

                                        <?php if ($row['checked'] != 1): ?>
                                            <div class="flex gap-1 w-full">
                                                <form method="POST" action="approve_action.php" class="flex-1">
                                                    <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <input type="hidden" name="gmail" value="<?= htmlspecialchars($row['gmail']) ?>">
                                                    <input type="hidden" name="name" value="<?= htmlspecialchars($row['name']) ?>">
                                                    <input type="hidden" name="course_name" value="<?= htmlspecialchars($row['course_name']) ?>">
                                                    <input type="hidden" name="regional_centre" value="<?= htmlspecialchars($row['regional_centre']) ?>">
                                                    <input type="hidden" name="amount" value="<?= $row['total_billed'] ?>">
                                                    <input type="hidden" name="reference_no" value="<?= htmlspecialchars($row['reference_no']) ?>">
                                                    <button type="submit"
                                                        class="w-full bg-green-600 text-white px-2 py-1.5 rounded-md text-xs font-medium hover:bg-green-700 transition">
                                                        Approve
                                                    </button>
                                                </form>
                                                <button type="button" onclick="openRejectModal(<?= $row['student_id'] ?>, '<?= htmlspecialchars($row['gmail']) ?>', '<?= htmlspecialchars($row['name']) ?>')"
                                                    class="w-full bg-gray-600 text-white px-2 py-1.5 rounded-md text-xs font-medium hover:bg-gray-700 transition">
                                                    Reject
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <div class="flex flex-col gap-2">
                                                <div class="w-full bg-green-100 text-green-700 px-3 py-1.5 rounded-md text-xs font-semibold text-center">
                                                    Approved
                                                </div>
                                                <?php if ($second_pending): ?>
                                                    <a href="send_installment_reminder.php?ref=<?= $row['reference_no'] ?>"
                                                        class="w-full bg-purple-600 text-white px-3 py-1.5 rounded-md text-xs font-medium hover:bg-purple-700 transition text-center block">
                                                        Send 2nd Reminder
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Your existing modals (Edit & Reject) — unchanged -->
    <!-- Keep them exactly as you have them -->

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        function openEdit(id, date, due, studentId) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_date').value = date;
            document.getElementById('edit_due').value = due;
            document.getElementById('edit_student_id_manual').value = studentId;
            document.getElementById('editModal').classList.remove('hidden');
            flatpickr("#edit_date", {
                dateFormat: "Y-m-d"
            });
        }

        function closeEdit() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function openRejectModal(id, gmail, name) {
            document.getElementById('reject_student_id').value = id;
            document.getElementById('reject_gmail').value = gmail;
            document.getElementById('reject_name').value = name;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        document.querySelectorAll('select[name="charge_type"]').forEach(select => {
            const approveBtn = select.closest('tr').querySelector('button[type="submit"]');
            if (approveBtn && approveBtn.textContent.includes('Approve')) {
                approveBtn.disabled = !select.value;
                select.addEventListener('change', () => approveBtn.disabled = !select.value);
            }
        });
    </script>

    <!-- Online Payment Details Modal -->
    <div id="onlineModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl p-6 max-w-sm w-full">
            <h3 class="text-lg font-bold text-gray-800 mb-4" id="modalType">1st Installment Details</h3>
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

    <script>
        function openOnlineModal(data) {
            document.getElementById('modalType').textContent = data.type + " Details";
            document.getElementById('modalTid').textContent = data.tid;
            document.getElementById('modalAmount').textContent = data.amount;
            document.getElementById('modalDate').textContent = data.date;
            const statusEl = document.getElementById('modalStatus');
            statusEl.textContent = data.status;
            statusEl.className = 'px-2 py-1 rounded text-xs font-medium ' +
                (data.status === 'Completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800');
            document.getElementById('onlineModal').classList.remove('hidden');
        }
    </script>
</body>

</html>
<?php $conn->close(); ?>