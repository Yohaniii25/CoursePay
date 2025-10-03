<?php
session_start();
require_once __DIR__ . '/classes/db.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['reference_no']) || !isset($_POST['transaction_id'])) {
    die("Error: Invalid request.");
}

$reference_no = $_POST['reference_no'];
$transaction_id = $_POST['transaction_id'];


$db = new Database();
$conn = $db->getConnection();


$stmt = $conn->prepare("
    SELECT s.name, s.contact_number, s.address, s.gmail, a.regional_centre, a.course_name, a.registration_fee, a.course_fee, p.amount, p.status
    FROM students s
    JOIN applications a ON s.id = a.student_id
    JOIN payments p ON a.id = p.application_id
    WHERE s.reference_no = ? AND p.status = 'completed'
");
$stmt->bind_param("s", $reference_no);
$stmt->execute();
$result = $stmt->get_result();
$application = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$application) {
    die("Error: Payment not found or not completed.");
}

// Generate LaTeX content
$latex_content = '
\documentclass[a4paper,12pt]{article}
\usepackage[utf8]{inputenc}
\usepackage[T1]{fontenc}
\usepackage{lmodern}
\usepackage{geometry}
\geometry{margin=1in}
\usepackage{fancyhdr}
\usepackage{lastpage}
\pagestyle{fancy}
\fancyhf{}
\lhead{Gem and Jewellery}
\rhead{Payment Receipt}
\cfoot{Page \thepage\ of \pageref{LastPage}}
\begin{document}
\begin{center}
    \textbf{\Large Payment Receipt} \\
    \vspace{0.5cm}
    Gem and Jewellery \\
    \vspace{0.2cm}
    \today
\end{center}
\vspace{1cm}
\begin{tabular}{ll}
    \textbf{Reference Number:} & ' . htmlspecialchars($reference_no) . ' \\
    \textbf{Transaction ID:} & ' . htmlspecialchars($transaction_id ?: 'N/A') . ' \\
    \textbf{Name:} & ' . htmlspecialchars($application['name']) . ' \\
    \textbf{Contact Number:} & ' . htmlspecialchars($application['contact_number'] ?: 'N/A') . ' \\
    \textbf{Email:} & ' . htmlspecialchars($application['gmail'] ?: 'N/A') . ' \\
    \textbf{Address:} & ' . htmlspecialchars($application['address'] ?: 'N/A') . ' \\
    \textbf{Regional Centre:} & ' . htmlspecialchars($application['regional_centre']) . ' \\
    \textbf{Course:} & ' . htmlspecialchars($application['course_name']) . ' \\
    \textbf{Registration Fee:} & Rs. ' . number_format($application['registration_fee'], 2) . ' \\
    \textbf{Course Fee:} & Rs. ' . number_format($application['course_fee'], 2) . ' \\
    \textbf{Total Amount Paid:} & Rs. ' . number_format($application['amount'], 2) . ' \\
    \textbf{Payment Status:} & Completed \\
\end{tabular}
\vspace{1cm}
\begin{center}
    Thank you for your payment! \\
    Please retain this receipt for your records.
\end{center}
\end{document}
';


$temp_dir = sys_get_temp_dir();
$latex_file = $temp_dir . '/receipt_' . $reference_no . '.tex';
file_put_contents($latex_file, $latex_content);


$command = "latexmk -pdf -interaction=nonstopmode -outdir=$temp_dir $latex_file 2>&1";
exec($command, $output, $return_var);

if ($return_var !== 0) {
    die("Error: Failed to generate PDF. " . implode("\n", $output));
}

$pdf_file = $temp_dir . '/receipt_' . $reference_no . '.pdf';
if (!file_exists($pdf_file)) {
    die("Error: PDF file not generated.");
}


header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="receipt_' . $reference_no . '.pdf"');
header('Content-Length: ' . filesize($pdf_file));
readfile($pdf_file);


unlink($pdf_file);
unlink($latex_file);
array_map('unlink', glob("$temp_dir/receipt_$reference_no.*"));
?>