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
$rows = [];
while ($r = $result->fetch_assoc()) {
    $rows[] = $r;
}
$totalCount = count($rows);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Applications Report - GJRTI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1f2937;
            background-color: #f3f4f6;
            padding: 20px;
            font-size: 11px;
            line-height: 1.3;
        }

        .no-print-bar {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-pdf {
            background: #b91c1c;
            color: #ffffff;
        }

        .btn-pdf:hover {
            background: #991b1b;
        }

        .btn-close {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-close:hover {
            background: #d1d5db;
        }

        .report-sheet {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #25116F;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .header-title h1 {
            font-size: 18px;
            color: #25116F;
            font-weight: 800;
            letter-spacing: -0.3px;
        }

        .header-title p {
            font-size: 11px;
            color: #6b7280;
            margin-top: 3px;
        }

        .header-meta {
            text-align: right;
            font-size: 11px;
            color: #4b5563;
        }

        .header-meta strong {
            color: #111827;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        th {
            background-color: #25116F;
            color: #ffffff;
            font-weight: 700;
            text-align: left;
            padding: 7px 8px;
            border: 1px solid #1a0b52;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 2px 7px;
            font-size: 9.5px;
            font-weight: 700;
            border-radius: 9999px;
            text-align: center;
            white-space: nowrap;
        }

        .badge-full {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .badge-part {
            background-color: #f3e8ff;
            color: #6b21a8;
            border: 1px solid #e9d5ff;
        }

        .status-completed {
            color: #15803d;
            font-weight: 700;
        }

        .status-pending {
            color: #b45309;
            font-weight: 700;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .nowrap {
            white-space: nowrap;
        }

        .font-mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
                font-size: 9pt;
            }

            .no-print-bar {
                display: none !important;
            }

            .report-sheet {
                border: none;
                box-shadow: none;
                padding: 0;
            }

            @page {
                size: landscape;
                margin: 8mm;
            }

            th {
                background-color: #25116F !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge-full {
                background-color: #dbeafe !important;
                color: #1e40af !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .badge-part {
                background-color: #f3e8ff !important;
                color: #6b21a8 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div>
            <span style="font-size: 14px; font-weight: 700; color: #111827;">GJRTI Student Applications & Payments Report</span>
            <span style="color: #6b7280; margin-left: 10px; font-size: 12px;">Total Records: <strong><?= $totalCount ?></strong></span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn btn-pdf">
                <i class="fas fa-print"></i> Save as PDF / Print
            </button>
            <button onclick="window.close()" class="btn btn-close">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    </div>

    <div class="report-sheet">
        <div class="header">
            <div class="header-title">
                <h1>Gem & Jewellery Research and Training Institute</h1>
                <p>CoursePay Admissions & Student Applications Register</p>
            </div>
            <div class="header-meta">
                <div>Generated: <strong><?= date('Y-m-d H:i') ?></strong></div>
                <div>Total Students: <strong><?= $totalCount ?></strong></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Student ID</th>
                    <th>Ref No</th>
                    <th>Course</th>
                    <th class="text-center">Mode</th>
                    <th>Centre</th>
                    <th class="text-right">Total (Rs.)</th>
                    <th class="text-right">Paid (Rs.)</th>
                    <th class="text-right">Due (Rs.)</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Next Pay</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                foreach ($rows as $row): 
                    $raw_course = trim($row['course_name'] ?? '');
                    $mode = trim($row['course_mode'] ?? '');
                    if ($mode === '' && $raw_course !== '') {
                        $mode = CourseHelper::resolveCourseMode($raw_course);
                    }
                    $status = strtolower(trim($row['status'] ?? ''));
                ?>
                <tr>
                    <td class="text-center font-mono" style="color: #6b7280; font-size: 9px;"><?= $i++ ?></td>
                    <td style="font-weight: 600;"><?= htmlspecialchars($row['name'] ?? '—') ?></td>
                    <td class="nowrap"><?= htmlspecialchars($row['contact_number'] ?? '—') ?></td>
                    <td class="nowrap font-mono text-center"><?= htmlspecialchars($row['student_id'] ?? '—') ?></td>
                    <td class="nowrap font-mono text-center"><?= htmlspecialchars($row['reference_no'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($raw_course) ?></td>
                    <td class="text-center nowrap">
                        <?php if ($mode === 'Full-Time'): ?>
                            <span class="badge badge-full">Full-Time</span>
                        <?php elseif ($mode === 'Part-Time'): ?>
                            <span class="badge badge-part">Part-Time</span>
                        <?php else: ?>
                            <span style="color: #9ca3af;">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="nowrap"><?= htmlspecialchars($row['regional_centre'] ?? '—') ?></td>
                    <td class="text-right nowrap"><?= number_format((float)($row['amount'] ?? 0), 2) ?></td>
                    <td class="text-right nowrap" style="color: #15803d; font-weight: 600;"><?= number_format((float)($row['paid_amount'] ?? 0), 2) ?></td>
                    <td class="text-right nowrap" style="color: #b91c1c; font-weight: 600;"><?= number_format((float)($row['due_amount'] ?? 0), 2) ?></td>
                    <td class="text-center nowrap <?= ($status === 'completed') ? 'status-completed' : 'status-pending' ?>">
                        <?= htmlspecialchars(ucfirst($row['status'] ?? '')) ?>
                    </td>
                    <td class="text-center nowrap"><?= !empty($row['next_payment_date']) ? date('d/m/Y', strtotime($row['next_payment_date'])) : '—' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Automatically prompt print dialog when opened in a new tab
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>

