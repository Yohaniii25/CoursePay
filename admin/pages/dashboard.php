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

        // manual update 
        $stmt = $conn->prepare("
            UPDATE students 
            SET student_id_manual = ?, next_payment_date = ? 
            WHERE id = ?
        ");
        $stmt->bind_param("ssi", $student_id_manual, $next_payment_date, $student_id);
        $stmt->execute();
        $stmt->close();

        // Update due amount in payments
        $stmt = $conn->prepare("
            UPDATE payments p 
            JOIN applications a ON p.application_id = a.id 
            SET p.due_amount = ? 
            WHERE a.student_id = ?
        ");
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
    a.course_name,
    a.regional_centre,
    p.amount,
    p.paid_amount,
    COALESCE(p.due_amount, 0) AS due_amount,
    p.status AS payment_status,
    p.slip_file,
    s.checked
FROM students s
LEFT JOIN applications a ON s.id = a.student_id
LEFT JOIN payments p ON a.id = p.application_id
ORDER BY s.id DESC
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
                            ?>
                            <tr class="hover:bg-purple-50 transition">
                                <td class="px-4 py-4 text-sm font-medium text-gray-900 whitespace-nowrap"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap"><?= htmlspecialchars($row['gmail']) ?></td>
                                <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap"><?= htmlspecialchars($row['contact_number']) ?></td>
                                <td class="px-4 py-4 text-sm font-medium text-blue-700 whitespace-nowrap"><?= htmlspecialchars($display_id) ?></td>
                                <td class="px-4 py-4 text-sm font-medium text-purple-700 whitespace-nowrap"><?= htmlspecialchars($row['reference_no']) ?></td>
                                <td class="px-4 py-4 text-sm text-gray-600"><?= htmlspecialchars($row['course_name'] ?? '—') ?></td>
                                <td class="px-4 py-4 text-sm text-gray-600 whitespace-nowrap"><?= htmlspecialchars($row['regional_centre'] ?? '—') ?></td>
                                <td class="px-4 py-4 text-sm font-semibold text-red-600 whitespace-nowrap">
                                    Rs. <?= number_format($row['due_amount'], 2) ?>
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
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <?php if ($row['slip_file']): ?>
                                        <a href="/gem/CoursePay/<?= htmlspecialchars($row['slip_file']) ?>" target="_blank"
                                           class="inline-flex items-center gap-1 bg-green-100 text-green-700 px-3 py-1.5 rounded-md hover:bg-green-200 transition text-xs font-medium">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                            View
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <?php
                                    $status = $row['payment_status'] ?? 'pending';
                                    $color = $status === 'completed' ? 'bg-green-100 text-green-800' :
                                             ($status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                    ?>
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full <?= $color ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-2">
                                        <!-- Edit Button -->
                                        <button onclick="openEdit(
                                            <?= $row['student_id'] ?>,
                                            '<?= $row['next_payment_date'] ?? '' ?>',
                                            <?= $row['due_amount'] ?>,
                                            '<?= htmlspecialchars($row['student_id_manual'] ?? '') ?>'
                                        )" class="w-full bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition">
                                            Edit
                                        </button>

                                        <!-- Delete Button -->
                                        <form method="POST" class="w-full" onsubmit="return confirm('Are you sure you want to delete this student?')">
                                            <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                                            <button type="submit" name="delete"
                                                    class="w-full bg-red-600 text-white px-3 py-1.5 rounded-md text-xs font-medium hover:bg-red-700 transition">
                                                Delete
                                            </button>
                                        </form>

                                        <!-- Approve/Not Approve Buttons -->
                                        <?php if ($row['checked'] != 1): ?>
                                            <div class="flex gap-1 w-full">
                                                <form method="POST" action="approve_action.php" class="flex-1">
                                                    <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <input type="hidden" name="gmail" value="<?= htmlspecialchars($row['gmail']) ?>">
                                                    <input type="hidden" name="name" value="<?= htmlspecialchars($row['name']) ?>">
                                                    <input type="hidden" name="course_name" value="<?= htmlspecialchars($row['course_name']) ?>">
                                                    <input type="hidden" name="regional_centre" value="<?= htmlspecialchars($row['regional_centre']) ?>">
                                                    <input type="hidden" name="amount" value="<?= $row['amount'] ?>">
                                                    <input type="hidden" name="reference_no" value="<?= htmlspecialchars($row['reference_no']) ?>">
                                                    <button type="submit"
                                                            class="w-full bg-green-600 text-white px-2 py-1.5 rounded-md text-xs font-medium hover:bg-green-700 transition">
                                                        Approve
                                                    </button>
                                                </form>
                                                <button type="button" onclick="openRejectModal(
                                                    <?= $row['student_id'] ?>,
                                                    '<?= htmlspecialchars($row['gmail']) ?>',
                                                    '<?= htmlspecialchars($row['name']) ?>'
                                                )"
                                                        class="w-full bg-gray-600 text-white px-2 py-1.5 rounded-md text-xs font-medium hover:bg-gray-700 transition">
                                                    Reject
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <div class="w-full bg-green-100 text-green-700 px-3 py-1.5 rounded-md text-xs font-semibold text-center">
                                                ✓ Approved
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

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-xl font-bold mb-4 text-gray-800">Reject Application</h3>
            <p class="text-sm text-gray-600 mb-4">Please provide a reason for rejection. This will be sent to the applicant.</p>
            <form method="POST" action="approve_action.php">
                <input type="hidden" name="student_id" id="reject_student_id">
                <input type="hidden" name="action" value="not_approved">
                <input type="hidden" name="gmail" id="reject_gmail">
                <input type="hidden" name="name" id="reject_name">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rejection Comment</label>
                    <textarea name="rejection_comment" id="reject_comment"
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Enter reason for rejection..."
                              rows="4"
                              required></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()" 
                            class="bg-gray-500 text-white px-6 py-2.5 rounded-lg hover:bg-gray-600 transition font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-red-600 text-white px-6 py-2.5 rounded-lg hover:bg-red-700 transition font-medium">
                        Send Rejection
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-xl font-bold mb-6 text-gray-800">Edit Student Information</h3>
            <form method="POST">
                <input type="hidden" name="student_id" id="edit_id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Student ID</label>
                    <input type="text" name="student_id_manual" id="edit_student_id_manual"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                           placeholder="e.g. STU001">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Next Payment Date</label>
                    <input type="text" name="next_payment_date" id="edit_date"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-transparent flatpickr" 
                           placeholder="YYYY-MM-DD">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Due Amount (Rs.)</label>
                    <input type="number" step="0.01" name="due_amount" id="edit_due"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeEdit()" 
                            class="bg-gray-500 text-white px-6 py-2.5 rounded-lg hover:bg-gray-600 transition font-medium">
                        Cancel
                    </button>
                    <button type="submit" name="edit" 
                            class="bg-purple-600 text-white px-6 py-2.5 rounded-lg hover:bg-purple-700 transition font-medium">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        function openEdit(id, date, due, studentId) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_date').value = date;
            document.getElementById('edit_due').value = due;
            document.getElementById('edit_student_id_manual').value = studentId;
            document.getElementById('editModal').classList.remove('hidden');
            flatpickr("#edit_date", { dateFormat: "Y-m-d" });
        }
        function closeEdit() {
            document.getElementById('editModal').classList.add('hidden');
        }
        
        function openRejectModal(studentId, gmail, name) {
            document.getElementById('reject_student_id').value = studentId;
            document.getElementById('reject_gmail').value = gmail;
            document.getElementById('reject_name').value = name;
            document.getElementById('reject_comment').value = '';
            document.getElementById('rejectModal').classList.remove('hidden');
        }
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
        
        // Close modals on outside click
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEdit();
            }
        });
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>