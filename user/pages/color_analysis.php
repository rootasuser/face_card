<?php
// Start session and check user authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../../pages/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filipino Beauty Virtual Color Analysis</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Color Thief Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/color-thief/2.3.2/color-thief.umd.js"></script>
    <!-- Face-api.js for face detection -->
    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/dist/face-api.min.js"></script>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-color: #6c5ce7;
            --text-dark: #2d3436;
            --text-light: #636e72;
            --border-radius: 15px;
            --shadow: 0 10px 30px rgba(0,0,0,0.1);
            --shadow-hover: 0 15px 40px rgba(0,0,0,0.15);
        }

        .container {
            margin-top: 50px;
        }

        .main-container {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 3rem;
            transition: all 0.3s ease;
        }

        .main-container:hover {
            box-shadow: var(--shadow-hover);
        }

        .header-section {
            text-align: center;
            margin-bottom: 3rem;
        }

        .header-title {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header-subtitle {
            color: var(--text-light);
            font-size: 1.1rem;
        }

        .upload-section {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: var(--border-radius);
            padding: 3rem;
            text-align: center;
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }

        .upload-section:hover {
            border-color: var(--accent-color);
            background: #f1f3f4;
        }

        .upload-section.dragover {
            border-color: var(--accent-color);
            background: rgba(108, 92, 231, 0.1);
        }

        .upload-icon {
            font-size: 3rem;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }

        .upload-text {
            color: var(--text-dark);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .upload-subtext {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .file-input {
            display: none;
        }

        .upload-btn {
            background: var(--primary-gradient);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .upload-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 92, 231, 0.4);
            color: white;
        }

        .preview-container {
            text-align: center;
            margin: 2rem 0;
            position: relative;
        }

        .preview-image {
            max-width: 100%;
            max-height: 400px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .preview-image:hover {
            transform: scale(1.02);
        }

        .analysis-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(108, 92, 231, 0.1);
            border-radius: var(--border-radius);
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .analysis-progress {
            width: 80%;
            height: 3px;
            background: rgba(255,255,255,0.3);
            border-radius: 2px;
            position: relative;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .analysis-progress::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--primary-gradient);
            animation: progress 3s ease-in-out infinite;
        }

        @keyframes progress {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .analysis-text {
            color: var(--accent-color);
            font-weight: 600;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }

        .results-section {
            display: none;
            animation: fadeInUp 0.8s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section-title {
            color: var(--text-dark);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--primary-gradient);
            border-radius: 2px;
        }

        .palette-container {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .palette-box {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
        }

        .palette-box:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-hover);
        }

        .palette-box::after {
            content: attr(data-hex);
            position: absolute;
            bottom: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--text-dark);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.7rem;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .palette-box:hover::after {
            opacity: 1;
        }

        .product-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .product-card:nth-child(1) { border-left-color: #e17055; }
        .product-card:nth-child(2) { border-left-color: #fd79a8; }
        .product-card:nth-child(3) { border-left-color: #fdcb6e; }

        .product-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .product-description {
            color: var(--text-light);
            font-size: 1rem;
            line-height: 1.5;
        }

        .product-swatch {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin: 0.5rem 0;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }

        .error-message {
            background: #fff3f3;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            display: none;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 2rem 1rem;
                margin: 1rem;
            }
            
            .header-title {
                font-size: 2rem;
            }
            
            .upload-section {
                padding: 2rem 1rem;
            }
            
            .palette-container {
                flex-wrap: wrap;
            }
            
            .palette-box {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="main-container">
                    <!-- Header Section -->
                    <div class="header-section">
                        <h1 class="header-title">Filipino Beauty Virtual Color Analysis</h1>
                        <p class="header-subtitle">Personalized makeup shade recommendations for your unique skin tone</p>
                    </div>

                    <!-- Upload Section -->
                    <div class="upload-section" id="uploadSection">
                        <div class="upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="upload-text">Upload Your Photo</div>
                        <div class="upload-subtext">Clear, front-facing photo without makeup, filters, or shadows (JPG, PNG)</div>
                        <button class="btn upload-btn" onclick="document.getElementById('imageInput').click()">
                            <i class="fas fa-camera"></i> Choose Image
                        </button>
                        <input type="file" id="imageInput" class="file-input" accept="image/jpeg,image/png">
                    </div>

                    <!-- Error Message -->
                    <div class="error-message" id="errorMessage">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span id="errorText"></span>
                    </div>

                    <!-- Preview Section -->
                    <div class="preview-container" id="previewContainer" style="display: none;">
                        <img id="previewImage" class="preview-image" crossorigin="anonymous">
                        <div class="analysis-overlay" id="analysisOverlay">
                            <div class="analysis-progress"></div>
                            <div class="analysis-text">
                                <i class="fas fa-search fa-spin"></i> <span id="analysisStep">Detecting facial features...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Results Section -->
                    <div class="results-section" id="resultsSection">
                        <div class="row mb-4">
                            <div class="col-12">
                                <h3 class="section-title">
                                    <i class="fas fa-palette"></i> Your Skin Tone Palette
                                </h3>
                                <div class="palette-container" id="paletteDisplay"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h3 class="section-title">
                                    <i class="fas fa-shopping-cart"></i> Recommended Products
                                </h3>
                                <div class="recommendations-grid" id="recommendations"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // DOM element references
        const imageInput = document.getElementById('imageInput');
        const previewContainer = document.getElementById('previewContainer');
        const previewImage = document.getElementById('previewImage');
        const analysisOverlay = document.getElementById('analysisOverlay');
        const analysisStep = document.getElementById('analysisStep');
        const resultsSection = document.getElementById('resultsSection');
        const paletteDisplay = document.getElementById('paletteDisplay');
        const recommendations = document.getElementById('recommendations');
        const uploadSection = document.getElementById('uploadSection');
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');

        let faceApiLoaded = false;

        // Load face-api.js models
        async function loadFaceApiModels() {
            try {
                await faceapi.nets.tinyFaceDetector.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/model/');
                await faceapi.nets.faceLandmark68Net.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/model/');
                faceApiLoaded = true;
            } catch (error) {
                console.error('Error loading face-api models:', error);
                showError('Failed to load face detection models. Using alternative analysis method.');
            }
        }

        // Initialize face-api on page load
        loadFaceApiModels();

        // Filipino beauty products database with seasonal color theory
        const products = {
            'Happy Skin': {
                foundation: [
                    { name: 'Off Duty Seamless Perfecting Liquid Foundation - Hazelnut Beige', hex: '#D7A68B', season: 'Autumn', price: 52.00, url: 'https://shopcosy.com.au/products/off-duty-seamless-perfecting-liquid-foundation' },
                    { name: 'Off Duty Seamless Perfecting Liquid Foundation - Natural Beige', hex: '#E0BCA4', season: 'Spring', price: 52.00, url: 'https://shopcosy.com.au/products/off-duty-seamless-perfecting-liquid-foundation' },
                    { name: 'Blurring Skin Tint SPF20 - Deep', hex: '#A67A5B', season: 'Autumn', price: 32.00, url: 'https://shopcosy.com.au/products/blurring-skin-tint-spf20' }
                ],
                blush: [
                    { name: 'Second Skin Melting Cream Blush - Skin-kissed', hex: '#F4A7B9', season: 'Spring', price: 30.00, url: 'https://shopcosy.com.au/products/second-skin-melting-cream-blush' },
                    { name: 'Second Skin Soft Matte Powder Blush - Blissful Bronze', hex: '#C68A7A', season: 'Autumn', price: 30.00, url: 'https://shopcosy.com.au/products/second-skin-soft-matte-powder-blush' }
                ],
                lipstick: [
                    { name: 'Water Lip & Cheek Tint - Breathe', hex: '#E57373', season: 'Summer', price: 20.00, url: 'https://shopcosy.com.au/products/water-lip-cheek-tint' }
                ]
            },
            'BLK Cosmetics': {
                foundation: [
                    { name: 'All-Day Matte Liquid Foundation - Medium', hex: '#C4967B', season: 'Autumn', price: 499.00, url: 'https://www.watsons.com.ph' },
                    { name: 'Airy Matte Tint - Muse', hex: '#D9A58A', season: 'Spring', price: 299.00, url: 'https://blkcosmetics.com.ph' }
                ],
                blush: [
                    { name: 'Creamy All Over Paint - Golden Hour', hex: '#F4A261', season: 'Autumn', price: 349.00, url: 'https://blkcosmetics.com.ph' }
                ],
                lipstick: [
                    { name: 'All-Day Intense Matte Lipstick - My Love', hex: '#B76E79', season: 'Summer', price: 299.00, url: 'https://www.watsons.com.ph' }
                ]
            },
            'Vice Cosmetics': {
                foundation: [
                    { name: 'Duo Finish Foundation - Medium', hex: '#B5896A', season: 'Autumn', price: 245.00, url: 'https://www.watsons.com.ph' }
                ],
                blush: [
                    { name: 'Water Gel Lip & Cheek Tint - Coral', hex: '#FF8080', season: 'Spring', price: 195.00, url: 'https://vicecosmetics.com.ph' }
                ],
                lipstick: [
                    { name: 'Lipstick - Pak Ganern', hex: '#C94F6D', season: 'Summer', price: 195.00, url: 'https://vicecosmetics.com.ph' }
                ]
            },
            'Ever Bilena': {
                foundation: [
                    { name: 'EB Advance Face Trio - Sunset Trio', hex: '#C89B7E', season: 'Autumn', price: 275.00, url: 'https://www.lazada.com.ph' }
                ],
                blush: [
                    { name: 'Pillow Pop Liquid Blush', hex: '#F28C82', season: 'Spring', price: 275.00, url: 'https://www.lazada.com.ph' }
                ],
                lipstick: [
                    { name: 'Lip Define Matte Matic Lipstick - Exposed Nude', hex: '#D69A8F', season: 'Spring', price: 180.00, url: 'https://www.robinsons.com.ph' }
                ]
            }
        };

        // Seasonal color palettes
        const seasonalColors = {
            spring: {
                name: 'Spring',
                description: 'Warm undertones with bright, clear features',
                best: [
                    { name: 'Peach', hex: '#FFCBA4' },
                    { name: 'Coral', hex: '#FF7F50' },
                    { name: 'Golden Yellow', hex: '#FFD700' }
                ],
                neutral: [
                    { name: 'Ivory', hex: '#FFFFF0' },
                    { name: 'Turquoise', hex: '#40E0D0' }
                ],
                avoid: [
                    { name: 'Black', hex: '#000000' },
                    { name: 'Navy Blue', hex: '#000080' }
                ]
            },
            summer: {
                name: 'Summer',
                description: 'Cool undertones with soft, muted features',
                best: [
                    { name: 'Rose Pink', hex: '#FF66CC' },
                    { name: 'Powder Blue', hex: '#B0E0E6' },
                    { name: 'Soft Yellow', hex: '#FFFFE0' }
                ],
                neutral: [
                    { name: 'Lavender', hex: '#E6E6FA' },
                    { name: 'Soft White', hex: '#F8F8FF' }
                ],
                avoid: [
                    { name: 'Orange', hex: '#FFA500' },
                    { name: 'Dark Brown', hex: '#654321' }
                ]
            },
            autumn: {
                name: 'Autumn',
                description: 'Warm undertones with rich, earthy features',
                best: [
                    { name: 'Burnt Orange', hex: '#CC5500' },
                    { name: 'Olive Green', hex: '#808000' },
                    { name: 'Mustard', hex: '#FFDB58' }
                ],
                neutral: [
                    { name: 'Cream', hex: '#FFFDD0' },
                    { name: 'Rust', hex: '#B7410E' }
                ],
                avoid: [
                    { name: 'Bright Blue', hex: '#0066FF' },
                    { name: 'Hot Pink', hex: '#FF69B4' }
                ]
            },
            winter: {
                name: 'Winter',
                description: 'Cool undertones with high contrast features',
                best: [
                    { name: 'Royal Purple', hex: '#663399' },
                    { name: 'Emerald', hex: '#50C878' },
                    { name: 'Pure White', hex: '#FFFFFF' }
                ],
                neutral: [
                    { name: 'Charcoal', hex: '#36454F' },
                    { name: 'Navy Blue', hex: '#000080' }
                ],
                avoid: [
                    { name: 'Peach', hex: '#FFCBA4' },
                    { name: 'Golden Yellow', hex: '#FFD700' }
                ]
            }
        };

        // Drag and drop function for uploading image
        uploadSection.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadSection.classList.add('dragover');
        });

        uploadSection.addEventListener('dragleave', (e) => {
            e.preventDefault();
            uploadSection.classList.remove('dragover');
        });

        uploadSection.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadSection.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
        });

        imageInput.addEventListener('change', () => {
            const file = imageInput.files[0];
            if (file) {
                handleFile(file);
            }
        });


            // File Uploaded identifier if jpg/jpeg or png or not
            function handleFile(file) {
                const validTypes = ['image/jpeg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    showError('Please upload a JPG or PNG image only.');
                    return;
                }

                if (file.size > 5 * 1024 * 1024) { // 5MB limit
                    showError('Image size must be less than 5MB.');
                    return;
                }

                hideError();

                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                    resultsSection.style.display = 'none';
                };
                reader.onerror = () => showError('Failed to read the image file.');
                reader.readAsDataURL(file);
            }


        function showError(message) {
            errorText.textContent = message;
            errorMessage.style.display = 'block';
        }

        function hideError() {
            errorMessage.style.display = 'none';
        }

        previewImage.onload = () => {
            analysisOverlay.style.display = 'flex';
            setTimeout(analyzeImage, 10000);
        };


        // Uploaded image analyzer if face or not 
            async function analyzeImage() {
            try {
                analysisStep.innerHTML = '<i class="fas fa-search fa-spin"></i> Detecting facial features...';

                let faceRegions = null;

                if (faceApiLoaded) {
                    // Detect all faces first to check for group photo or non-human content
                    const allDetections = await faceapi.detectAllFaces(previewImage, new faceapi.TinyFaceDetectorOptions());

                    if (allDetections.length === 0) {
                        showError('No face detected. Please upload a clear, front-facing photo of a human face.');
                        analysisOverlay.style.display = 'none';
                        return;
                    }

                    if (allDetections.length > 1) {
                        showError('Multiple faces detected. Please upload a photo with only one person.');
                        analysisOverlay.style.display = 'none';
                        return;
                    }

                    // Proceed with detailed detection on the single face
                    const detection = await faceapi.detectSingleFace(previewImage, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks();

                    if (!detection) {
                        showError('Face landmarks could not be detected. Make sure the face is clear and unobstructed.');
                        analysisOverlay.style.display = 'none';
                        return;
                    }

                    analysisStep.innerHTML = '<i class="fas fa-palette fa-spin"></i> Analyzing skin tones...';
                    faceRegions = extractFaceRegions(detection);

                } else {
                    faceRegions = getFallbackRegions();
                    analysisStep.innerHTML = '<i class="fas fa-palette fa-spin"></i> Analyzing skin tones...';
                }

                const colorAnalysis = await analyzeFaceRegions(faceRegions);
                analysisOverlay.style.display = 'none';
                displayResults(colorAnalysis);

            } catch (error) {
                console.error('Analysis error:', error);
                showError('Analysis failed. Please try again with a clearer, front-facing photo of a single human face.');
                analysisOverlay.style.display = 'none';
            }
        }


        function extractFaceRegions(detection) {
            const landmarks = detection.landmarks;
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            canvas.width = previewImage.naturalWidth;
            canvas.height = previewImage.naturalHeight;
            ctx.drawImage(previewImage, 0, 0);
            
            return {
                forehead: getRegionPixels(ctx, getForeheadRegion(landmarks)),
                leftCheek: getRegionPixels(ctx, getLeftCheekRegion(landmarks)),
                rightCheek: getRegionPixels(ctx, getRightCheekRegion(landmarks))
            };
        }

        function getForeheadRegion(landmarks) {
            const points = landmarks.positions;
            const eyebrowPoints = points.slice(17, 27);
            const centerX = (points[27].x + points[28].x) / 2;
            const topY = Math.min(...eyebrowPoints.map(p => p.y)) - 30;
            const bottomY = Math.min(...eyebrowPoints.map(p => p.y));
            
            return { x: centerX - 40, y: topY, width: 80, height: bottomY - topY };
        }

        function getLeftCheekRegion(landmarks) {
            const points = landmarks.positions;
            return { x: points[1].x, y: points[29].y, width: 30, height: 40 };
        }

        function getRightCheekRegion(landmarks) {
            const points = landmarks.positions;
            return { x: points[15].x - 30, y: points[29].y, width: 30, height: 40 };
        }

        function getFallbackRegions() {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            
            canvas.width = previewImage.naturalWidth;
            canvas.height = previewImage.naturalHeight;
            ctx.drawImage(previewImage, 0, 0);
            
            const w = canvas.width;
            const h = canvas.height;
            
            return {
                forehead: getRegionPixels(ctx, { x: w * 0.35, y: h * 0.15, width: w * 0.3, height: h * 0.15 }),
                leftCheek: getRegionPixels(ctx, { x: w * 0.25, y: h * 0.4, width: w * 0.15, height: h * 0.15 }),
                rightCheek: getRegionPixels(ctx, { x: w * 0.6, y: h * 0.4, width: w * 0.15, height: h * 0.15 })
            };
        }

        function getRegionPixels(ctx, region) {
            try {
                const x = Math.max(0, Math.min(region.x, ctx.canvas.width - 1));
                const y = Math.max(0, Math.min(region.y, ctx.canvas.height - 1));
                const width = Math.min(region.width, ctx.canvas.width - x);
                const height = Math.min(region.height, ctx.canvas.height - y);
                
                if (width <= 0 || height <= 0) {
                    return [];
                }

                const imageData = ctx.getImageData(x, y, width, height);
                const pixels = [];
                
                for (let i = 0; i < imageData.data.length; i += 4) {
                    pixels.push([
                        imageData.data[i],
                        imageData.data[i + 1],
                        imageData.data[i + 2]
                    ]);
                }
                
                return pixels;
            } catch (error) {
                console.error('Error getting region pixels:', error);
                return [];
            }
        }

        async function analyzeFaceRegions(regions) {
            const analysis = {};
            
            for (const [regionName, pixels] of Object.entries(regions)) {
                if (pixels.length > 0) {
                    const skinPixels = pixels.filter(rgb => isSkinTone(rgb));
                    
                    if (skinPixels.length > 0) {
                        const avgColor = calculateAverageColor(skinPixels);
                        analysis[regionName] = {
                            color: avgColor,
                            hex: rgbToHex(avgColor[0], avgColor[1], avgColor[2])
                        };
                    }
                }
            }
            
            return analysis;
        }

        function isSkinTone(rgb) {
            const [r, g, b] = rgb;
            const max = Math.max(r, g, b);
            const min = Math.min(r, g, b);
            const diff = max - min;
            const brightness = (r + g + b) / 3;
            
            // Adjusted for Filipino skin tones (morena, medium, fair)
            return brightness > 60 && brightness < 230 && diff < 80 && r > g && g > b;
        }

        function calculateAverageColor(pixels) {
            const total = pixels.reduce((acc, pixel) => {
                acc[0] += pixel[0];
                acc[1] += pixel[1];
                acc[2] += pixel[2];
                return acc;
            }, [0, 0, 0]);
            
            return [
                Math.round(total[0] / pixels.length),
                Math.round(total[1] / pixels.length),
                Math.round(total[2] / pixels.length)
            ];
        }

        function displayResults(colorAnalysis) {
            paletteDisplay.innerHTML = '';
            recommendations.innerHTML = '';

            const skinToneColors = [];
            if (colorAnalysis.forehead) skinToneColors.push(colorAnalysis.forehead.color);
            if (colorAnalysis.leftCheek) skinToneColors.push(colorAnalysis.leftCheek.color);
            if (colorAnalysis.rightCheek) skinToneColors.push(colorAnalysis.rightCheek.color);

            if (skinToneColors.length > 0) {
                const overallSkinTone = calculateAverageColor(skinToneColors);
                const hex = rgbToHex(overallSkinTone[0], overallSkinTone[1], overallSkinTone[2]);
                
                const paletteBox = document.createElement('div');
                paletteBox.className = 'palette-box';
                paletteBox.style.backgroundColor = hex;
                paletteBox.setAttribute('data-hex', hex);
                paletteBox.title = 'Primary skin tone';
                paletteBox.style.border = '4px solid #6c5ce7';
                paletteDisplay.appendChild(paletteBox);

                const { tone, season } = detectSkinToneAndSeason(overallSkinTone);
                
                const seasonInfo = document.createElement('div');
                seasonInfo.className = 'alert alert-info mt-3';
                seasonInfo.innerHTML = `
                    <i class="fas fa-info-circle"></i>
                    <strong>Analysis Complete!</strong> Detected skin tone: <strong>${tone.charAt(0).toUpperCase() + tone.slice(1)}</strong>
                    <br>Seasonal palette: <strong>${season.name}</strong> (${season.description})
                `;
                resultsSection.appendChild(seasonInfo);

                // Display seasonal color palette
                const paletteSection = document.createElement('div');
                paletteSection.className = 'row mb-4';
                paletteSection.innerHTML = `
                    <div class="col-12">
                        <h3 class="section-title">
                            <i class="fas fa-paint-brush"></i> Seasonal Color Palette
                        </h3>
                        <div class="palette-container">
                            ${season.best.map(color => `
                                <div class="palette-box" style="background-color: ${color.hex}" data-hex="${color.hex}" title="${color.name} (Best Match)"></div>
                            `).join('')}
                            ${season.neutral.map(color => `
                                <div class="palette-box" style="background-color: ${color.hex}" data-hex="${color.hex}" title="${color.name} (Neutral)"></div>
                            `).join('')}
                            ${season.avoid.map(color => `
                                <div class="palette-box" style="background-color: ${color.hex}" data-hex="${color.hex}" title="${color.name} (Avoid)"></div>
                            `).join('')}
                        </div>
                    </div>
                `;
                resultsSection.appendChild(paletteSection);

                // Recommend products
                const recommendedProducts = getRecommendedProducts(tone, season.name.toLowerCase());
                recommendedProducts.forEach(product => {
                    const card = document.createElement('div');
                    card.className = 'product-card';
                    card.innerHTML = `
                        <div class="product-title">
                            <i class="fas fa-shopping-bag"></i>
                            ${product.name}
                        </div>
                        <div class="product-swatch" style="background-color: ${product.hex}"></div>
                        <div class="product-description">
                            <strong>Brand:</strong> ${product.brand}<br>
                            <strong>Season:</strong> ${product.season}<br>
                            <strong>Price:</strong> ₱${product.price.toFixed(2)}<br>
                        
                        </div>
                    `;
                    recommendations.appendChild(card);
                });

                resultsSection.style.display = 'block';
            } else {
                showError('Could not analyze skin tone. Please upload a clearer photo without makeup or filters.');
            }
        }

        function rgbToHex(r, g, b) {
            return "#" + [r, g, b].map(x => x.toString(16).padStart(2, '0')).join('');
        }

        function detectSkinToneAndSeason(rgb) {
            const [r, g, b] = rgb;
            const brightness = (r + g + b) / 3;
            const redness = r - ((g + b) / 2);
            const yellowness = ((r + g) / 2) - b;

            let tone, season;
            if (brightness > 200) {
                tone = 'fair';
                season = seasonalColors.spring;
            } else if (brightness > 160) {
                tone = 'light';
                season = seasonalColors.spring;
            } else if (brightness > 120) {
                if (yellowness > 30 && g > r) {
                    tone = 'olive';
                    season = seasonalColors.autumn;
                } else {
                    tone = 'medium';
                    season = seasonalColors.autumn;
                }
            } else {
                tone = 'dark';
                season = seasonalColors.winter;
            }

            return { tone, season };
        }

        function getRecommendedProducts(tone, season) {
            const recommendations = [];
            const brands = ['Happy Skin', 'BLK Cosmetics', 'Vice Cosmetics', 'Ever Bilena'];

            brands.forEach(brand => {
                ['foundation', 'blush', 'lipstick'].forEach(category => {
                    const items = products[brand][category].filter(item => item.season.toLowerCase() === season);
                    if (items.length > 0) {
                        const item = items[0]; // Select first matching item
                        recommendations.push({
                            name: item.name,
                            hex: item.hex,
                            season: item.season,
                            price: item.price,
                            url: item.url,
                            brand: brand
                        });
                    }
                });
            });

            return recommendations;
        }
    </script>
</body>
</html>