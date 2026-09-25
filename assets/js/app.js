/**
 * FileForge — Conversion Page Logic
 * Handles drag-and-drop, file upload, AJAX conversion, and download.
 */

document.addEventListener('DOMContentLoaded', () => {
    // DOM Elements
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const filePreviewName = document.getElementById('filePreviewName');
    const filePreviewSize = document.getElementById('filePreviewSize');
    const removeFileBtn = document.getElementById('removeFileBtn');
    const convertBtn = document.getElementById('convertBtn');
    const progressSection = document.getElementById('progressSection');
    const progressBarFill = document.getElementById('progressBarFill');
    const progressText = document.getElementById('progressText');
    const resultSection = document.getElementById('resultSection');
    const resultFilename = document.getElementById('resultFilename');
    const downloadBtn = document.getElementById('downloadBtn');
    const convertAnotherBtn = document.getElementById('convertAnotherBtn');
    const errorSection = document.getElementById('errorSection');
    const errorMessage = document.getElementById('errorMessage');
    const errorDetail = document.getElementById('errorDetail');

    let selectedFile = null;

    // --- Drag and Drop ---
    if (uploadZone) {
        ['dragenter', 'dragover'].forEach(evt => {
            uploadZone.addEventListener(evt, (e) => {
                e.preventDefault();
                e.stopPropagation();
                uploadZone.classList.add('drag-over');
            });
        });

        ['dragleave', 'drop'].forEach(evt => {
            uploadZone.addEventListener(evt, (e) => {
                e.preventDefault();
                e.stopPropagation();
                uploadZone.classList.remove('drag-over');
            });
        });

        uploadZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelect(files[0]);
            }
        });

        uploadZone.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                handleFileSelect(fileInput.files[0]);
            }
        });
    }

    // --- File Selection ---
    function handleFileSelect(file) {
        // Validate file size (50MB)
        if (file.size > 50 * 1024 * 1024) {
            showError('File too large', 'Maximum file size is 50MB');
            return;
        }

        selectedFile = file;

        // Show file preview
        filePreviewName.textContent = file.name;
        filePreviewSize.textContent = formatFileSize(file.size);
        filePreview.classList.add('visible');
        convertBtn.classList.add('visible');

        // Hide upload zone text indicators
        uploadZone.querySelector('.upload-zone-icon').style.display = 'none';
        uploadZone.querySelector('.upload-zone-text').innerHTML = `
            <h3>✓ File selected</h3>
            <p>Click to choose a different file</p>
        `;
    }

    // --- Remove File ---
    if (removeFileBtn) {
        removeFileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            resetUpload();
        });
    }

    // --- Convert ---
    if (convertBtn) {
        convertBtn.addEventListener('click', () => {
            if (!selectedFile) return;
            startConversion();
        });
    }

    // --- Convert Another ---
    if (convertAnotherBtn) {
        convertAnotherBtn.addEventListener('click', () => {
            resetAll();
        });
    }

    function startConversion() {
        const conversionType = document.getElementById('conversionType').value;

        // Show progress, hide other sections
        uploadZone.style.display = 'none';
        filePreview.classList.remove('visible');
        convertBtn.classList.remove('visible');
        errorSection.classList.remove('visible');
        resultSection.classList.remove('visible');
        progressSection.classList.add('visible');
        progressText.textContent = 'Uploading file...';
        progressBarFill.style.width = '0%';

        // Create form data
        const formData = new FormData();
        formData.append('file', selectedFile);
        formData.append('conversion_type', conversionType);

        // AJAX upload
        const xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const pct = Math.round((e.loaded / e.total) * 60); // 60% for upload
                progressBarFill.style.width = pct + '%';
                if (pct < 60) {
                    progressText.textContent = `Uploading... ${pct}%`;
                }
            }
        });

        xhr.addEventListener('load', () => {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Show 100% and then show result
                        progressBarFill.style.width = '100%';
                        progressText.textContent = 'Conversion complete!';

                        setTimeout(() => {
                            showResult(response);
                        }, 500);
                    } else {
                        showError('Conversion failed', response.error || 'Unknown error');
                    }
                } catch (e) {
                    showError('Server error', 'Invalid response from server');
                }
            } else {
                try {
                    const response = JSON.parse(xhr.responseText);
                    showError('Conversion failed', response.error || `Server error (${xhr.status})`);
                } catch (e) {
                    showError('Server error', `HTTP ${xhr.status}`);
                }
            }
        });

        xhr.addEventListener('error', () => {
            showError('Connection error', 'Could not connect to the server. Make sure Apache is running.');
        });

        xhr.addEventListener('abort', () => {
            showError('Upload cancelled', 'The upload was cancelled.');
        });

        // After upload starts, simulate conversion progress
        xhr.upload.addEventListener('loadend', () => {
            progressBarFill.style.width = '65%';
            progressText.textContent = 'Converting file...';

            // Animate progress while waiting for response
            let progress = 65;
            const interval = setInterval(() => {
                if (progress < 90) {
                    progress += Math.random() * 3;
                    progressBarFill.style.width = Math.min(progress, 90) + '%';
                }
            }, 300);

            // Store interval ID to clear later
            xhr._progressInterval = interval;
        });

        const originalOnLoad = xhr.onload;
        xhr.addEventListener('loadend', () => {
            if (xhr._progressInterval) {
                clearInterval(xhr._progressInterval);
            }
        });

        xhr.open('POST', 'api/upload.php');
        xhr.send(formData);
    }

    function showResult(response) {
        progressSection.classList.remove('visible');
        resultSection.classList.add('visible');
        resultFilename.textContent = response.filename;

        // Set download link
        downloadBtn.href = response.download_url;
        downloadBtn.download = response.filename;
    }

    function showError(title, detail) {
        progressSection.classList.remove('visible');
        errorSection.classList.add('visible');
        errorMessage.textContent = title;
        errorDetail.textContent = detail || '';

        // Show upload zone again after delay
        setTimeout(() => {
            uploadZone.style.display = '';
        }, 100);
    }

    function resetUpload() {
        selectedFile = null;
        fileInput.value = '';
        filePreview.classList.remove('visible');
        convertBtn.classList.remove('visible');

        // Restore upload zone
        uploadZone.querySelector('.upload-zone-icon').style.display = '';
        uploadZone.querySelector('.upload-zone-text').innerHTML = `
            <h3>Drop your file here</h3>
            <p>or <span class="browse-link">browse</span> to choose a file</p>
        `;
    }

    function resetAll() {
        resetUpload();
        uploadZone.style.display = '';
        progressSection.classList.remove('visible');
        resultSection.classList.remove('visible');
        errorSection.classList.remove('visible');
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }
});
