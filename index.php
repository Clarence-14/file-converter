<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FileForge — Offline File Converter</title>
    <meta name="description" content="Convert files between formats entirely offline. Images, documents, and more — all processed locally on your machine.">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Inline critical above-fold styles */
        .tool-card {
            opacity: 0;
            animation: fadeInCard 0.5s ease-out forwards;
        }

        @keyframes fadeInCard {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-badge">
                <span class="dot"></span>
                100% Offline &amp; Private
            </div>
            <h1 class="hero-title">
                Every file tool you need,<br>
                <span class="gradient-text">right on your machine.</span>
            </h1>
            <p class="hero-subtitle">
                Convert images, documents, and more between formats — completely offline.
                No uploads to the cloud. No internet required. Fast &amp; private.
            </p>
        </div>
    </section>

    <!-- Category Filter Tabs -->
    <div class="container">
        <div class="category-tabs">
            <button class="category-tab active" data-category="all" onclick="filterTools('all', this)">All Tools</button>
            <button class="category-tab" data-category="image" onclick="filterTools('image', this)">🖼️ Images</button>
            <button class="category-tab" data-category="document" onclick="filterTools('document', this)">📄 Documents</button>
            <button class="category-tab" data-category="pdf" onclick="filterTools('pdf', this)">📕 PDF</button>
        </div>
    </div>

    <!-- Tools Grid -->
    <div class="container tools-grid">
        <div class="row g-3" id="toolsGrid">

            <?php
            // Define all conversion tools
            $tools = [
                // Image conversions
                ['slug' => 'jpg-to-png',  'title' => 'JPG to PNG',  'desc' => 'Convert JPEG images to PNG format with transparency support', 'icon' => '🖼️', 'category' => 'image'],
                ['slug' => 'png-to-jpg',  'title' => 'PNG to JPG',  'desc' => 'Convert PNG images to compressed JPEG format', 'icon' => '🖼️', 'category' => 'image'],
                ['slug' => 'jpg-to-webp', 'title' => 'JPG to WEBP', 'desc' => 'Convert JPEG to modern WebP for smaller file sizes', 'icon' => '🌐', 'category' => 'image'],
                ['slug' => 'webp-to-jpg', 'title' => 'WEBP to JPG', 'desc' => 'Convert WebP images back to universal JPEG', 'icon' => '🌐', 'category' => 'image'],
                ['slug' => 'png-to-webp', 'title' => 'PNG to WEBP', 'desc' => 'Compress PNG images into lightweight WebP format', 'icon' => '🌐', 'category' => 'image'],
                ['slug' => 'webp-to-png', 'title' => 'WEBP to PNG', 'desc' => 'Convert WebP to PNG with lossless quality', 'icon' => '🌐', 'category' => 'image'],
                ['slug' => 'bmp-to-jpg',  'title' => 'BMP to JPG',  'desc' => 'Convert bitmap images to compressed JPEG', 'icon' => '🖼️', 'category' => 'image'],
                ['slug' => 'bmp-to-png',  'title' => 'BMP to PNG',  'desc' => 'Convert bitmap images to PNG format', 'icon' => '🖼️', 'category' => 'image'],
                ['slug' => 'tiff-to-jpg', 'title' => 'TIFF to JPG', 'desc' => 'Convert TIFF images to JPEG', 'icon' => '🖼️', 'category' => 'image'],
                ['slug' => 'gif-to-png',  'title' => 'GIF to PNG',  'desc' => 'Convert GIF frames to static PNG', 'icon' => '🎞️', 'category' => 'image'],
                ['slug' => 'png-to-ico',  'title' => 'PNG to ICO',  'desc' => 'Create favicon ICO files from PNG images', 'icon' => '⭐', 'category' => 'image'],
                ['slug' => 'jpg-to-ico',  'title' => 'JPG to ICO',  'desc' => 'Create favicon ICO files from JPEG images', 'icon' => '⭐', 'category' => 'image'],

                // PDF conversions
                ['slug' => 'jpg-to-pdf',  'title' => 'JPG to PDF',  'desc' => 'Convert JPEG images into PDF documents', 'icon' => '📕', 'category' => 'pdf'],
                ['slug' => 'png-to-pdf',  'title' => 'PNG to PDF',  'desc' => 'Convert PNG images into PDF documents', 'icon' => '📕', 'category' => 'pdf'],
                ['slug' => 'webp-to-pdf', 'title' => 'WEBP to PDF', 'desc' => 'Convert WebP images into PDF documents', 'icon' => '📕', 'category' => 'pdf'],
                ['slug' => 'bmp-to-pdf',  'title' => 'BMP to PDF',  'desc' => 'Convert bitmap images into PDF documents', 'icon' => '📕', 'category' => 'pdf'],
                ['slug' => 'txt-to-pdf',  'title' => 'TXT to PDF',  'desc' => 'Convert plain text files to formatted PDF', 'icon' => '📝', 'category' => 'pdf'],
                ['slug' => 'docx-to-pdf', 'title' => 'DOCX to PDF', 'desc' => 'Convert Word documents to PDF format', 'icon' => '📄', 'category' => 'pdf'],
                ['slug' => 'pdf-to-txt',  'title' => 'PDF to TXT',  'desc' => 'Extract all text content from PDF files', 'icon' => '📋', 'category' => 'pdf'],
                ['slug' => 'pdf-to-docx', 'title' => 'PDF to DOCX', 'desc' => 'Convert PDF files to editable Word documents', 'icon' => '📄', 'category' => 'pdf'],
                ['slug' => 'pdf-to-jpg',  'title' => 'PDF to JPG',  'desc' => 'Render PDF pages as high-quality JPEG images', 'icon' => '🖼️', 'category' => 'pdf'],
                ['slug' => 'pdf-to-png',  'title' => 'PDF to PNG',  'desc' => 'Render PDF pages as crisp PNG images', 'icon' => '🖼️', 'category' => 'pdf'],

                // Document conversions
                ['slug' => 'xlsx-to-csv', 'title' => 'XLSX to CSV', 'desc' => 'Convert Excel spreadsheets to CSV format', 'icon' => '📊', 'category' => 'document'],
                ['slug' => 'csv-to-xlsx', 'title' => 'CSV to XLSX', 'desc' => 'Convert CSV files to Excel spreadsheets', 'icon' => '📊', 'category' => 'document'],
            ];

            foreach ($tools as $i => $tool):
                $delay = $i * 0.04;
            ?>
                <div class="col-6 col-md-4 col-lg-3 tool-item" data-category="<?= $tool['category'] ?>">
                    <a href="convert.php?tool=<?= $tool['slug'] ?>" class="tool-card category-<?= $tool['category'] ?>" style="animation-delay: <?= $delay ?>s">
                        <div class="tool-card-icon"><?= $tool['icon'] ?></div>
                        <div class="tool-card-title"><?= $tool['title'] ?></div>
                        <div class="tool-card-desc"><?= $tool['desc'] ?></div>
                        <span class="tool-card-arrow">→</span>
                    </a>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>FileForge — All conversions run locally on your machine. No data leaves your computer.</p>
        </div>
    </footer>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Category filter
        function filterTools(category, btn) {
            // Update active tab
            document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
            btn.classList.add('active');

            // Filter cards
            const items = document.querySelectorAll('.tool-item');
            items.forEach((item, index) => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = '';
                    // Re-trigger animation
                    const card = item.querySelector('.tool-card');
                    card.style.animation = 'none';
                    card.offsetHeight; // force reflow
                    card.style.animation = `fadeInCard 0.4s ease-out ${index * 0.03}s forwards`;
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>
</body>

</html>