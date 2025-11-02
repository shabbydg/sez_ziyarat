<?php
/**
 * Admin Check Previous Application API
 * Checks for previous KUN applications and returns data for auto-population
 */

require_once 'common/database.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['kun_its']) || empty($input['kun_its'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ITS number is required']);
    exit;
}

$kun_its = mysqli_real_escape_string(getDbConnection(), $input['kun_its']);

try {
    // Check for the most recent application for this ITS
    $sql = "SELECT * FROM zyt_kun_registrations 
            WHERE kun_its = '$kun_its' 
            AND kun_pp_no IS NOT NULL 
            AND kun_pp_no != '' 
            ORDER BY kun_id DESC 
            LIMIT 1";
    
    $result = dbQuery($sql);
    
    if (dbNumRows($result) > 0) {
        $row = dbFetchAssoc($result);
        
        // Check passport expiry validity (6 months rule)
        $isPassportValid = true;
        $passportMessage = '';
        
        if (!empty($row['kun_pp_expiry'])) {
            $expiryDate = new DateTime($row['kun_pp_expiry']);
            $currentDate = new DateTime();
            $sixMonthsFromNow = clone $currentDate;
            $sixMonthsFromNow->add(new DateInterval('P6M'));
            
            if ($expiryDate < $sixMonthsFromNow) {
                $isPassportValid = false;
                $passportMessage = 'Passport expires within 6 months. Manual entry required.';
            }
        }
        
        echo json_encode([
            'success' => true,
            'hasData' => true,
            'data' => $row,
            'passportValid' => $isPassportValid,
            'passportMessage' => $passportMessage
        ]);
        
    } else {
        echo json_encode([
            'success' => true,
            'hasData' => false,
            'message' => 'No previous application found'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>

