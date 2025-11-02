<?php

// Add these headers at the top of the file
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Name Translation</title>
</head>
<body>
<?php

require 'vendor/autoload.php'; // Include the Google Cloud Translation library

use Google\Cloud\Translate\TranslateClient;

function translateName($name, $targetLanguage = 'ar') {
    try {
        // Your Google Cloud Project ID
        $projectId = 'm-abdulally';  // Replace with your project ID

        $translate = new TranslateClient([
            'projectId' => $projectId,
            'keyFilePath' => 'translation_key.json'
        ]);

        $result = $translate->translate($name, [
            'source' => 'en',  // Add source language
            'target' => $targetLanguage,
            'format' => 'text'  // Ensure proper text formatting
        ]);

        if (isset($result['text'])) {
            // For browser: wrap in HTML and add direction for Arabic
            return '<div dir="rtl" style="font-size: 18px;">' . htmlspecialchars($result['text']) . '</div>';
        } else {
            print_r($result);
            return null;
        }
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
}

// Example usage with HTML output
$name = "Your Name";  // Replace with your input mechanism
echo "<h2>Original Name: " . htmlspecialchars($name) . "</h2>";
echo "<h2>Translated Name: " . translateName($name) . "</h2>";

// Example usage:
$englishName = "John Doe";
$arabicName = translateName($englishName);

if ($arabicName) {
    echo "English Name: " . $englishName . "<br>";
    echo "Arabic Translation: " . $arabicName . "<br>";
} else {
    echo "Translation of '" . $englishName . "' failed." . "<br>";
}


$englishName = "Jane Smith";
$arabicName = translateName($englishName);

if ($arabicName) {
    echo "English Name: " . $englishName . "<br>";
    echo "Arabic Translation: " . $arabicName . "<br>";
} else {
    echo "Translation of '" . $englishName . "' failed." . "<br>";
}


$englishName = "Michael Jordan";
$arabicName = translateName($englishName);

if ($arabicName) {
    echo "English Name: " . $englishName . "<br>";
    echo "Arabic Translation: " . $arabicName . "<br>";
} else {
    echo "Translation of '" . $englishName . "' failed." . "<br>";
}


$englishName = "Invalid Name Input"; // Example of a name that might cause issues
$arabicName = translateName($englishName);

if ($arabicName) {
    echo "English Name: " . $englishName . "<br>";
    echo "Arabic Translation: " . $arabicName . "<br>";
} else {
    echo "Translation of '" . $englishName . "' failed." . "<br>";
}

?>
</body>
</html>