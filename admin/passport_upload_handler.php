<?php
/**
 * Admin Passport Upload Handler
 * 
 * Handles passport document uploads (passport_info and passport_page) with:
 * - OCR extraction for passport_info pages
 * - Quality validation with admin override capability
 * - Thumbnail generation
 * - Multiple file upload support
 * - Comprehensive error handling
 */

require_once 'common/config.php';
require_once 'common/functions.php';
require_once 'common/cleanup_functions.php';
require_once 'passport_ocr_service.php';

// Set content type for JSON response
header('Content-Type: application/json');

// Constants
$adminthisFile = str_replace('\\', '/', __FILE__);
$adminsrvRoot  = str_replace('admin/passport_upload_handler.php', '', $adminthisFile);
define('PASSPORT_UPLOAD_DIR', $adminsrvRoot . 'images/uploads/');

/**
 * Upload a single file
 */
function uploadFile($file, $uploadDir) {
    $result = [
        'success' => false,
        'filename' => null,
        'error' => null
    ];
    
    // Validate file upload
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        $result['error'] = 'Invalid file upload';
        return $result;
    }
    
    // Check file size
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        $result['error'] = 'File size exceeds 5MB limit';
        return $result;
    }
    
    // Validate file type
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        $result['error'] = 'Invalid image file';
        return $result;
    }
    
    // Generate MD5 hash filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $md5Hash = md5_file($file['tmp_name']);
    $filename = $md5Hash . '.' . strtolower($fileExtension);
    
    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Move uploaded file
    $targetPath = $uploadDir . $filename;
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $result['success'] = true;
        $result['filename'] = $filename;
    } else {
        $result['error'] = 'Failed to move uploaded file';
    }
    
    return $result;
}

/**
 * Run passport quality check
 */
function runPassportQualityCheck($filePath, $adminOverride = false) {
    $qualityResult = validatePassportImageQuality($filePath);
    
    if ($adminOverride && (isset($qualityResult['error']) || (isset($qualityResult['issues']) && !empty($qualityResult['issues'])))) {
        // Admin override: return as warnings instead of errors
        $issues = $qualityResult['issues'] ?? [$qualityResult['error']];
        return [
            'overridden' => true,
            'warnings' => $issues,
            'message' => 'Quality issues detected but overridden by admin'
        ];
    }
    
    return $qualityResult;
}

/**
 * Process image file with conversion
 */
function processImageFile($filePath, $filename) {
    $result = [
        'success' => false,
        'processed_filename' => null,
        'error' => null
    ];
    
    try {
        $base = pathinfo($filename, PATHINFO_FILENAME);
        $jpgDest = PASSPORT_UPLOAD_DIR . $base . '.jpg';
        
        // Convert to JPG
        $converted = convertToJpg($filePath, $jpgDest);
        if (!$converted) {
            $result['error'] = 'Failed to convert image to JPG';
            return $result;
        }
        
        $result['success'] = true;
        $result['processed_filename'] = basename($jpgDest);
        
    } catch (Exception $e) {
        $result['error'] = 'Image processing error: ' . $e->getMessage();
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    
    return $result;
}

/**
 * Run passport OCR
 */
function runPassportOcr($filePath) {
    $result = [
        'success' => false,
        'data' => [],
        'error' => null
    ];
    
    try {
        $ocrResult = extractPassportData($filePath);
        
        if (is_array($ocrResult) && isset($ocrResult['success']) && $ocrResult['success']) {
            $result['success'] = true;
            
            // Map OCR results to database field names
            $data = $ocrResult['data'] ?? [];
            $result['data'] = [
                'kun_ppno' => $data['kun_passport_no'] ?? '',
                'kun_ppexpiry' => $data['kun_passport_expiry'] ?? '',
                'kun_fname' => $data['kun_fname'] ?? '',
                'kun_lname' => $data['kun_lname'] ?? '',
                'kun_nationality' => $data['kun_nationality'] ?? '',
                'kun_dob' => $data['kun_dob'] ?? '',
                'kun_gender' => $data['kun_gender'] ?? ''
            ];
        } else {
            $result['error'] = $ocrResult['error'] ?? 'OCR extraction failed';
        }
        
    } catch (Exception $e) {
        $result['error'] = 'OCR processing error: ' . $e->getMessage();
    }
    
    return $result;
}

/**
 * Insert photo record into database
 */
function insertPhotoRecord($kunId, $filename, $thumbnail, $photoType, $ocrData = []) {
    global $dbConn;
    $result = [
        'success' => false,
        'photo_id' => null,
        'error' => null
    ];
    
    try {
        $escapedKunId = mysqli_real_escape_string($dbConn, $kunId);
        $escapedFilename = mysqli_real_escape_string($dbConn, $filename);
        $escapedThumbnail = mysqli_real_escape_string($dbConn, $thumbnail);
        $escapedPhotoType = mysqli_real_escape_string($dbConn, $photoType);
        $escapedUserId = mysqli_real_escape_string($dbConn, getActiveUser());
        
        $sql = "INSERT INTO zyt_kun_photos (kun_id, photo_filename, photo_filename_thumb, photo_type, photo_upload_time, photo_user_id) 
                VALUES ('$escapedKunId', '$escapedFilename', '$escapedThumbnail', '$escapedPhotoType', NOW(), '$escapedUserId')";
        
        $queryResult = dbQuery($sql);
        
        if ($queryResult) {
            $result['success'] = true;
            $result['photo_id'] = dbInsertId();
            $result['ocr_data'] = $ocrData;
        } else {
            $result['error'] = 'Database insertion failed: ' . mysqli_error($dbConn);
        }
        
    } catch (Exception $e) {
        $result['error'] = 'Database error: ' . $e->getMessage();
    }
    
    return $result;
}

/**
 * Generate thumbnail
 */
function generateThumbnail($srcFile, $uploadDir, $size = 100) {
    $result = [
        'success' => false,
        'filename' => null,
        'error' => null
    ];
    
    if (!file_exists($srcFile)) {
        $result['error'] = 'Source file not found';
        return $result;
    }
    
    $imageInfo = getimagesize($srcFile);
    if (!$imageInfo) {
        $result['error'] = 'Invalid image file';
        return $result;
    }
    
    $pathInfo = pathinfo($srcFile);
    $thumbnailFilename = $pathInfo['filename'] . '_thumb.jpg';
    $thumbnailPath = $uploadDir . $thumbnailFilename;
    
    $src = loadImageFromFile($srcFile, $imageInfo[2]);
    if (!$src) {
        $result['error'] = 'Failed to load source image';
        return $result;
    }
    
    $srcWidth = imagesx($src);
    $srcHeight = imagesy($src);
    $cropSize = min($srcWidth, $srcHeight);
    $cropX = ($srcWidth - $cropSize) / 2;
    $cropY = ($srcHeight - $cropSize) / 2;
    
    $thumb = imagecreatetruecolor($size, $size);
    imagecopyresampled($thumb, $src, 0, 0, $cropX, $cropY, $size, $size, $cropSize, $cropSize);
    
    if (imagejpeg($thumb, $thumbnailPath, 85)) {
        $result['success'] = true;
        $result['filename'] = $thumbnailFilename;
    } else {
        $result['error'] = 'Failed to save thumbnail';
    }
    
    imagedestroy($src);
    imagedestroy($thumb);
    
    return $result;
}

/**
 * Cleanup files on error
 */
function cleanupFiles($files) {
    foreach ($files as $file) {
        if (file_exists($file)) {
            unlink($file);
        }
    }
}

// Main processing logic
try {
    // Validate POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST requests are allowed');
    }
    
    // Check if files were uploaded
    if (!isset($_FILES['kunPhotos']) || !is_array($_FILES['kunPhotos']['name'])) {
        throw new Exception('No files uploaded');
    }
    
    // Get parameters
    $kunId = 0;
    if (isset($_POST['kunregid'])) {
        $kunId = (int) $_POST['kunregid'];
    } elseif (isset($_POST['kun_id'])) {
        $kunId = (int) $_POST['kun_id'];
    }
    if ($kunId <= 0) {
        throw new Exception('Invalid registration ID');
    }
    
    $adminOverride = isset($_POST['admin_override']) && $_POST['admin_override'] == '1';
    
    // Initialize response
    $response = [
        'success' => true,
        'message' => 'Passport upload processing completed',
        'admin_override_used' => $adminOverride,
        'data' => [
            'total_files' => count($_FILES['kunPhotos']['name']),
            'processed_files' => 0,
            'failed_files' => 0,
            'file_results' => [],
            'ocr_data' => null
        ],
        'warnings' => [],
        'errors' => []
    ];
    
    // Process each uploaded file
    $fileCount = count($_FILES['kunPhotos']['name']);
    $isFirstFile = true;
    
    for ($i = 0; $i < $fileCount; $i++) {
        $fileResult = [
            'file_index' => $i,
            'original_name' => $_FILES['kunPhotos']['name'][$i],
            'success' => false,
            'filename' => null,
            'thumbnail' => null,
            'photo_type' => null,
            'ocr_data' => null,
            'quality_validation' => null,
            'error' => null
        ];
        
        $uploadedFiles = []; // Track files for cleanup
        
        try {
            // Prepare file array
            $file = [
                'name' => $_FILES['kunPhotos']['name'][$i],
                'type' => $_FILES['kunPhotos']['type'][$i],
                'tmp_name' => $_FILES['kunPhotos']['tmp_name'][$i],
                'error' => $_FILES['kunPhotos']['error'][$i],
                'size' => $_FILES['kunPhotos']['size'][$i]
            ];
            
            // Check for upload errors
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('File upload error: ' . $file['error']);
            }
            
            // Upload file
            $uploadResult = uploadFile($file, PASSPORT_UPLOAD_DIR);
            if (!$uploadResult['success']) {
                throw new Exception($uploadResult['error']);
            }
            
            $uploadedFiles[] = PASSPORT_UPLOAD_DIR . $uploadResult['filename'];
            $fileResult['filename'] = $uploadResult['filename'];
            
            // Process image file
            $processResult = processImageFile(PASSPORT_UPLOAD_DIR . $uploadResult['filename'], $uploadResult['filename']);
            if (!$processResult['success']) {
                throw new Exception($processResult['error']);
            }
            
            $uploadedFiles[] = PASSPORT_UPLOAD_DIR . $processResult['processed_filename'];
            $fileResult['filename'] = $processResult['processed_filename'];
            
            // Validate image quality
            $qualityResult = runPassportQualityCheck(PASSPORT_UPLOAD_DIR . $processResult['processed_filename'], $adminOverride);
            $fileResult['quality_validation'] = $qualityResult;
            
            // Handle quality issues
            if (!$adminOverride && (isset($qualityResult['error']) || (isset($qualityResult['issues']) && !empty($qualityResult['issues'])))) {
                $issues = $qualityResult['issues'] ?? [$qualityResult['error']];
                throw new Exception('Image quality issues: ' . implode('; ', $issues));
            }
            
            if (isset($qualityResult['warnings']) && !empty($qualityResult['warnings'])) {
                $response['warnings'] = array_merge($response['warnings'], $qualityResult['warnings']);
            }
            
            // Determine photo type
            $photoType = $isFirstFile ? 'passport_info' : 'passport_page';
            $fileResult['photo_type'] = $photoType;
            
            // Extract OCR data for passport_info
            $ocrData = [];
            if ($isFirstFile) {
                $ocrResult = runPassportOcr(PASSPORT_UPLOAD_DIR . $processResult['processed_filename']);
                if ($ocrResult['success']) {
                    $fileResult['ocr_data'] = $ocrResult['data'];
                    $ocrData = $ocrResult['data'];
                    $response['data']['ocr_data'] = $ocrData;
                } else {
                    $fileResult['ocr_data'] = ['error' => $ocrResult['error']];
                }
            }
            
            // Generate thumbnail
            $thumbResult = generateThumbnail(PASSPORT_UPLOAD_DIR . $processResult['processed_filename'], PASSPORT_UPLOAD_DIR);
            $thumbnail = $thumbResult['success'] ? $thumbResult['filename'] : '';
            $fileResult['thumbnail'] = $thumbnail;
            
            // Insert photo record into database
            $dbResult = insertPhotoRecord($kunId, $processResult['processed_filename'], $thumbnail, $photoType, $ocrData);
            if (!$dbResult['success']) {
                throw new Exception($dbResult['error']);
            }
            
            $fileResult['success'] = true;
            $fileResult['photo_id'] = $dbResult['photo_id'];
            $response['data']['processed_files']++;
            
            // Mark first file as processed
            $isFirstFile = false;
            
        } catch (Exception $e) {
            $fileResult['error'] = $e->getMessage();
            $response['data']['failed_files']++;
            $response['errors'][] = "File " . ($i + 1) . ": " . $e->getMessage();
            
            // Cleanup uploaded files on failure
            cleanupFiles($uploadedFiles);
        }
        
        $response['data']['file_results'][] = $fileResult;
    }
    
    // Determine overall success
    if ($response['data']['failed_files'] > 0 && $response['data']['processed_files'] === 0) {
        $response['success'] = false;
        $response['message'] = 'All files failed to process';
    } else if ($response['data']['failed_files'] > 0) {
        $response['success'] = false;
        $response['message'] = 'Some files failed to process';
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Passport upload processing failed',
        'error' => $e->getMessage(),
        'data' => null
    ];
}

// Return JSON response
echo json_encode($response, JSON_PRETTY_PRINT);
?>

