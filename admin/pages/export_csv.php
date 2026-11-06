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

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="students_export.csv"');

$output = fopen('php://output', 'w');

// Output UTF-8 BOM so Excel on Windows recognizes UTF-8 properly
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['Name','Email','Contact','Student ID','Ref No','Course','Mode','Centre','Total','Paid','Due','Status','Next Pay','NIC','Slip','Transaction ID']);

while ($row = $result->fetch_assoc()) {
    $raw_course = trim($row['course_name'] ?? '');
    $mode = trim($row['course_mode'] ?? '');
    if ($mode === '' && $raw_course !== '') {
        $mode = CourseHelper::resolveCourseMode($raw_course);
    }

    $txn_id = '';
    if (!empty($row['transaction_id'])) {
        $txn_id = $row['transaction_id'];
    } elseif (isset($row['method']) && $row['method'] === 'Online Payment') {
        $txn_id = 'Pending';
    }

    fputcsv($output, [
        $row['name'], $row['gmail'], $row['contact_number'],
        $row['student_id'], $row['reference_no'],
        $raw_course, $mode, $row['regional_centre'],
        $row['amount'], $row['paid_amount'], $row['due_amount'],
        $row['status'], $row['next_payment_date'],
        $row['nic_file'] ? 'Yes' : 'No',
        $row['slip_file'] ? 'Yes' : 'No',
        $txn_id
    ]);
}
exit;
