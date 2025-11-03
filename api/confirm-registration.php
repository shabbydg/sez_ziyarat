<?php
/**
 * Confirm Registration API
 * 
 * Updates registration status to confirmed after user review
 */

require_once '../common/config.php';
require_once '../common/functions.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $kun_id = isset($input['kun_id']) ? intval($input['kun_id']) : 0;
    $safar_id = isset($input['safar_id']) ? intval($input['safar_id']) : 0;
    
    if ($kun_id <= 0) {
        throw new Exception('Invalid registration ID');
    }
    
    // Update registration status to confirmed
    $sql = "UPDATE zyt_kun_registrations 
            SET kun_status = 'confirmed', 
                kun_confirmed_date = NOW()
            WHERE kun_id = $kun_id";
    
    $result = dbQuery($sql);
    
    if (!$result) {
        throw new Exception('Failed to confirm registration');
    }
    
    // Log confirmation
    error_log("Registration $kun_id confirmed by user");
    
    $response['success'] = true;
    $response['message'] = 'Registration confirmed successfully';
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>

