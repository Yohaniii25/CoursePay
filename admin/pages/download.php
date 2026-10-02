<?php
session_start();

// Ensure the user is logged in as admin
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    die('Unauthorized access.');
}

if (!isset($_GET['file']) || empty($_GET['file'])) {
    http_response_code(400);
    die('Invalid request.');
}

// Sanitize the file name to prevent directory traversal attacks
$filename = basename($_GET['file']);

// Define the absolute path to the uploads directory
$filepath = __DIR__ . '/../../Uploads/nic/' . $filename;

// Check if the file exists and is a valid file
if (file_exists($filepath) && is_file($filepath)) {
    // Get the file's mime type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $filepath);
    finfo_close($finfo);

    // Set headers to force download and prevent XSS execution
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream'); // Force download instead of inline display
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));
    
    // Additional security headers to mitigate XSS
    header('X-Content-Type-Options: nosniff');
    
    // Clear output buffer
    ob_clean();
    flush();
    
    // Read and serve the file
    readfile($filepath);
    exit;
} else {
    http_response_code(404);
    die('File not found.');
}
