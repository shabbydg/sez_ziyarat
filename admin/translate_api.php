<?php
/**
 * Admin Google Translate API Endpoint
 * Handles translation requests using Google Cloud Translate V3
 */

require_once 'common/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Google\Cloud\Translate\V3\TranslationServiceClient;
use Google\Cloud\Translate\V3\TranslateTextRequest;

// Set up Google Cloud credentials
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/../gcloud/service-account.json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['text']) || empty($input['text'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Text is required']);
    exit;
}

$text = trim($input['text']);
$sourceLanguage = $input['source'] ?? 'en';
$targetLanguage = $input['target'] ?? 'ar';

try {
    // Initialize the client
    $translationServiceClient = new TranslationServiceClient();
    
    // Project ID
    $projectId = 'your-project-id'; // Update this with your actual project ID
    $location = 'global';
    $parent = $translationServiceClient->locationName($projectId, $location);
    
    // Create the request
    $request = (new TranslateTextRequest())
        ->setParent($parent)
        ->setContents([$text])
        ->setMimeType('text/plain')
        ->setSourceLanguageCode($sourceLanguage)
        ->setTargetLanguageCode($targetLanguage);
    
    // Make the request
    $response = $translationServiceClient->translateText($request);
    
    // Get translations
    $translations = $response->getTranslations();
    
    if (count($translations) > 0) {
        $translatedText = $translations[0]->getTranslatedText();
        
        echo json_encode([
            'success' => true,
            'translatedText' => $translatedText,
            'originalText' => $text,
            'sourceLanguage' => $sourceLanguage,
            'targetLanguage' => $targetLanguage
        ]);
    } else {
        throw new Exception('No translation returned');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Translation failed: ' . $e->getMessage()
    ]);
}
?>
