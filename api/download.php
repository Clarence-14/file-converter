<?php

/**
 * File Download API
 * Serves converted files and cleans up after download.
 */

$convertedDir = __DIR__ . '/../converted/';

// Validate parameters
$filename = isset($_GET['file']) ? basename($_GET['file']) : '';
$downloadName = isset($_GET['name']) ? $_GET['name'] : $filename;

if (!$filename) {
    http_response_code(400);
    echo 'Missing file parameter';
    exit;
}

$filePath = $convertedDir . $filename;

// Security: ensure the file is within the converted directory
$realConverted = realpath($convertedDir);
$realFile = realpath($filePath);

if (!$realFile || strpos($realFile, $realConverted) !== 0) {
    http_response_code(403);
    echo 'Access denied';
    exit;
}

if (!file_exists($filePath)) {
    http_response_code(404);
    echo 'File not found or has expired';
    exit;
}

// Determine MIME type
$mimeTypes = [
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'webp' => 'image/webp',
    'bmp'  => 'image/bmp',
    'gif'  => 'image/gif',
    'tiff' => 'image/tiff',
    'tif'  => 'image/tiff',
    'ico'  => 'image/x-icon',
    'pdf'  => 'application/pdf',
    'csv'  => 'text/csv',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'txt'  => 'text/plain',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
];

$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
$mime = $mimeTypes[$ext] ?? 'application/octet-stream';

// Serve file
header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . addslashes($downloadName) . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: no-cache, must-revalidate');

readfile($filePath);

// Clean up after serving
@unlink($filePath);
exit;
