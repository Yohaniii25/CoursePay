<?php
require_once __DIR__ . '/../../classes/db.php';
require_once __DIR__ . '/../../classes/CourseHelper.php';

$db = new Database();
$conn = $db->getConnection();

$sql = "SELECT 
    s.name, s.gmail, s.contact_number,
    COALESCE(s.student_id_manual, CONCAT('GJRTI', LPAD(s.id, 4, '0'))) AS student_id,
    s.reference_no,
    a.course_name, a.course_mode, a.regional_centre,
    p.amount, p.paid_amount, p.due_amount, p.status,
    s.next_payment_date, s.nic_file, p.slip_file,
    p.method, p.transaction_id
FROM students s
LEFT JOIN applications a ON s.id = a.student_id
LEFT JOIN payments p ON a.id = p.application_id
ORDER BY s.id DESC, p.id ASC";

$result = $conn->query($sql);

$filename = 'students_export_' . date('Y-m-d_His') . '.xls';

header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// UTF-8 BOM
echo "\xEF\xBB\xBF";
?>
<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!--[if gte mso 9]>
<xml>
 <x:ExcelWorkbook>
  <x:ExcelWorksheets>
   <x:ExcelWorksheet>
    <x:Name>Student Applications</x:Name>
    <x:WorksheetOptions>
     <x:DisplayGridlines/>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
  </x:ExcelWorksheets>
 </x:ExcelWorkbook>
</xml>
<![endif]-->
<style>
    body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
    table { border-collapse: collapse; width: 100%; }
    th {
        background-color: #25116F;
        color: #ffffff;
        font-weight: bold;
        text-align: left;
        border: 1px solid #1a0b52;
        padding: 8px 10px;
        white-space: nowrap;
    }
    td {
        border: 1px solid #d1d5db;
        padding: 6px 10px;
        vertical-align: middle;
    }
    .text-cell { mso-number-format: "\@"; }
    .num-cell { mso-number-format: "\#\,\#\#0\.00"; text-align: right; }
    .center-cell { text-align: center; }
    .mode-full { background-color: #dbeafe; color: #1e40af; font-weight: bold; text-align: center; }
    .mode-part { background-color: #f3e8ff; color: #6b21a8; font-weight: bold; text-align: center; }
    .status-completed { color: #15803d; font-weight: bold; text-align: center; }
    .status-pending { color: #b45309; font-weight: bold; text-align: center; }
    .report-title {
        font-size: 16pt;
        font-weight: bold;
        color: #25116F;
        text-align: left;
        padding-bottom: 5px;
    }
    .report-subtitle {
        font-size: 10pt;
        color: #6b7280;
        text-align: left;
        padding-bottom: 15px;
    }
</style>
</head>
<body>

<table>
    <tr>
        <td colspan="16" class="report-title">Gem & Jewellery Research and Training Institute (GJRTI)</td>
    </tr>
    <tr>
        <td colspan="16" class="report-subtitle">Student Applications & Payments Export | Generated: <?= date('Y-m-d H:i:s') ?></td>
    </tr>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Contact</th>
        <th>Student ID</th>
        <th>Ref No</th>
        <th>Course</th>
        <th>Mode</th>
        <th>Centre</th>
        <th>Total (Rs.)</th>
        <th>Paid (Rs.)</th>
        <th>Due (Rs.)</th>
        <th>Status</th>
        <th>Next Pay</th>
        <th>NIC</th>
        <th>Slip</th>
        <th>Transaction ID</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): 
        $raw_course = trim($row['course_name'] ?? '');
        $mode = trim($row['course_mode'] ?? '');
        if ($mode === '' && $raw_course !== '') {
            $mode = CourseHelper::resolveCourseMode($raw_course);
        }

        $modeClass = '';
        if ($mode === 'Full-Time') {
            $modeClass = 'mode-full';
        } elseif ($mode === 'Part-Time') {
            $modeClass = 'mode-part';
        }

        $txn_id = '';
        if (!empty($row['transaction_id'])) {
            $txn_id = $row['transaction_id'];
        } elseif (isset($row['method']) && $row['method'] === 'Online Payment') {
            $txn_id = 'Pending';
        }

        $status = strtolower(trim($row['status'] ?? ''));
        $statusClass = ($status === 'completed') ? 'status-completed' : 'status-pending';
    ?>
    <tr>
        <td><?= htmlspecialchars($row['name'] ?? '') ?></td>
        <td><?= htmlspecialchars($row['gmail'] ?? '') ?></td>
        <td class="text-cell"><?= htmlspecialchars($row['contact_number'] ?? '') ?></td>
        <td class="text-cell center-cell"><?= htmlspecialchars($row['student_id'] ?? '') ?></td>
        <td class="text-cell center-cell"><?= htmlspecialchars($row['reference_no'] ?? '') ?></td>
        <td><?= htmlspecialchars($raw_course) ?></td>
        <td class="<?= $modeClass ?>"><?= htmlspecialchars($mode ?: '—') ?></td>
        <td><?= htmlspecialchars($row['regional_centre'] ?? '') ?></td>
        <td class="num-cell"><?= number_format((float)($row['amount'] ?? 0), 2, '.', '') ?></td>
        <td class="num-cell"><?= number_format((float)($row['paid_amount'] ?? 0), 2, '.', '') ?></td>
        <td class="num-cell"><?= number_format((float)($row['due_amount'] ?? 0), 2, '.', '') ?></td>
        <td class="<?= $statusClass ?>"><?= htmlspecialchars(ucfirst($row['status'] ?? '')) ?></td>
        <td class="center-cell"><?= !empty($row['next_payment_date']) ? date('d/m/Y', strtotime($row['next_payment_date'])) : '—' ?></td>
        <td class="center-cell"><?= $row['nic_file'] ? 'Yes' : 'No' ?></td>
        <td class="center-cell"><?= $row['slip_file'] ? 'Yes' : 'No' ?></td>
        <td class="text-cell"><?= htmlspecialchars($txn_id) ?></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>

