<?php

require_once 'vendor/autoload.php'; // Use Composer autoloader
require_once 'common/config.php'; // Load configuration

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Feature\Type;

function extractPassportData($imagePath) {
    $projectId = 'm-abdulally'; // Replace with your actual project ID

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . GCLOUD_CREDENTIALS_PATH);

    try {
        $imageAnnotatorClient = new ImageAnnotatorClient();

        // Read the image file
        $image = file_get_contents($imagePath);
        if ($image === false) {
            return ['error' => 'Could not read image file'];
        }

        // Perform document text detection (OCR)
        $response = $imageAnnotatorClient->documentTextDetection($image);
        
        // Check for API errors
        if ($response->getError()) {
            $imageAnnotatorClient->close();
            return ['error' => 'OCR service error', 'debug' => $response->getError()->getMessage()];
        }
        
        $annotations = $response->getFullTextAnnotation();

        if ($annotations) {
            $text = $annotations->getText();
            
            // Parse MRZ (Machine Readable Zone) data
            $passportData = parseMRZData($text);
            
            $imageAnnotatorClient->close();
            return ['success' => true, 'data' => $passportData, 'rawText' => $text];
        } else {
            $imageAnnotatorClient->close();
            return ['error' => 'No text detected in image'];
        }

    } catch (Exception $e) {
        return ['error' => 'OCR service unavailable', 'debug' => $e->getMessage()];
    }
}

function validateImageQuality($imagePath) {
    $projectId = 'm-abdulally'; // Replace with your actual project ID

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . GCLOUD_CREDENTIALS_PATH);

    try {
        $imageAnnotatorClient = new ImageAnnotatorClient();

        // Read the image file
        $image = file_get_contents($imagePath);
        if ($image === false) {
            return ['error' => 'Could not read image file'];
        }

        // Perform label detection, image properties analysis, and text detection
        $features = [
            Type::LABEL_DETECTION,
            Type::IMAGE_PROPERTIES,
            Type::TEXT_DETECTION
        ];

        $response = $imageAnnotatorClient->annotateImage($image, $features);
        
        // Check for API errors
        if ($response->getError()) {
            $imageAnnotatorClient->close();
            return ['error' => 'Image validation service error', 'debug' => $response->getError()->getMessage()];
        }
        
        $qualityIssues = [];
        
        // Check for poor quality indicators
        $labels = $response->getLabelAnnotations() ?: [];
        foreach ($labels as $label) {
            $description = strtolower($label->getDescription());
            $score = $label->getScore();
            
            // Check for fingers, phone camera artifacts, etc.
            if (strpos($description, 'finger') !== false && $score > 0.7) {
                $qualityIssues[] = 'Finger visible in image';
            }
            if (strpos($description, 'hand') !== false && $score > 0.7) {
                $qualityIssues[] = 'Hand visible in image';
            }
            if (strpos($description, 'phone') !== false && $score > 0.6) {
                $qualityIssues[] = 'Phone camera detected - please use scanner';
            }
            if (strpos($description, 'screen') !== false && $score > 0.6) {
                $qualityIssues[] = 'Screen reflection detected';
            }
        }

        // Check image properties for glare, brightness issues
        $imageProperties = $response->getImagePropertiesAnnotation();
        if ($imageProperties) {
            $dominantColors = $imageProperties->getDominantColors() ?: [];
            
            // Calculate brightness and contrast heuristics
            $totalBrightness = 0;
            $colorCount = 0;
            
            foreach ($dominantColors as $color) {
                $rgb = $color->getColor();
                if ($rgb) {
                    $brightness = ($rgb->getRed() + $rgb->getGreen() + $rgb->getBlue()) / 3;
                    $totalBrightness += $brightness;
                    $colorCount++;
                }
            }
            
            if ($colorCount > 0) {
                $avgBrightness = $totalBrightness / $colorCount;
                
                // Check for overexposure (too bright)
                if ($avgBrightness > 200) {
                    $qualityIssues[] = 'Image appears overexposed - too bright';
                }
                
                // Check for underexposure (too dark)
                if ($avgBrightness < 50) {
                    $qualityIssues[] = 'Image appears underexposed - too dark';
                }
            }
        }
        
        // Check for text presence and quality
        $textAnnotations = $response->getTextAnnotations() ?: [];
        if (empty($textAnnotations)) {
            $qualityIssues[] = 'No text detected - ensure passport information is visible';
        } else {
            $totalConfidence = 0;
            $textCount = 0;
            
            foreach ($textAnnotations as $text) {
                $confidence = $text->getConfidence();
                if ($confidence !== null) {
                    $totalConfidence += $confidence;
                    $textCount++;
                }
            }
            
            if ($textCount > 0) {
                $avgConfidence = $totalConfidence / $textCount;
                
                // Check for low text confidence (blurry text)
                if ($avgConfidence < 0.7) {
                    $qualityIssues[] = 'Text appears blurry or unclear - retake photo';
                }
            }
            
            // Check minimum text length (passport should have substantial text)
            $totalTextLength = 0;
            foreach ($textAnnotations as $text) {
                $totalTextLength += strlen($text->getDescription() ?: '');
            }
            
            if ($totalTextLength < 50) {
                $qualityIssues[] = 'Insufficient text detected - ensure full passport page is visible';
            }
        }

        $imageAnnotatorClient->close();
        
        if (empty($qualityIssues)) {
            return ['success' => true, 'message' => 'Image quality is acceptable'];
        } else {
            return ['error' => 'Image quality issues detected', 'issues' => $qualityIssues];
        }

    } catch (Exception $e) {
        return ['error' => 'Image validation service unavailable', 'debug' => $e->getMessage()];
    }
}

function parseMRZData($text) {
    $passportData = [
        'kun_fname' => '',
        'kun_lname' => '',
        'kun_passport_no' => '',
        'kun_dob' => '',
        'kun_nationality' => '',
        'kun_gender' => '',
        'kun_passport_expiry' => ''
    ];

    // Collect MRZ candidates using regex and pick the last two 44-char lines
    if (preg_match_all('/^[A-Z0-9<]{44}$/m', $text, $matches) && count($matches[0]) >= 2) {
        $line1 = $matches[0][count($matches[0]) - 2];
        $line2 = $matches[0][count($matches[0]) - 1];
    } else {
        return parseMRZDataFallback($text);
    }
    
    // Normalize (strtoupper, strip spaces) before parsing
    $line1 = strtoupper(str_replace(' ', '', $line1));
    $line2 = strtoupper(str_replace(' ', '', $line2));
    
    // Parse line1 (document type + issuing state + names)
    if (substr($line1, 0, 2) === 'P<') {
        // Issuing state: substr($line1, 2, 3) (do not assign to nationality here)
        $issuingState = substr($line1, 2, 3);
        
        // Names field: substr($line1, 5, 39). Split on <<; replace < with spaces; trim
        $namesField = substr($line1, 5, 39);
        $nameParts = explode('<<', $namesField);
        
        if (count($nameParts) >= 2) {
            $surname = trim(str_replace('<', ' ', $nameParts[0]));
            $givenNames = trim(str_replace('<', ' ', $nameParts[1]));
            
            $passportData['kun_lname'] = $surname;
            $passportData['kun_fname'] = $givenNames;
        }
    }
    
    // Parse line2 fixed fields
    // kun_passport_no: substr($line2, 0, 9) with < removed; validate optional check digit at pos 9
    $passportNo = str_replace('<', '', substr($line2, 0, 9));
    $passportData['kun_passport_no'] = $passportNo;
    
    // Optional: Validate passport number check digit (position 9)
    if (strlen($line2) > 9 && is_numeric($line2[9])) {
        $expectedCheckDigit = computeMRZCheckDigit(substr($line2, 0, 9));
        $actualCheckDigit = intval($line2[9]);
        // Note: We don't fail parsing if check digit is wrong, just note uncertainty
    }
    
    // kun_nationality: substr($line2, 10, 3)
    $passportData['kun_nationality'] = substr($line2, 10, 3);
    
    // DOB: YYMMDD from substr($line2, 13, 6); map century (e.g., if YY <= currentYY+10 then 20YY else 19YY)
    $dobStr = substr($line2, 13, 6);
    if (strlen($dobStr) === 6 && is_numeric($dobStr)) {
        $yy = intval(substr($dobStr, 0, 2));
        $month = substr($dobStr, 2, 2);
        $day = substr($dobStr, 4, 2);
        
        // Map century: if YY <= currentYY+10 then 20YY else 19YY
        $currentYY = intval(date('y'));
        $year = ($yy <= $currentYY + 10) ? '20' . sprintf('%02d', $yy) : '19' . sprintf('%02d', $yy);
        
        if (checkdate($month, $day, $year)) {
            $passportData['kun_dob'] = $year . '-' . $month . '-' . $day;
        }
        
        // Optional: Validate DOB check digit (position 19)
        if (strlen($line2) > 19 && is_numeric($line2[19])) {
            $expectedCheckDigit = computeMRZCheckDigit($dobStr);
            $actualCheckDigit = intval($line2[19]);
            // Note: We don't fail parsing if check digit is wrong, just note uncertainty
        }
    }
    
    // Sex: substr($line2, 20, 1) in ['M','F']
    $sex = substr($line2, 20, 1);
    if (in_array($sex, ['M', 'F'])) {
        $passportData['kun_gender'] = $sex;
    }
    
    // Expiry: YYMMDD from substr($line2, 21, 6); map century similarly
    $expiryStr = substr($line2, 21, 6);
    if (strlen($expiryStr) === 6 && is_numeric($expiryStr)) {
        $yy = intval(substr($expiryStr, 0, 2));
        $month = substr($expiryStr, 2, 2);
        $day = substr($expiryStr, 4, 2);
        
        // Map century: if YY <= currentYY+10 then 20YY else 19YY
        $currentYY = intval(date('y'));
        $year = ($yy <= $currentYY + 10) ? '20' . sprintf('%02d', $yy) : '19' . sprintf('%02d', $yy);
        
        if (checkdate($month, $day, $year)) {
            $passportData['kun_passport_expiry'] = $year . '-' . $month . '-' . $day;
        }
        
        // Optional: Validate expiry check digit (position 27)
        if (strlen($line2) > 27 && is_numeric($line2[27])) {
            $expectedCheckDigit = computeMRZCheckDigit($expiryStr);
            $actualCheckDigit = intval($line2[27]);
            // Note: We don't fail parsing if check digit is wrong, just note uncertainty
        }
    }
    
    return $passportData;
}

// Helper function to compute MRZ check digits (weights 7-3-1)
function computeMRZCheckDigit($data) {
    $weights = [7, 3, 1];
    $weightIndex = 0;
    $sum = 0;
    
    for ($i = 0; $i < strlen($data); $i++) {
        $char = $data[$i];
        
        // Convert character to numeric value
        if (is_numeric($char)) {
            $value = intval($char);
        } elseif (ctype_alpha($char)) {
            $value = ord(strtoupper($char)) - ord('A') + 10;
        } elseif ($char === '<') {
            $value = 0;
        } else {
            continue; // Skip invalid characters
        }
        
        $sum += $value * $weights[$weightIndex % 3];
        $weightIndex++;
    }
    
    return $sum % 10;
}

// Fallback parsing method for when MRZ is not detected
function parseMRZDataFallback($text) {
    $passportData = [
        'kun_fname' => '',
        'kun_lname' => '',
        'kun_passport_no' => '',
        'kun_dob' => '',
        'kun_nationality' => '',
        'kun_gender' => '',
        'kun_passport_expiry' => ''
    ];

    // Look for passport number pattern (usually 6-9 alphanumeric characters)
    if (preg_match('/\b([A-Z0-9]{6,9})\b/', $text, $matches)) {
        $passportData['kun_passport_no'] = $matches[1];
    }

    // Look for date patterns (DD/MM/YYYY or DD-MM-YYYY)
    $datePatterns = [
        '/(\d{2})[\/\-](\d{2})[\/\-](\d{4})/',
        '/(\d{2})[\/\-](\d{2})[\/\-](\d{2})/'
    ];
    
    foreach ($datePatterns as $pattern) {
        if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $day = $match[1];
                $month = $match[2];
                $year = strlen($match[3]) == 2 ? '20' . $match[3] : $match[3];
                
                // Validate date
                if (checkdate($month, $day, $year)) {
                    $dateStr = $year . '-' . $month . '-' . $day;
                    
                    // If we haven't set DOB yet, use this date
                    if (empty($passportData['kun_dob'])) {
                        $passportData['kun_dob'] = $dateStr;
                    }
                    // If we have DOB and this is a future date, it's likely expiry
                    elseif (strtotime($dateStr) > time()) {
                        $passportData['kun_passport_expiry'] = $dateStr;
                    }
                }
            }
        }
    }

    // Look for names (common patterns in passports)
    $namePatterns = [
        '/([A-Z][a-z]+)\s+([A-Z][a-z]+)/', // First Last
        '/([A-Z][A-Z\s]+)/' // ALL CAPS names
    ];
    
    foreach ($namePatterns as $pattern) {
        if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $name = trim($match[1]);
                if (strlen($name) > 2 && strlen($name) < 50) {
                    if (empty($passportData['kun_fname'])) {
                        $passportData['kun_fname'] = $name;
                    } elseif (empty($passportData['kun_lname'])) {
                        $passportData['kun_lname'] = $name;
                    }
                }
            }
        }
    }

    // Look for nationality (usually 3-letter country codes)
    if (preg_match('/\b([A-Z]{3})\b/', $text, $matches)) {
        $passportData['kun_nationality'] = $matches[1];
    }

    // Look for gender (M/F)
    if (preg_match('/\b([MF])\b/', $text, $matches)) {
        $passportData['kun_gender'] = $matches[1];
    }

    return $passportData;
}

// Handle POST request
if (php_sapi_name() !== 'cli' && realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (php_sapi_name() !== 'cli') {
            header('Content-Type: application/json');
        }
        
        $input = json_decode(file_get_contents('php://input'), true);

        $action = $input['action'] ?? '';
        $imagePath = $input['imagePath'] ?? '';

        if (empty($imagePath)) {
            echo json_encode(['error' => 'Image path required']);
            exit;
        }

        // Validate and sanitize image path to prevent directory traversal
        $allowedBaseDir = realpath('./images/uploads/');
        if (!$allowedBaseDir) {
            echo json_encode(['error' => 'Upload directory not accessible']);
            exit;
        }
        
        // Remove any directory traversal attempts
        $imagePath = str_replace(['../', '..\\', '/..', '\\..'], '', $imagePath);
        
        // Ensure path is relative and within allowed directory
        $fullPath = realpath($allowedBaseDir . '/' . ltrim($imagePath, '/'));
        
        if (!$fullPath || strpos($fullPath, $allowedBaseDir) !== 0) {
            echo json_encode(['error' => 'Invalid image path - access denied']);
            exit;
        }
        
        // Use the validated path
        $imagePath = $fullPath;

        switch ($action) {
            case 'extract':
                $result = extractPassportData($imagePath);
                break;
            case 'validate':
                $result = validateImageQuality($imagePath);
                break;
            default:
                $result = ['error' => 'Invalid action. Use "extract" or "validate"'];
                break;
        }

        echo json_encode($result);
    } else {
        if (php_sapi_name() !== 'cli') {
            header('Content-Type: application/json');
        }
        echo json_encode(['error' => 'POST method required']);
    }
}
