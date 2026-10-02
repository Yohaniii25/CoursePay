<?php
session_start();
if (!isset($_SESSION['user'])) {
    die("HTTP 403: Unauthorized access.");
}
if (!isset($_GET['file']) || empty(trim($_GET['file']))) {
    die("HTTP 400: No file specified.");
}
$filename = basename($_GET['file']); 
$filepath = __DIR__ . '/../Uploads/nic/' . $filename;
if (!file_exists($filepath) || !is_file($filepath)) {
    die("HTTP 404: File not found.");
}
$mime_type = mime_content_type($filepath) ?: 'application/octet-stream';
header('Content-Description: File Transfer');
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));
header('X-Content-Type-Options: nosniff');
ob_clean();
flush();
readfile($filepath);
exit;
?>
