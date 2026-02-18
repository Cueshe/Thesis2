<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Extraction Debug Tool</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .upload-area {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            margin: 20px 0;
            cursor: pointer;
            transition: border-color 0.3s;
        }
        .upload-area:hover {
            border-color: #007bff;
        }
        .upload-area.dragover {
            border-color: #007bff;
            background: #f0f8ff;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .results {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .preview {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 300px;
            overflow-y: auto;
        }
        .file-info {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .method-result {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .method-result h4 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .loading {
            text-align: center;
            padding: 20px;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 PDF Extraction Debug Tool</h1>
        <p>This tool helps you debug PDF text extraction issues. Upload your PDF to see exactly what text is being extracted.</p>
        
        <div class="file-info">
            <h3>📋 System Check</h3>
            <button onclick="checkSystem()" class="btn">Check System</button>
            <div id="systemInfo" class="hidden"></div>
        </div>
        
        <div class="upload-area" onclick="document.getElementById('pdfFile').click()" ondrop="handleDrop(event)" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
            <input type="file" id="pdfFile" accept=".pdf" style="display: none;" onchange="handleFileSelect(event)">
            <h3>📄 Upload PDF for Debugging</h3>
            <p>Click here or drag and drop your PDF file</p>
            <p><small>Maximum file size: 10MB</small></p>
        </div>
        
        <div id="fileInfo" class="hidden file-info">
            <h4>📁 File Information</h4>
            <div id="fileDetails"></div>
            <button onclick="debugPDF()" class="btn">🔍 Debug Extraction</button>
        </div>
        
        <div id="loading" class="loading hidden">
            <p>🔄 Analyzing PDF... This may take a few seconds.</p>
        </div>
        
        <div id="results" class="hidden"></div>
    </div>

    <script>
        let selectedFile = null;

        function checkSystem() {
            const systemInfo = document.getElementById('systemInfo');
            systemInfo.innerHTML = '<p>🔄 Checking system...</p>';
            systemInfo.classList.remove('hidden');
            
            fetch('/test/pdf-info')
                .then(response => response.json())
                .then(data => {
                    let html = '<h4>✅ System Status</h4>';
                    html += '<ul>';
                    html += `<li>PHP Version: ${data.php_version}</li>`;
                    html += '<li>Extensions:';
                    html += `<ul>
                        <li>zlib: ${data.extensions.zlib ? '✅' : '❌'}</li>
                        <li>iconv: ${data.extensions.iconv ? '✅' : '❌'}</li>
                        <li>mbstring: ${data.extensions.mbstring ? '✅' : '❌'}</li>
                        <li>fileinfo: ${data.extensions.fileinfo ? '✅' : '❌'}</li>
                    </ul></li>`;
                    html += `<li>PDF Parser: ${data.composer_packages['smalot/pdfparser'] ? '✅' : '❌'}</li>`;
                    html += `<li>Storage Writable: ${data.storage_writable ? '✅' : '❌'}</li>`;
                    html += `<li>Public Storage Writable: ${data.public_storage_writable ? '✅' : '❌'}</li>`;
                    html += '</ul>';
                    
                    systemInfo.innerHTML = html;
                    systemInfo.className = 'file-info success';
                })
                .catch(error => {
                    systemInfo.innerHTML = `<p>❌ Error: ${error.message}</p>`;
                    systemInfo.className = 'file-info error';
                });
        }

        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                selectedFile = file;
                showFileInfo(file);
            }
        }

        function handleDrop(event) {
            event.preventDefault();
            event.target.classList.remove('dragover');
            
            const file = event.dataTransfer.files[0];
            if (file && file.type === 'application/pdf') {
                selectedFile = file;
                showFileInfo(file);
            } else {
                alert('Please upload a PDF file');
            }
        }

        function handleDragOver(event) {
            event.preventDefault();
            event.target.classList.add('dragover');
        }

        function handleDragLeave(event) {
            event.target.classList.remove('dragover');
        }

        function showFileInfo(file) {
            const fileInfo = document.getElementById('fileInfo');
            const fileDetails = document.getElementById('fileDetails');
            
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            
            fileDetails.innerHTML = `
                <p><strong>Name:</strong> ${file.name}</p>
                <p><strong>Size:</strong> ${fileSize} MB</p>
                <p><strong>Type:</strong> ${file.type}</p>
                <p><strong>Last Modified:</strong> ${new Date(file.lastModified).toLocaleString()}</p>
            `;
            
            fileInfo.classList.remove('hidden');
            document.getElementById('results').classList.add('hidden');
        }

        function debugPDF() {
            if (!selectedFile) {
                alert('Please select a PDF file first');
                return;
            }
            
            const loading = document.getElementById('loading');
            const results = document.getElementById('results');
            
            loading.classList.remove('hidden');
            results.classList.add('hidden');
            
            const formData = new FormData();
            formData.append('pdf_file', selectedFile);
            
            fetch('/test/pdf-debug', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                loading.classList.add('hidden');
                results.classList.remove('hidden');
                
                if (data.success) {
                    displayResults(data.debug_results);
                } else {
                    results.innerHTML = `<div class="results error">
                        <h3>❌ Debug Failed</h3>
                        <p>${data.error}</p>
                    </div>`;
                }
            })
            .catch(error => {
                loading.classList.add('hidden');
                results.classList.remove('hidden');
                results.innerHTML = `<div class="results error">
                    <h3>❌ Network Error</h3>
                    <p>${error.message}</p>
                </div>`;
            });
        }

        function displayResults(results) {
            let html = '<div class="results success">';
            html += '<h3>🔍 PDF Extraction Results</h3>';
            
            // File info
            html += '<div class="method-result">';
            html += '<h4>📁 File Information</h4>';
            html += `<p>Name: ${results.file_info.name}</p>`;
            html += `<p>Size: ${(results.file_info.size / 1024 / 1024).toFixed(2)} MB</p>`;
            html += `<p>Pages: ${results.pages_count}</p>`;
            html += '</div>';
            
            // Extraction methods
            for (const [method, data] of Object.entries(results.extractions)) {
                html += `<div class="method-result">`;
                html += `<h4>🔧 Method: ${method}</h4>`;
                
                if (data.success) {
                    html += `<p>✅ Success - Extracted ${data.length} characters</p>`;
                    
                    if (data.full_text) {
                        html += '<h5>Full Text:</h5>';
                        html += `<div class="preview">${escapeHtml(data.full_text)}</div>`;
                    }
                    
                    if (data.pages) {
                        html += '<h5>Page by Page:</h5>';
                        data.pages.forEach(page => {
                            html += `<div style="margin: 10px 0; padding: 10px; background: #f1f1f1; border-radius: 3px;">`;
                            html += `<p><strong>Page ${page.page_number}:</strong> ${page.length} characters</p>`;
                            html += `<div class="preview">${escapeHtml(page.preview)}</div>`;
                            html += `</div>`;
                        });
                    }
                } else {
                    html += `<p>❌ Failed: ${data.error}</p>`;
                }
                
                html += '</div>';
            }
            
            html += '</div>';
            
            document.getElementById('results').innerHTML = html;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
