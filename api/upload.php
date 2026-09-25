<?php
/**
 * File Upload & Conversion API
 * 
 * Accepts file uploads via multipart form data, validates them,
 * runs the Python conversion engine, and returns the result.
 */

header('Content-Type: application/json');

// Configuration
$pythonPath = 'python';
$converterScript = __DIR__ . '/../python/converter.py';
$uploadDir = __DIR__ . '/../uploads/';
$convertedDir = __DIR__ . '/../converted/';
$maxFileSize = 50 * 1024 * 1024; // 50 MB

// Ensure directories exist
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
if (!is_dir($convertedDir)) mkdir($convertedDir, 0755, true);

// Conversion definitions: type => [accepted_extensions, output_extension, label]
$conversions = [
    // Image conversions
    'jpg-to-png'  => [['jpg', 'jpeg'], 'png', 'JPG to PNG'],
    'png-to-jpg'  => [['png'], 'jpg', 'PNG to JPG'],
    'jpg-to-webp' => [['jpg', 'jpeg'], 'webp', 'JPG to WEBP'],
    'webp-to-jpg' => [['webp'], 'jpg', 'WEBP to JPG'],
    'png-to-webp' => [['png'], 'webp', 'PNG to WEBP'],
    'webp-to-png' => [['webp'], 'png', 'WEBP to PNG'],
    'bmp-to-jpg'  => [['bmp'], 'jpg', 'BMP to JPG'],
    'bmp-to-png'  => [['bmp'], 'png', 'BMP to PNG'],
    'jpg-to-bmp'  => [['jpg', 'jpeg'], 'bmp', 'JPG to BMP'],
    'png-to-bmp'  => [['png'], 'bmp', 'PNG to BMP'],
    'tiff-to-jpg' => [['tiff', 'tif'], 'jpg', 'TIFF to JPG'],
    'tiff-to-png' => [['tiff', 'tif'], 'png', 'TIFF to PNG'],
    'gif-to-png'  => [['gif'], 'png', 'GIF to PNG'],
    'gif-to-jpg'  => [['gif'], 'jpg', 'GIF to JPG'],
    'png-to-gif'  => [['png'], 'gif', 'PNG to GIF'],
    'jpg-to-ico'  => [['jpg', 'jpeg'], 'ico', 'JPG to ICO'],
    'png-to-ico'  => [['png'], 'ico', 'PNG to ICO'],
    'jpg-to-pdf'  => [['jpg', 'jpeg'], 'pdf', 'JPG to PDF'],
    'png-to-pdf'  => [['png'], 'pdf', 'PNG to PDF'],
    'webp-to-pdf' => [['webp'], 'pdf', 'WEBP to PDF'],
    'bmp-to-pdf'  => [['bmp'], 'pdf', 'BMP to PDF'],
    // Document conversions
    'docx-to-pdf' => [['docx'], 'pdf', 'DOCX to PDF'],
    'xlsx-to-csv' => [['xlsx'], 'csv', 'XLSX to CSV'],
    'csv-to-xlsx' => [['csv'], 'xlsx', 'CSV to XLSX'],
    'txt-to-pdf'  => [['txt'], 'pdf', 'TXT to PDF'],
    'pdf-to-txt'  => [['pdf'], 'txt', 'PDF to TXT'],
    'pdf-to-docx' => [['pdf'], 'docx', 'PDF to DOCX'],
    'pdf-to-jpg'  => [['pdf'], 'jpg',  'PDF to JPG'],
    'pdf-to-png'  => [['pdf'], 'png',  'PDF to PNG'],
];

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get conversion type
$conversionType = isset($_POST['conversion_type']) ? trim($_POST['conversion_type']) : '';
if (!$conversionType || !isset($conversions[$conversionType])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid conversion type']);
    exit;
}

$convDef = $conversions[$conversionType];
$acceptedExts = $convDef[0];
$outputExt = $convDef[1];

// Validate file upload
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE   => 'File exceeds the maximum upload size',
        UPLOAD_ERR_FORM_SIZE  => 'File exceeds the form maximum size',
        UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE    => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder on server',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
    ];
    $errorCode = isset($_FILES['file']) ? $_FILES['file']['error'] : UPLOAD_ERR_NO_FILE;
    $msg = $errorMessages[$errorCode] ?? 'Unknown upload error';
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}

$file = $_FILES['file'];

// Check file size
if ($file['size'] > $maxFileSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'File exceeds maximum size of 50MB']);
    exit;
}

// Check file extension
$originalName = $file['name'];
$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
if (!in_array($ext, $acceptedExts)) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'error' => "Invalid file type. Expected: " . implode(', ', $acceptedExts) . ". Got: " . $ext
    ]);
    exit;
}

// Generate unique filenames
$uniqueId = uniqid('conv_', true);
$inputFilename = $uniqueId . '.' . $ext;
$outputFilename = $uniqueId . '.' . $outputExt;
$inputPath = $uploadDir . $inputFilename;
$outputPath = $convertedDir . $outputFilename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $inputPath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save uploaded file']);
    exit;
}

// Run Python converter
$command = sprintf(
    '%s %s %s %s %s 2>&1',
    escapeshellarg($pythonPath),
    escapeshellarg($converterScript),
    escapeshellarg($inputPath),
    escapeshellarg($outputPath),
    escapeshellarg($conversionType)
);

$output = [];
$returnCode = 0;
exec($command, $output, $returnCode);

// Clean up the uploaded file
if (file_exists($inputPath)) {
    @unlink($inputPath);
}

// Check result
if ($returnCode !== 0 || !file_exists($outputPath)) {
    $errorMsg = !empty($output) ? implode("\n", $output) : 'Conversion failed';
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
    exit;
}

// Build the download filename
$baseName = pathinfo($originalName, PATHINFO_FILENAME);
$downloadName = $baseName . '.' . $outputExt;

// Success response
echo json_encode([
    'success' => true,
    'download_url' => 'api/download.php?file=' . urlencode($outputFilename) . '&name=' . urlencode($downloadName),
    'filename' => $downloadName,
    'size' => filesize($outputPath),
]);
