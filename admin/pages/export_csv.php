<?php
require_once __DIR__ . '/../../classes/db.php';
$db = new Database();
$conn = $db->getConnection();

/**
 * Determine whether a course is Full-Time or Part-Time
 */
function resolve_course_mode($course_name) {
    if (empty($course_name)) return '';
    $clean = trim($course_name);
    $lower = strtolower($clean);

    // Explicit Part-Time courses in GJRTI
    $part_time_courses = [
        'certificate in basic gemmology',
        'certificate in gem cutting and polishing (weekend)',
        'certificate in gem cutting and polishing (10 days)',
        'gem related certificate in tailor – made courses',
        'jewellery certificate in tailor – made courses',
        'certificate in tailor – made courses',
        'gem related certificate in tailor - made courses',
        'jewellery certificate in tailor - made courses',
        'certificate in tailor - made courses',
    ];

    if (in_array($lower, $part_time_courses)) {
        return 'Part-Time';
    }

    // Heuristics for keywords
    if (
        strpos($lower, 'weekend') !== false ||
        strpos($lower, '10 day') !== false ||
        strpos($lower, 'tailor') !== false ||
        strpos($lower, 'basic gemmology') !== false ||
        strpos($lower, 'part-time') !== false ||
        strpos($lower, 'part time') !== false
    ) {
        return 'Part-Time';
    }

    // All standard GJRTI certificate & diploma courses are Full-Time
    return 'Full-Time';
}


$sql = "SELECT 
    s.name, s.gmail, s.contact_number,
    COALESCE(s.student_id_manual, CONCAT('GJRTI', LPAD(s.id, 4, '0'))) AS student_id,
    s.reference_no,
    a.course_name, a.regional_centre,
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

fputcsv($output, ['Name','Email','Contact','Student ID','Ref No','Course','Centre','Total','Paid','Due','Status','Next Pay','NIC','Slip','Transaction ID']);

while ($row = $result->fetch_assoc()) {
    $raw_course = trim($row['course_name'] ?? '');
    if ($raw_course !== '') {
        if (!preg_match('/\b(full[\s-]?time|part[\s-]?time)\b/i', $raw_course)) {
            $mode = resolve_course_mode($raw_course);
            $course_display = $raw_course . ($mode ? " ({$mode})" : "");
        } else {
            $course_display = $raw_course;
        }
    } else {
        $course_display = '';
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
        $course_display, $row['regional_centre'],
        $row['amount'], $row['paid_amount'], $row['due_amount'],
        $row['status'], $row['next_payment_date'],
        $row['nic_file'] ? 'Yes' : 'No',
        $row['slip_file'] ? 'Yes' : 'No',
        $txn_id
    ]);
}
exit;
