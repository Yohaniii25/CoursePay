<?php
require_once __DIR__ . '/../../classes/db.php';
$db = new Database();
$conn = $db->getConnection();

$sql = "SELECT 
    s.name, s.gmail, s.contact_number,
    COALESCE(s.student_id_manual, CONCAT('GJRTI', LPAD(s.id, 4, '0'))) AS student_id,
    s.reference_no,
    a.course_name, a.regional_centre,
    p.amount, p.paid_amount, p.due_amount, p.status,
    s.next_payment_date, s.nic_file, p.slip_file
FROM students s
LEFT JOIN applications a ON s.id = a.student_id
LEFT JOIN payments p ON a.id = p.application_id";

$result = $conn->query($sql);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="students_export.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Name','Email','Contact','Student ID','Ref No','Course','Centre','Total','Paid','Due','Status','Next Pay','NIC','Slip']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['name'], $row['gmail'], $row['contact_number'],
        $row['student_id'], $row['reference_no'],
        $row['course_name'], $row['regional_centre'],
        $row['amount'], $row['paid_amount'], $row['due_amount'],
        $row['status'], $row['next_payment_date'],
        $row['nic_file'] ? 'Yes' : 'No',
        $row['slip_file'] ? 'Yes' : 'No'
    ]);
}
exit;