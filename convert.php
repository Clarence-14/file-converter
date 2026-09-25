<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    // Conversion tool definitions
    $tools = [
        'jpg-to-png'  => ['title' => 'JPG to PNG',  'desc' => 'Convert your JPEG image to PNG format with transparency support',  'accept' => '.jpg,.jpeg', 'category' => 'image'],
        'png-to-jpg'  => ['title' => 'PNG to JPG',  'desc' => 'Convert your PNG image to compressed JPEG format',  'accept' => '.png', 'category' => 'image'],
        'jpg-to-webp' => ['title' => 'JPG to WEBP', 'desc' => 'Convert your JPEG image to modern WebP format',  'accept' => '.jpg,.jpeg', 'category' => 'image'],
        'webp-to-jpg' => ['title' => 'WEBP to JPG', 'desc' => 'Convert your WebP image to universal JPEG format', 'accept' => '.webp', 'category' => 'image'],
        'png-to-webp' => ['title' => 'PNG to WEBP', 'desc' => 'Compress your PNG image into lightweight WebP',    'accept' => '.png', 'category' => 'image'],
        'webp-to-png' => ['title' => 'WEBP to PNG', 'desc' => 'Convert your WebP image to lossless PNG format',   'accept' => '.webp', 'category' => 'image'],
        'bmp-to-jpg'  => ['title' => 'BMP to JPG',  'desc' => 'Convert your bitmap image to compressed JPEG',     'accept' => '.bmp', 'category' => 'image'],
        'bmp-to-png'  => ['title' => 'BMP to PNG',  'desc' => 'Convert your bitmap image to PNG format',          'accept' => '.bmp', 'category' => 'image'],
        'jpg-to-bmp'  => ['title' => 'JPG to BMP',  'desc' => 'Convert your JPEG image to bitmap BMP format',     'accept' => '.jpg,.jpeg', 'category' => 'image'],
        'png-to-bmp'  => ['title' => 'PNG to BMP',  'desc' => 'Convert your PNG image to bitmap BMP format',      'accept' => '.png', 'category' => 'image'],
        'tiff-to-jpg' => ['title' => 'TIFF to JPG', 'desc' => 'Convert your TIFF image to JPEG format',          'accept' => '.tiff,.tif', 'category' => 'image'],
        'tiff-to-png' => ['title' => 'TIFF to PNG', 'desc' => 'Convert your TIFF image to PNG format',           'accept' => '.tiff,.tif', 'category' => 'image'],
        'gif-to-png'  => ['title' => 'GIF to PNG',  'desc' => 'Convert your GIF to a static PNG image',           'accept' => '.gif', 'category' => 'image'],
        'gif-to-jpg'  => ['title' => 'GIF to JPG',  'desc' => 'Convert your GIF to a JPEG image',                 'accept' => '.gif', 'category' => 'image'],
        'png-to-gif'  => ['title' => 'PNG to GIF',  'desc' => 'Convert your PNG image to GIF format',             'accept' => '.png', 'category' => 'image'],
        'jpg-to-ico'  => ['title' => 'JPG to ICO',  'desc' => 'Create a favicon ICO file from your JPEG image',   'accept' => '.jpg,.jpeg', 'category' => 'image'],
        'png-to-ico'  => ['title' => 'PNG to ICO',  'desc' => 'Create a favicon ICO file from your PNG image',    'accept' => '.png', 'category' => 'image'],
        'jpg-to-pdf'  => ['title' => 'JPG to PDF',  'desc' => 'Convert your JPEG image into a PDF document',      'accept' => '.jpg,.jpeg', 'category' => 'pdf'],
        'png-to-pdf'  => ['title' => 'PNG to PDF',  'desc' => 'Convert your PNG image into a PDF document',       'accept' => '.png', 'category' => 'pdf'],
        'webp-to-pdf' => ['title' => 'WEBP to PDF', 'desc' => 'Convert your WebP image into a PDF document',      'accept' => '.webp', 'category' => 'pdf'],
        'bmp-to-pdf'  => ['title' => 'BMP to PDF',  'desc' => 'Convert your bitmap image into a PDF document',    'accept' => '.bmp', 'category' => 'pdf'],
        'docx-to-pdf' => ['title' => 'DOCX to PDF', 'desc' => 'Convert your Word document to PDF format',         'accept' => '.docx', 'category' => 'document'],
        'xlsx-to-csv' => ['title' => 'XLSX to CSV', 'desc' => 'Convert your Excel spreadsheet to CSV format',     'accept' => '.xlsx', 'category' => 'document'],
        'csv-to-xlsx' => ['title' => 'CSV to XLSX', 'desc' => 'Convert your CSV file to an Excel spreadsheet',    'accept' => '.csv', 'category' => 'document'],
        'txt-to-pdf'  => ['title' => 'TXT to PDF',  'desc' => 'Convert your text file to a formatted PDF',        'accept' => '.txt', 'category' => 'pdf'],
        'pdf-to-txt'  => ['title' => 'PDF to TXT',  'desc' => 'Extract all text content from your PDF file',      'accept' => '.pdf', 'category' => 'pdf'],
        'pdf-to-docx' => ['title' => 'PDF to DOCX', 'desc' => 'Convert your PDF file to an editable Word document', 'accept' => '.pdf', 'category' => 'pdf'],
        'pdf-to-jpg'  => ['title' => 'PDF to JPG',  'desc' => 'Render your PDF page as a high-quality JPEG image', 'accept' => '.pdf', 'category' => 'pdf'],
        'pdf-to-png'  => ['title' => 'PDF to PNG',  'desc' => 'Render your PDF page as a crisp PNG image',         'accept' => '.pdf', 'category' => 'pdf'],
    ];

    // Get requested tool
    $toolSlug = isset($_GET['tool']) ? trim($_GET['tool']) : '';
    $tool = isset($tools[$toolSlug]) ? $tools[$toolSlug] : null;

    if (!$tool) {
        header('Location: index.php');
        exit;
    }

    $categoryColors = [
        'image' => '#6c5ce7',
        'document' => '#00b894',
        'pdf' => '#e17055',
    ];
    $categoryColor = $categoryColors[$tool['category']] ?? '#6c5ce7';

    $categoryIcons = [
        'image' => '🖼️',
        'document' => '📄',
        'pdf' => '📕',
    ];
    $categoryIcon = $categoryIcons[$tool['category']] ?? '📄';
    ?>
    <title><?= htmlspecialchars($tool['title']) ?> — FileForge</title>
    <meta name="description" content="<?= htmlspecialchars($tool['desc']) ?> — Free offline converter">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="orb"></div>
        <div class="orb"></div>
        <div class="orb"></div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <span class="brand-icon">⚡</span>
                FileForge
            </a>
            <div class="d-flex align-items-center gap-2">
                <span class="nav-link-custom d-none d-md-inline-flex align-items-center gap-1" style="font-size:0.78rem; color: var(--color-document-light) !important;">
                    <span style="display:inline-block; width:6px; height:6px; background:#55efc4; border-radius:50%;"></span>
                    Offline Mode
                </span>
            </div>
        </div>
    </nav>

    <!-- Conversion Page -->
    <section class="convert-page">
        <div class="convert-container">

            <!-- Back Button -->
            <a href="index.php" class="convert-back-btn">← Back to all tools</a>

            <!-- Header -->
            <div class="convert-header">
                <div style="font-size: 2.5rem; margin-bottom: 0.75rem;"><?= $categoryIcon ?></div>
                <h1><?= htmlspecialchars($tool['title']) ?></h1>
                <p><?= htmlspecialchars($tool['desc']) ?></p>
            </div>

            <!-- Hidden conversion type -->
            <input type="hidden" id="conversionType" value="<?= htmlspecialchars($toolSlug) ?>">

            <!-- Upload Zone -->
            <div class="upload-zone" id="uploadZone">
                <input type="file" id="fileInput" accept="<?= htmlspecialchars($tool['accept']) ?>" style="display:none">
                <div class="upload-zone-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <polyline points="17 8 12 3 7 8" />
                        <line x1="12" y1="3" x2="12" y2="15" />
                    </svg>
                </div>
                <div class="upload-zone-text">
                    <h3>Drop your file here</h3>
                    <p>or <span class="browse-link">browse</span> to choose a file</p>
                </div>
            </div>

            <!-- File Preview -->
            <div class="file-preview" id="filePreview">
                <div class="file-preview-icon"><?= $categoryIcon ?></div>
                <div class="file-preview-info">
                    <div class="file-preview-name" id="filePreviewName"></div>
                    <div class="file-preview-size" id="filePreviewSize"></div>
                </div>
                <button class="file-preview-remove" id="removeFileBtn" title="Remove file">✕</button>
            </div>

            <!-- Convert Button -->
            <button class="convert-btn" id="convertBtn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="16 3 21 3 21 8" />
                    <line x1="4" y1="20" x2="21" y2="3" />
                    <polyline points="21 16 21 21 16 21" />
                    <line x1="15" y1="15" x2="21" y2="21" />
                </svg>
                Convert Now
            </button>

            <!-- Progress Section -->
            <div class="progress-section" id="progressSection">
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" id="progressBarFill"></div>
                </div>
                <p class="progress-text" id="progressText">Uploading file...</p>
            </div>

            <!-- Result Section -->
            <div class="result-section" id="resultSection">
                <div class="result-success-icon">✓</div>
                <div class="result-title">Conversion Complete!</div>
                <div class="result-filename" id="resultFilename"></div>
                <a href="#" class="download-btn" id="downloadBtn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <polyline points="7 10 12 15 17 10" />
                        <line x1="12" y1="15" x2="12" y2="3" />
                    </svg>
                    Download File
                </a>
                <br>
                <button class="convert-another-btn" id="convertAnotherBtn">↻ Convert Another File</button>
            </div>

            <!-- Error Section -->
            <div class="error-section" id="errorSection">
                <div class="error-icon">⚠️</div>
                <div class="error-message" id="errorMessage">Something went wrong</div>
                <div class="error-detail" id="errorDetail"></div>
                <button class="convert-another-btn" onclick="location.reload()" style="margin-top:1rem;">↻ Try Again</button>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>FileForge — All conversions run locally on your machine. No data leaves your computer.</p>
        </div>
    </footer>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>

</html>