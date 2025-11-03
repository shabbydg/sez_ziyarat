<?php
/**
 * Check Previous Registration API
 * 
 * Checks if a user has a previous registration with valid passport
 * Returns previous data and validity status for smart registration routing
 */

require_once '../common/config.php';
require_once '../common/functions.php';
require_once '../common/date_helpers.php';

header('Content-Type: application/json');

// Response structure
$response = [
    'success' => false,
    'has_previous' => false,
    'passport_valid' => false,
    'months_remaining' => 0,
    'travel_date' => null,
    'previous_kun_id' => null,
    'photo_count' => 0,
    'action' => 'manual',
    'previous_data' => null,
    'message' => ''
];

try {
    // Get input
    $input = json_decode(file_get_contents('php://input'), true);
    
    $kun_its = isset($input['kun_its']) ? trim($input['kun_its']) : '';
    $safar_id = isset($input['safar_id']) ? intval($input['safar_id']) : 0;
    
    if (empty($kun_its)) {
        throw new Exception('ITS number is required');
    }
    
    if ($safar_id <= 0) {
        throw new Exception('Invalid safar ID');
    }
    
    // Step 1: Query for latest non-cancelled registration
    $sql = "SELECT * FROM zyt_kun_registrations 
            WHERE kun_its = '" . mysqli_real_escape_string($dbConn, $kun_its) . "' 
              AND kun_status != 'cancelled' 
            ORDER BY kun_id DESC 
            LIMIT 1";
    
    $result = dbQuery($sql);
    
    if (!$result || dbNumRows($result) === 0) {
        $response['success'] = true;
        $response['message'] = 'No previous registration found';
        echo json_encode($response);
        exit;
    }
    
    $previousReg = dbFetchAssoc($result);
    $response['has_previous'] = true;
    $response['previous_kun_id'] = $previousReg['kun_id'];
    
    // Step 2: Get safar travel date
    $sql_safar = "SELECT safar_eta FROM zyt_safar WHERE safar_id = $safar_id";
    $safarResult = dbQuery($sql_safar);
    $safarData = dbFetchAssoc($safarResult);
    
    // Use travel date or fallback to today + 6 months
    $travelDate = ($safarData && !empty($safarData['safar_eta'])) 
                    ? $safarData['safar_eta'] 
                    : date('Y-m-d', strtotime('+6 months'));
    
    $response['travel_date'] = $travelDate;
    
    // Step 3: Check passport validity
    $expiryDate = $previousReg['kun_ppexpiry'];
    
    if (empty($expiryDate) || $expiryDate === '0000-00-00') {
        $response['success'] = true;
        $response['message'] = 'Previous registration found but no passport expiry date';
        echo json_encode($response);
        exit;
    }
    
    $validityStatus = getPassportValidityStatus($expiryDate, $travelDate);
    $response['passport_valid'] = $validityStatus['valid'];
    $response['months_remaining'] = $validityStatus['months_remaining'];
    
    // Step 4: Count associated photos
    $sql_photos = "SELECT COUNT(*) as photo_count FROM zyt_kun_photos 
                   WHERE kun_id = " . $previousReg['kun_id'] . " 
                     AND photo_type IN ('passport_info', 'passport_page', 'passport_photo')";
    $photoResult = dbQuery($sql_photos);
    $photoData = dbFetchAssoc($photoResult);
    $response['photo_count'] = $photoData ? intval($photoData['photo_count']) : 0;
    
    // Step 5: Determine action
    if ($response['passport_valid']) {
        // FLOW A: Valid passport - auto-create entire registration
        $response['action'] = 'auto_create';
        $response['message'] = "Previous registration found with valid passport ({$response['months_remaining']} months remaining)";
        $response['previous_data'] = $previousReg;
        $response['prefill_fields'] = [];
        $response['require_new_passport'] = false;
        
    } else if ($response['has_previous']) {
        // FLOW C: Expired passport - partial prefill (NEW)
        $response['action'] = 'partial_prefill';
        $response['message'] = "Previous registration found but passport expired. We'll pre-fill your contact and family details.";
        $response['previous_data'] = $previousReg;
        $response['require_new_passport'] = true;
        
        // Specify which fields to prefill
        $response['prefill_fields'] = [
            'non_passport' => [
                'kun_its', 'kun_address', 'kun_contact', 'kun_email', 'kun_mname',
                'kun_passport_type', 'kun_profession', 
                'kun_father_name', 'kun_grandfather_name', 'kun_mother_name', 'kun_mother_father_name',
                'kun_father_name_arabic', 'kun_grandfather_name_arabic', 
                'kun_mother_name_arabic', 'kun_mother_father_name_arabic',
                'kun_fname_arabic', 'kun_mname_arabic', 'kun_lname_arabic'
            ],
            'verify_only' => [
                'kun_dob', 'kun_gender', 'kun_birthplace', 'kun_age'
            ],
            'new_passport_required' => [
                'kun_ppno', 'kun_ppissue', 'kun_ppexpiry', 'kun_placeofissue',
                'kun_fname', 'kun_lname', 'kun_nationality'
            ]
        ];
        
    } else {
        // FLOW B: No previous registration - manual entry
        $response['action'] = 'manual';
        $response['message'] = 'No previous registration found';
        $response['prefill_fields'] = [];
        $response['require_new_passport'] = false;
    }
    
    $response['success'] = true;
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log("Check previous registration error: " . $e->getMessage());
}

echo json_encode($response);
?>

