<?php
require_once __DIR__ . '/../../classes/db.php';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=students_data.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Reference No', 'Name', 'Email', 'Contact', 'Course', 'Centre', 'Fee', 'Payment Status']);

$db = new Database();
$conn = $db->getConnection();

$sql = "SELECT s.reference_no, s.name, s.gmail, s.contact_number,
               a.course_name, a.regional_centre, a.course_fee,
               p.status AS payment_status
        FROM students s
        LEFT JOIN applications a ON s.id = a.student_id
        LEFT JOIN payments p ON a.id = p.application_id";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit;
?>
