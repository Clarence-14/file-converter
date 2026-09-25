# FileForge — Offline File Converter

[![License](https://img.shields.io/badge/License-Apache_2.0-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.x%20%7C%207.4+-777bb4.svg)](https://www.php.net/)
[![Python](https://img.shields.io/badge/Python-3.10%2B-3776ab.svg)](https://www.python.org/)

**FileForge** is an offline, privacy-focused file conversion web application. It allows you to convert images, documents, and PDF files locally on your machine without uploading sensitive files to cloud services or third-party servers.

---

## Highlights

- **100% Offline & Private**: All conversions run entirely on your local machine using Python. No internet connection required, no cloud uploads, and zero data tracking.
- **Modern Dark UI**: Glassmorphic styling, smooth micro-animations, drag-and-drop file uploader, real-time progress indicators, and instant downloads.
- **Broad Format Support**: Convert across 25+ popular file formats spanning images, spreadsheets, text documents, and PDFs.
- **Self-Hosted**: Runs seamlessly on local server stacks like XAMPP, WAMP, or standalone Apache/Nginx + PHP.

---

## Supported Formats & Conversions

### Image Conversions
| Conversion | Description |
| :--- | :--- |
| **JPG to PNG** | Convert JPEG to PNG with transparency support |
| **PNG to JPG** | Compress PNG images into universal JPEG |
| **JPG to WEBP** | Convert JPEG to modern lightweight WebP |
| **WEBP to JPG** | Convert WebP images back to standard JPEG |
| **PNG to WEBP** | Compress PNG into modern lightweight WebP |
| **WEBP to PNG** | Convert WebP to lossless PNG format |
| **BMP to JPG / PNG** | Convert Windows Bitmap images to JPG or PNG |
| **JPG / PNG to BMP** | Convert JPEG or PNG images to Bitmap |
| **TIFF to JPG / PNG** | Convert multi-layer or raw TIFF images to JPG or PNG |
| **GIF to PNG / JPG** | Extract static frames from GIF animations |
| **PNG to GIF** | Convert PNG images to GIF format |
| **JPG / PNG to ICO** | Generate multi-size favicon `.ico` files |

### PDF Conversions
| Conversion | Description |
| :--- | :--- |
| **JPG / PNG / WEBP / BMP to PDF** | Bundle single or multiple image formats into PDF documents |
| **TXT to PDF** | Convert formatted text files directly to clean PDF pages |
| **DOCX to PDF** | Convert Microsoft Word documents into PDF |
| **PDF to TXT** | Extract raw text content from PDF documents |
| **PDF to DOCX** | Convert PDF files into editable Microsoft Word documents |
| **PDF to JPG / PNG** | Render individual PDF pages as high-resolution images |

### Document & Spreadsheet Conversions
| Conversion | Description |
| :--- | :--- |
| **XLSX to CSV** | Export Excel spreadsheets to standard comma-separated text |
| **CSV to XLSX** | Import CSV datasets into formatted Excel spreadsheets |

---

## Project Architecture

```
file-converter/
├── api/
│   ├── download.php        # Secure local file download handler
│   └── upload.php          # Upload validator, dispatcher & Python caller
├── assets/
│   ├── css/
│   │   └── style.css       # Custom design system & animations
│   ├── js/
│   │   └── app.js          # Drag-and-drop, AJAX upload, and progress UI
│   └── vendor/             # Bootstrap 5 and UI dependencies
├── converted/              # Destination directory for processed files
├── uploads/                # Temporary directory for uploaded files
├── python/
│   ├── converter.py        # CLI dispatcher invoked by PHP backend
│   ├── document_converter.py# Document & PDF conversion routines
│   ├── image_converter.py   # Pillow-based image conversion routines
│   └── requirements.txt    # Python dependencies
├── convert.php             # Conversion workspace page for individual tools
├── index.php               # Homepage with tool categories and filter tabs
├── LICENSE                 # Apache License 2.0
└── README.md               # Project documentation
```

---

## Getting Started

### Prerequisites

- **Web Server**: [XAMPP](https://www.apachefriends.org/), WAMP, or any Apache/Nginx environment with **PHP 7.4+** or **PHP 8.x**.
- **Python**: **Python 3.10+** installed and accessible via your system's `PATH`.
- *(Optional)* **Microsoft Word**: Required on Windows if performing native high-fidelity DOCX→PDF conversion via `docx2pdf`.

### Installation Steps

1. **Clone or move the project into your web root**:
   ```bash
   # For XAMPP on Windows:
   cd C:\xampp\htdocs\
   git clone https://github.com/Clarence-14/file-converter.git
   ```

2. **Install Python dependencies**:
   Open a terminal or PowerShell in the project directory and install the required Python packages:
   ```bash
   cd C:\xampp\htdocs\file-converter\python
   python -m pip install -r requirements.txt
   ```

3. **Verify directory permissions**:
   Ensure the `uploads/` and `converted/` folders exist and are writable by the web server process.

4. **Start Apache**:
   - Open **XAMPP Control Panel** and start the **Apache** module.

5. **Launch in Browser**:
   Navigate to:
   ```text
   http://localhost/file-converter
   ```

---

## Configuration

In [api/upload.php](api/upload.php), you can customize default settings:

```php
// Path to your Python binary (use 'python', 'python3', or an absolute path)
$pythonPath = 'python';

// Maximum upload file size (default: 50 MB)
$maxFileSize = 50 * 1024 * 1024;
```

---

## License

This project is licensed under the **Apache License 2.0**. See the [LICENSE](LICENSE) file for details.
