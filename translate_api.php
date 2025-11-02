<?php

require_once 'vendor/autoload.php'; // Use Composer autoloader
require_once 'common/config.php'; // Load configuration

header('Content-Type: application/json');

use Google\Cloud\Translate\V3\TranslationServiceClient;

function translateText($text, $targetLanguage = 'ar', $sourceLanguage = 'en') {
    $projectId = 'm-abdulally'; // Replace with your actual project ID

    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . GCLOUD_CREDENTIALS_PATH);

    try {
        $translationServiceClient = new TranslationServiceClient();

        // Set location for v3 API
        $location = $translationServiceClient->locationName($projectId, 'global');

        // Make the translation request
        $response = $translationServiceClient->translateText(
            [$text],
            $targetLanguage,
            $location,
            [
                'sourceLanguageCode' => $sourceLanguage,
                'mimeType' => 'text/plain'
            ]
        );

        // Get translated text from response
        $translatedText = null;
        foreach ($response->getTranslations() as $translation) {
            $translatedText = $translation->getTranslatedText();
            break;
        }
        $translationServiceClient->close();
        return ['translatedText' => $translatedText];
    } catch (Exception $e) {
        return ['error' => 'Translation service unavailable', 'debug' => $e->getMessage()];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $text = $input['text'] ?? '';
    $target = $input['target'] ?? 'ar';
    $source = $input['source'] ?? 'en';

    $result = translateText($text, $target, $source);
    echo json_encode($result);
} else {
    echo json_encode(['error' => 'POST method required']);
}
