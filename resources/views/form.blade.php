{{-- resources/views/transcription/form.blade.php --}}
<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>آڈیو ٹرانککرپشن سسٹم</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Noto Sans Arabic', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .language-option {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }
        
        .language-option:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }
        
        .language-option.active {
            border-color: #667eea;
            background: #f0f4ff;
        }
        
        .result-box {
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px dashed #dee2e6;
            min-height: 200px;
            padding: 20px;
        }
        
        .confidence-meter {
            height: 10px;
            background: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .confidence-fill {
            height: 100%;
            border-radius: 5px;
            transition: width 1s ease;
        }
        
        .audio-player {
            width: 100%;
            margin: 15px 0;
        }
        
        .loading {
            display: none;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .urdu-text {
            font-size: 1.2rem;
            line-height: 2;
            text-align: right;
        }
        
        .roman-text {
            font-size: 1rem;
            line-height: 1.8;
            color: #666;
            font-style: italic;
        }
        
        .file-info {
            background: #f0f8ff;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
        }
        
        /* RTL Support */
        .rtl {
            direction: rtl;
            text-align: right;
        }
        
        .form-label {
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body class="rtl">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="text-center mb-5">
                    <h1 class="text-white mb-3">آڈیو ٹرانککرپشن سسٹم</h1>
                    <p class="text-white-50">آڈیو فائل اپ لوڈ کریں اور اردو متن حاصل کریں</p>
                </div>
                
                <!-- Main Card -->
                <div class="card">
                    <div class="card-body p-5">
                        <!-- Upload Form -->
                        <form id="transcriptionForm" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Step 1: File Upload -->
                            <div class="mb-4" id="step1">
                                <h4 class="mb-4 text-primary">
                                    <i class="fas fa-file-audio me-2"></i>قدم ۱: آڈیو فائل منتخب کریں
                                </h4>
                                
                                <div class="mb-4">
                                    <label class="form-label">فائل اپ لوڈ کریں:</label>
                                    <div class="input-group">
                                        <input type="file" 
                                               class="form-control form-control-lg" 
                                               id="audioFile" 
                                               name="audio"
                                               accept="audio/*,.mp3,.wav,.m4a,.ogg,.flac"
                                               required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('audioFile').click()">
                                            <i class="fas fa-folder-open"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">
                                        قبول شدہ فارمیٹس: MP3, WAV, M4A, OGG, FLAC (زیادہ سے زیادہ سائز: 10MB)
                                    </div>
                                </div>
                                
                                <!-- Or Audio URL -->
                                <div class="mb-4">
                                    <label class="form-label">یا آڈیو لنک درج کریں:</label>
                                    <input type="url" 
                                           class="form-control" 
                                           id="audioUrl" 
                                           placeholder="https://example.com/audio.mp3">
                                    <div class="form-text">
                                        ویب سے براہ راست آڈیو فائل کا لنک
                                    </div>
                                </div>
                                
                                <!-- Audio Preview -->
                                <div id="audioPreview" class="mb-4" style="display: none;">
                                    <label class="form-label">آڈیو کا پیش نظارہ:</label>
                                    <audio id="audioPlayer" controls class="audio-player">
                                        آپ کا براؤزر آڈیو سپورٹ نہیں کرتا
                                    </audio>
                                    <div id="fileInfo" class="file-info"></div>
                                </div>
                            </div>
                            
                            <!-- Step 2: Language Selection -->
                            <div class="mb-4" id="step2">
                                <h4 class="mb-4 text-primary">
                                    <i class="fas fa-language me-2"></i>قدم ۲: زبان منتخب کریں
                                </h4>
                                
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="language-option text-center" onclick="selectLanguage('ur')" id="langUr">
                                            <i class="fas fa-flag fa-3x mb-3 text-success"></i>
                                            <h5>اردو</h5>
                                            <p>Urdu Transcription</p>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="language" value="ur" checked>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="language-option text-center" onclick="selectLanguage('en')" id="langEn">
                                            <i class="fas fa-flag-usa fa-3x mb-3 text-primary"></i>
                                            <h5>انگریزی</h5>
                                            <p>English Transcription</p>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="language" value="en">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="language-option text-center" onclick="selectLanguage('hi')" id="langHi">
                                            <i class="fas fa-flag fa-3x mb-3 text-warning"></i>
                                            <h5>ہندی</h5>
                                            <p>Hindi Transcription</p>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="language" value="hi">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Step 3: Advanced Options -->
                            <div class="mb-4" id="step3">
                                <h4 class="mb-4 text-primary">
                                    <i class="fas fa-cogs me-2"></i>قدم ۳: اضافی اختیارات
                                </h4>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">ماڈل سائز:</label>
                                        <select class="form-select" name="model_size" id="modelSize">
                                            <option value="tiny">Tiny (سب سے تیز)</option>
                                            <option value="base" selected>Base (متوازن)</option>
                                            <option value="small">Small (بہتر معیار)</option>
                                            <option value="medium">Medium (اعلی معیار)</option>
                                            <option value="large">Large (بہترین معیار)</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="form-label">آؤٹ پٹ فارمیٹ:</label>
                                        <select class="form-select" name="output_format" id="outputFormat">
                                            <option value="both" selected>اردو + رومن</option>
                                            <option value="urdu">صرف اردو</option>
                                            <option value="roman">صرف رومن</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="showConfidence" checked>
                                        <label class="form-check-label" for="showConfidence">
                                            اعتماد کی سطح دکھائیں
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="autoPlay" checked>
                                        <label class="form-check-label" for="autoPlay">
                                            آڈیو خودبخود چلائیں
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="text-center mt-5">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-magic me-2"></i>ٹرانککرپشن شروع کریں
                                </button>
                                
                                <button type="button" class="btn btn-outline-secondary btn-lg ms-2" onclick="resetForm()">
                                    <i class="fas fa-redo me-2"></i>دوبارہ شروع کریں
                                </button>
                            </div>
                        </form>
                        
                        <!-- Loading Spinner -->
                        <div class="text-center my-5 loading" id="loading">
                            <div class="spinner mx-auto mb-3"></div>
                            <h5>ٹرانککرپشن ہو رہی ہے...</h5>
                            <p class="text-muted">براہ کرم انتظار کریں، یہ کچھ وقت لے سکتا ہے</p>
                            <div class="progress mt-3" style="height: 10px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     style="width: 100%;"></div>
                            </div>
                        </div>
                        
                        <!-- Results Section -->
                        <div id="resultsSection" style="display: none;">
                            <hr class="my-5">
                            
                            <h4 class="mb-4 text-success">
                                <i class="fas fa-check-circle me-2"></i>ٹرانککرپشن کا نتیجہ
                            </h4>
                            
                            <!-- Confidence Meter -->
                            <div class="mb-4" id="confidenceSection">
                                <label class="form-label">اعتماد کی سطح:</label>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="confidence-meter flex-grow-1 me-3">
                                        <div class="confidence-fill bg-success" id="confidenceFill"></div>
                                    </div>
                                    <span class="fw-bold" id="confidencePercent">0%</span>
                                </div>
                            </div>
                            
                            <!-- Urdu Text Result -->
                            <div class="mb-4">
                                <label class="form-label">اردو متن:</label>
                                <div class="result-box urdu-text" id="urduResult">
                                    -- نتیجہ یہاں ظاہر ہوگا --
                                </div>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('urduResult')">
                                        <i class="fas fa-copy me-1"></i>کاپی کریں
                                    </button>
                                    <button class="btn btn-sm btn-outline-success ms-2" onclick="speakText('urdu')">
                                        <i class="fas fa-volume-up me-1"></i>سنائیں
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Roman Text Result -->
                            <div class="mb-4" id="romanSection">
                                <label class="form-label">رومن متن:</label>
                                <div class="result-box roman-text" id="romanResult">
                                    -- Roman text will appear here --
                                </div>
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('romanResult')">
                                        <i class="fas fa-copy me-1"></i>کاپی کریں
                                    </button>
                                    <button class="btn btn-sm btn-outline-success ms-2" onclick="speakText('roman')">
                                        <i class="fas fa-volume-up me-1"></i>سنائیں
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Download Options -->
                            <div class="mb-4">
                                <label class="form-label">نتیجہ ڈاؤن لوڈ کریں:</label>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-success" onclick="downloadResult('txt')">
                                        <i class="fas fa-file-alt me-1"></i>TXT فائل
                                    </button>
                                    <button class="btn btn-info" onclick="downloadResult('json')">
                                        <i class="fas fa-file-code me-1"></i>JSON فائل
                                    </button>
                                    <button class="btn btn-warning" onclick="downloadResult('pdf')">
                                        <i class="fas fa-file-pdf me-1"></i>PDF فائل
                                    </button>
                                </div>
                            </div>
                            
                            <!-- History -->
                            <div class="mt-5">
                                <h5 class="mb-3">
                                    <i class="fas fa-history me-2"></i>حالیہ ٹرانککرپشنز
                                </h5>
                                <div class="list-group" id="historyList">
                                    <!-- History items will be added here dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="text-center mt-4">
                    <p class="text-white">
                        <i class="fas fa-code me-2"></i>
                        Whisper AI پر مبنی ٹرانککرپشن سسٹم
                    </p>
                    <p class="text-white-50 small">
                        © 2024 آڈیو ٹرانککرپشن سسٹم | تمام حقوق محفوظ ہیں
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    
    <script>
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            selectLanguage('ur'); // Default language
            loadHistory();
        });
        
        // File Upload Preview
        document.getElementById('audioFile').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                previewAudio(file);
            }
        });
        
        // Language Selection
        function selectLanguage(lang) {
            // Remove active class from all
            document.querySelectorAll('.language-option').forEach(option => {
                option.classList.remove('active');
            });
            
            // Add active class to selected
            document.getElementById(`lang${lang.charAt(0).toUpperCase() + lang.slice(1)}`).classList.add('active');
            
            // Update radio button
            document.querySelector(`input[name="language"][value="${lang}"]`).checked = true;
        }
        
        // Audio Preview Function
        function previewAudio(file) {
            const audioPreview = document.getElementById('audioPreview');
            const audioPlayer = document.getElementById('audioPlayer');
            const fileInfo = document.getElementById('fileInfo');
            
            if (file.size > 10 * 1024 * 1024) { // 10MB limit
                alert('فائل کا سائز 10MB سے زیادہ نہیں ہونا چاہیے!');
                document.getElementById('audioFile').value = '';
                return;
            }
            
            // Show preview section
            audioPreview.style.display = 'block';
            
            // Set audio source
            const url = URL.createObjectURL(file);
            audioPlayer.src = url;
            
            // Show file info
            const fileSize = (file.size / (1024 * 1024)).toFixed(2);
            fileInfo.innerHTML = `
                <strong>فائل کی معلومات:</strong><br>
                نام: ${file.name}<br>
                سائز: ${fileSize} MB<br>
                قسم: ${file.type}
            `;
            
            // Auto-play if enabled
            if (document.getElementById('autoPlay').checked) {
                audioPlayer.play().catch(e => console.log('Auto-play prevented:', e));
            }
        }
        
        // Form Submission
     // Complete fixed form submission with error handling
async function submitTranscriptionForm(formData) {
    try {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        // Show loading
        document.getElementById('loading').style.display = 'block';
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerHTML = 
            '<i class="fas fa-spinner fa-spin me-2"></i>جاری ہے...';
        
        // Send request
        const response = await fetch('/transcribe/upload', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        });
        
        // Get response as text first
        const responseText = await response.text();
        
        // Debug: log response
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        console.log('Response first 500 chars:', responseText.substring(0, 500));
        
        // Check if response is HTML (starts with <!DOCTYPE)
        if (responseText.trim().startsWith('<!DOCTYPE') || 
            responseText.trim().startsWith('<html')) {
            
            // This is an HTML error page, not JSON
            throw new Error(
                'سرور سے HTML جواب آیا۔ ممکنہ وجوہات:\n' +
                '1. CSRF token موجود نہیں\n' +
                '2. Authentication required\n' +
                '3. Route موجود نہیں\n' +
                '4. Server error'
            );
        }
        
        // Try to parse as JSON
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (parseError) {
            console.error('JSON parse error:', parseError);
            throw new Error('سرور سے غلط فارمیٹ کا جواب: ' + responseText.substring(0, 200));
        }
        
        // Check if successful
        if (!response.ok) {
            throw new Error(data.error || `سرور خرابی: ${response.status}`);
        }
        
        if (data.success) {
            // Show results
            showResults(data);
            addToHistory(data);
            
            // Success message
            showToast('کامیابی', 'ٹرانککرپشن مکمل ہو گئی!', 'success');
            
        } else {
            throw new Error(data.error || 'ٹرانککرپشن ناکام ہوئی');
        }
        
        return data;
        
    } catch (error) {
        console.error('Transcription error:', error);
        
        // Show error to user
        showToast('خرابی', error.message, 'error');
        
        // Also show in alert
        alert(`ٹرانککرپشن میں خرابی:\n\n${error.message}\n\nبراہ کرم کنسول دیکھیں مزید تفصیلات کے لیے۔`);
        
        throw error;
        
    } finally {
        // Hide loading
        document.getElementById('loading').style.display = 'none';
        document.getElementById('submitBtn').disabled = false;
        document.getElementById('submitBtn').innerHTML = 
            '<i class="fas fa-magic me-2"></i>ٹرانککرپشن شروع کریں';
    }
}

// Toast notification function
function showToast(title, message, type = 'info') {
    // Remove existing toast
    const existingToast = document.getElementById('transcriptionToast');
    if (existingToast) {
        existingToast.remove();
    }
    
    // Create toast
    const toast = document.createElement('div');
    toast.id = 'transcriptionToast';
    toast.className = `toast align-items-center text-bg-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <strong>${title}:</strong> ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(toast);
    
    // Show toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}

// Updated form submission handler
document.getElementById('transcriptionForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Create FormData
    const formData = new FormData(this);
    
    // Get audio file
    const audioFile = document.getElementById('audioFile').files[0];
    if (!audioFile && !document.getElementById('audioUrl').value) {
        showToast('انتباہ', 'براہ کرم آڈیو فائل منتخب کریں یا لنک دیں', 'warning');
        return;
    }
    
    // Validate file size
    if (audioFile && audioFile.size > 10 * 1024 * 1024) { // 10MB
        showToast('خرابی', 'فائل کا سائز 10MB سے زیادہ نہیں ہونا چاہیے', 'error');
        return;
    }
    
    // Add additional parameters
    formData.append('model_size', document.getElementById('modelSize').value);
    formData.append('output_format', document.getElementById('outputFormat').value);
    
    // Submit form
    await submitTranscriptionForm(formData);
});

// Test endpoint function
async function testEndpoint() {
    try {
        const response = await fetch('/transcribe/upload', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ test: true })
        });
        
        const text = await response.text();
        console.log('Test response:', text.substring(0, 500));
        
        return response.ok;
        
    } catch (error) {
        console.error('Test error:', error);
        return false;
    }
}

// Add test button to debug
document.addEventListener('DOMContentLoaded', function() {
    // Add debug button
    const debugBtn = document.createElement('button');
    debugBtn.className = 'btn btn-sm btn-warning position-fixed';
    debugBtn.style.bottom = '10px';
    debugBtn.style.right = '10px';
    debugBtn.style.zIndex = '1000';
    debugBtn.innerHTML = '<i class="fas fa-bug me-1"></i>ڈیبگ';
    debugBtn.onclick = async function() {
        const result = await testEndpoint();
        alert('Endpoint test: ' + (result ? 'Working' : 'Failed - Check console'));
    };
    
    document.body.appendChild(debugBtn);
});
        // Show Results
        function showResults(data) {
            // Update confidence
            const confidence = data.confidence * 100;
            document.getElementById('confidenceFill').style.width = `${confidence}%`;
            document.getElementById('confidencePercent').textContent = `${confidence.toFixed(1)}%`;
            
            // Show/hide confidence based on checkbox
            document.getElementById('confidenceSection').style.display = 
                document.getElementById('showConfidence').checked ? 'block' : 'none';
            
            // Update Urdu text
            document.getElementById('urduResult').textContent = data.urdu_text || 'کوئی متن نہیں ملا';
            
            // Update Roman text
            const romanSection = document.getElementById('romanSection');
            const romanResult = document.getElementById('romanResult');
            const outputFormat = document.getElementById('outputFormat').value;
            
            if (outputFormat === 'urdu') {
                romanSection.style.display = 'none';
            } else {
                romanSection.style.display = 'block';
                romanResult.textContent = data.roman_text || '';
            }
            
            // Show results section
            document.getElementById('resultsSection').style.display = 'block';
            
            // Scroll to results
            document.getElementById('resultsSection').scrollIntoView({ behavior: 'smooth' });
        }
        
        // Copy to Clipboard
        function copyToClipboard(elementId) {
            const text = document.getElementById(elementId).textContent;
            navigator.clipboard.writeText(text)
                .then(() => alert('متن کاپی ہو گیا!'))
                .catch(err => alert('کاپی کرنے میں خرابی: ' + err));
        }
        
        // Text to Speech
        function speakText(type) {
            const text = type === 'urdu' 
                ? document.getElementById('urduResult').textContent
                : document.getElementById('romanResult').textContent;
            
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = type === 'urdu' ? 'ur-PK' : 'en-US';
                speechSynthesis.speak(utterance);
            } else {
                alert('آپ کا براؤزر ٹیکسٹ ٹو اسپیچ سپورٹ نہیں کرتا');
            }
        }
        
        // Download Result
        function downloadResult(format) {
            const urduText = document.getElementById('urduResult').textContent;
            const romanText = document.getElementById('romanResult').textContent;
            const confidence = document.getElementById('confidencePercent').textContent;
            
            let content, filename, mimeType;
            
            switch(format) {
                case 'txt':
                    content = `اردو متن:\n${urduText}\n\nرومن متن:\n${romanText}\n\nاعتماد کی سطح: ${confidence}`;
                    filename = 'transcription-result.txt';
                    mimeType = 'text/plain';
                    break;
                    
                case 'json':
                    content = JSON.stringify({
                        urdu_text: urduText,
                        roman_text: romanText,
                        confidence: confidence,
                        timestamp: new Date().toISOString()
                    }, null, 2);
                    filename = 'transcription-result.json';
                    mimeType = 'application/json';
                    break;
                    
                case 'pdf':
                    // For PDF, you would need a PDF generation library
                    alert('PDF ڈاؤن لوڈ فیچر جلد ہی دستیاب ہوگا!');
                    return;
            }
            
            const blob = new Blob([content], { type: mimeType });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
        
        // History Management
        function addToHistory(data) {
            const history = JSON.parse(localStorage.getItem('transcriptionHistory') || '[]');
            
            history.unshift({
                id: Date.now(),
                urdu_text: data.urdu_text.substring(0, 100) + '...',
                timestamp: new Date().toLocaleString('ur-PK'),
                confidence: data.confidence
            });
            
            // Keep only last 10 items
            if (history.length > 10) {
                history.pop();
            }
            
            localStorage.setItem('transcriptionHistory', JSON.stringify(history));
            loadHistory();
        }
        
        function loadHistory() {
            const history = JSON.parse(localStorage.getItem('transcriptionHistory') || '[]');
            const historyList = document.getElementById('historyList');
            
            historyList.innerHTML = '';
            
            if (history.length === 0) {
                historyList.innerHTML = '<div class="text-center text-muted p-3">کوئی حالیہ ٹرانککرپشن نہیں</div>';
                return;
            }
            
            history.forEach(item => {
                const historyItem = document.createElement('div');
                historyItem.className = 'list-group-item';
                historyItem.innerHTML = `
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>${item.urdu_text}</strong><br>
                            <small class="text-muted">${item.timestamp}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success">${(item.confidence * 100).toFixed(1)}%</span>
                        </div>
                    </div>
                `;
                historyList.appendChild(historyItem);
            });
        }
        
        function clearHistory() {
            if (confirm('کیا آپ واقعی ساری تاریخ مٹانا چاہتے ہیں؟')) {
                localStorage.removeItem('transcriptionHistory');
                loadHistory();
            }
        }
        
        // Reset Form
        function resetForm() {
            document.getElementById('transcriptionForm').reset();
            document.getElementById('audioPreview').style.display = 'none';
            document.getElementById('resultsSection').style.display = 'none';
            selectLanguage('ur');
            document.getElementById('submitBtn').disabled = false;
        }
        
        // Add clear history button to the history section
        document.addEventListener('DOMContentLoaded', function() {
            const historySection = document.querySelector('#resultsSection .mt-5');
            if (historySection) {
                const clearBtn = document.createElement('button');
                clearBtn.className = 'btn btn-sm btn-outline-danger float-start';
                clearBtn.innerHTML = '<i class="fas fa-trash me-1"></i>تاریخ صاف کریں';
                clearBtn.onclick = clearHistory;
                historySection.querySelector('h5').appendChild(clearBtn);
            }
        });
    
    // JavaScript میں debug function شامل کریں
function debugFileUpload() {
    const fileInput = document.getElementById('audioFile');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('Please select a file first');
        return;
    }
    
    console.log('File details:', {
        name: file.name,
        size: file.size,
        type: file.type,
        lastModified: new Date(file.lastModified)
    });
    
    // Create FormData for test
    const formData = new FormData();
    formData.append('audio', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
    
    // Test upload
    fetch('/transcribe/test-upload', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Test upload response:', data);
        alert('Test upload: ' + (data.success ? 'SUCCESS' : 'FAILED') + 
              '\nCheck console for details');
    })
    .catch(error => {
        console.error('Test upload error:', error);
        alert('Test upload failed: ' + error.message);
    });
}

// Check system status
function checkSystemStatus() {
    fetch('/transcribe/status')
    .then(response => response.json())
    .then(data => {
        console.log('System status:', data);
        
        let message = 'System Status:\n\n';
        Object.entries(data.checks).forEach(([key, value]) => {
            message += `${key}: ${value}\n`;
        });
        
        alert(message);
    })
    .catch(error => {
        console.error('Status check error:', error);
        alert('Failed to check system status');
    });
}

// Add debug buttons to form
document.addEventListener('DOMContentLoaded', function() {
    // Add debug panel
    const debugPanel = document.createElement('div');
    debugPanel.className = 'card mt-3';
    debugPanel.innerHTML = `
        <div class="card-header bg-warning">
            <h6 class="mb-0"><i class="fas fa-bug me-2"></i>ڈیبگ پینل</h6>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-info" onclick="debugFileUpload()">
                    <i class="fas fa-upload me-1"></i>فائل اپ لوڈ ٹیسٹ
                </button>
                <button class="btn btn-sm btn-info" onclick="checkSystemStatus()">
                    <i class="fas fa-server me-1"></i>سسٹم اسٹیٹس
                </button>
                <button class="btn btn-sm btn-danger" onclick="clearForm()">
                    <i class="fas fa-trash me-1"></i>فارم صاف کریں
                </button>
            </div>
            <div id="debugOutput" class="mt-2 small text-muted"></div>
        </div>
    `;
    
    // Insert after form
    const formCard = document.querySelector('.card');
    formCard.parentNode.insertBefore(debugPanel, formCard.nextSibling);
    
    // Update form submission to show debug info
    const originalSubmit = document.getElementById('transcriptionForm').onsubmit;
    document.getElementById('transcriptionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('audioFile');
        const file = fileInput.files[0];
        
        if (file) {
            console.log('Submitting file:', {
                name: file.name,
                size: (file.size / (1024*1024)).toFixed(2) + ' MB',
                type: file.type
            });
            
            // Update debug panel
            document.getElementById('debugOutput').innerHTML = 
                `فائل: ${file.name}<br>سائز: ${(file.size / (1024*1024)).toFixed(2)} MB`;
        }
        
        // Call original submit function
        submitTranscriptionForm(new FormData(this));
    });
});

// Clear form function
function clearForm() {
    document.getElementById('transcriptionForm').reset();
    document.getElementById('audioPreview').style.display = 'none';
    document.getElementById('resultsSection').style.display = 'none';
    document.getElementById('debugOutput').innerHTML = '';
    console.log('Form cleared');
}
    </script>
</body>
</html>