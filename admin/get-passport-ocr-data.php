<?php
/**
 * Get Passport OCR Data API Endpoint
 * 
 * Fetches OCR data for a given kun_id from the latest passport_info photo
 * Used for "Import from Passport" functionality in registration forms
 */

require_once 'common/config.php';
require_once 'common/functions.php';

header('Content-Type: application/json');

// Response structure
$response = [
    'success' => false,
    'message' => '',
    'data' => null,
    'photo_info' => null
];

try {
    // Get kun_id parameter
    $kunId = isset($_GET['kun_id']) ? intval($_GET['kun_id']) : 0;
    
    if ($kunId <= 0) {
        throw new Exception('Invalid registration ID');
    }
    
    // Query for latest passport_info photo
    $sql = "SELECT photo_id, photo_filename, photo_upload_time, photo_user_id 
            FROM zyt_kun_photos 
            WHERE kun_id = $kunId AND photo_type = 'passport_info' 
            ORDER BY photo_upload_time DESC 
            LIMIT 1";
    
    $result = dbQuery($sql);
    
    if (!$result || dbNumRows($result) === 0) {
        throw new Exception('No passport information page found for this registration');
    }
    
    $photoRow = dbFetchAssoc($result);
    
    // Check if file exists
    $filePath = SRV_ROOT . 'images/uploads/' . $photoRow['photo_filename'];
    if (!file_exists($filePath)) {
        throw new Exception('Passport image file not found on server');
    }
    
    // Run OCR extraction
    require_once 'passport_ocr_service.php';
    $ocrResult = extractPassportData($filePath);
    
    if (!$ocrResult['success']) {
        throw new Exception('OCR extraction failed: ' . ($ocrResult['error'] ?? 'Unknown error'));
    }
    
    // Map OCR data to database field names
    $data = $ocrResult['data'] ?? [];
    $mappedData = [
        'kun_ppno' => $data['kun_passport_no'] ?? '',
        'kun_ppexpiry' => $data['kun_passport_expiry'] ?? '',
        'kun_fname' => $data['kun_fname'] ?? '',
        'kun_lname' => $data['kun_lname'] ?? '',
        'kun_nationality' => $data['kun_nationality'] ?? '',
        'kun_dob' => $data['kun_dob'] ?? '',
        'kun_gender' => $data['kun_gender'] ?? ''
    ];
    
    $response['success'] = true;
    $response['message'] = 'OCR data retrieved successfully';
    $response['data'] = $mappedData;
    $response['photo_info'] = [
        'photo_id' => $photoRow['photo_id'],
        'filename' => $photoRow['photo_filename'],
        'upload_time' => $photoRow['photo_upload_time'],
        'uploaded_by' => $photoRow['photo_user_id']
    ];
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>



