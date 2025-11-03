<?php
/**
 * Cleanup Functions
 * 
 * Provides centralized cleanup functions for file uploads and orphaned files
 */

/**
 * Clean up a failed upload
 * Deletes the main file and its thumbnail
 * 
 * @param string $filename The filename (without path) or full path to delete
 * @param string $uploadDir Optional upload directory (defaults to images/uploads/)
 * @return bool Success status
 */
function cleanupFailedUpload($filename, $uploadDir = null) {
    if ($uploadDir === null) {
        $uploadDir = SRV_ROOT . 'images/uploads/';
    }
    
    // Handle both full paths and just filenames
    if (strpos($filename, '/') !== false) {
        $filePath = $filename;
        $pathInfo = pathinfo($filename);
        $uploadDir = $pathInfo['dirname'] . '/';
        $filename = $pathInfo['basename'];
    } else {
        $filePath = $uploadDir . $filename;
    }
    
    $deleted = false;
    
    // Delete main file
    if (file_exists($filePath)) {
        unlink($filePath);
        $deleted = true;
        error_log("Cleaned up failed upload: $filePath");
    }
    
    // Delete thumbnail if it exists
    $pathInfo = pathinfo($filename);
    $thumbFilename = $pathInfo['filename'] . '_thumb.jpg';
    $thumbPath = $uploadDir . $thumbFilename;
    
    if (file_exists($thumbPath)) {
        unlink($thumbPath);
        $deleted = true;
        error_log("Cleaned up thumbnail: $thumbPath");
    }
    
    return $deleted;
}

/**
 * Clean up temporary passport files
 * Removes files from the passport photo temporary directory
 * 
 * @return int Number of files deleted
 */
function cleanupTempPassportFiles() {
    if (!defined('PASSPORT_PHOTO_TEMP_DIR')) {
        return 0;
    }
    
    $tempDir = PASSPORT_PHOTO_TEMP_DIR;
    
    if (!is_dir($tempDir)) {
        return 0;
    }
    
    $deletedCount = 0;
    $files = glob($tempDir . '*');
    $currentTime = time();
    
    foreach ($files as $file) {
        if (is_file($file)) {
            // Delete files older than 1 hour
            $fileAge = $currentTime - filemtime($file);
            if ($fileAge > 3600) {
                if (unlink($file)) {
                    $deletedCount++;
                    error_log("Cleaned up temp file: $file");
                }
            }
        }
    }
    
    return $deletedCount;
}

/**
 * Find and clean up orphaned files
 * Files that exist in the uploads directory but are not referenced in the database
 * 
 * @param bool $dryRun If true, only returns list without deleting
 * @return array List of orphaned files and count
 */
function cleanupOrphanedFiles($dryRun = true) {
    global $dbConn;
    
    $uploadDir = SRV_ROOT . 'images/uploads/';
    
    if (!is_dir($uploadDir)) {
        return ['files' => [], 'count' => 0, 'error' => 'Upload directory not found'];
    }
    
    // Get all files from database
    $sql = "SELECT photo_filename, photo_filename_thumb FROM zyt_kun_photos";
    $result = dbQuery($sql);
    
    $dbFiles = [];
    while ($row = dbFetchAssoc($result)) {
        if (!empty($row['photo_filename'])) {
            $dbFiles[$row['photo_filename']] = true;
        }
        if (!empty($row['photo_filename_thumb'])) {
            $dbFiles[$row['photo_filename_thumb']] = true;
        }
    }
    
    // Get all files from upload directory
    $diskFiles = glob($uploadDir . '*');
    
    $orphanedFiles = [];
    $deletedCount = 0;
    
    foreach ($diskFiles as $filePath) {
        if (!is_file($filePath)) {
            continue;
        }
        
        $filename = basename($filePath);
        
        // Skip system files
        if (in_array($filename, ['.htaccess', 'index.html', 'index.php', '.DS_Store'])) {
            continue;
        }
        
        // Check if file is in database
        if (!isset($dbFiles[$filename])) {
            $orphanedFiles[] = [
                'filename' => $filename,
                'path' => $filePath,
                'size' => filesize($filePath),
                'modified' => date('Y-m-d H:i:s', filemtime($filePath))
            ];
            
            // Delete if not dry run
            if (!$dryRun) {
                if (unlink($filePath)) {
                    $deletedCount++;
                    error_log("Cleaned up orphaned file: $filePath");
                }
            }
        }
    }
    
    return [
        'files' => $orphanedFiles,
        'count' => count($orphanedFiles),
        'deleted' => $deletedCount,
        'dry_run' => $dryRun
    ];
}

/**
 * Cleanup files on transaction rollback
 * 
 * @param array $filePaths Array of file paths to delete
 * @return int Number of files deleted
 */
function cleanupTransactionFiles($filePaths) {
    $deletedCount = 0;
    
    foreach ($filePaths as $filePath) {
        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                $deletedCount++;
                error_log("Transaction rollback cleanup: $filePath");
            }
        }
    }
    
    return $deletedCount;
}

/**
 * Get upload directory size and file count
 * 
 * @return array Statistics about upload directory
 */
function getUploadDirectoryStats() {
    $uploadDir = SRV_ROOT . 'images/uploads/';
    
    if (!is_dir($uploadDir)) {
        return ['error' => 'Upload directory not found'];
    }
    
    $files = glob($uploadDir . '*');
    $totalSize = 0;
    $fileCount = 0;
    $fileTypes = [];
    
    foreach ($files as $file) {
        if (is_file($file)) {
            $fileCount++;
            $size = filesize($file);
            $totalSize += $size;
            
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!isset($fileTypes[$ext])) {
                $fileTypes[$ext] = ['count' => 0, 'size' => 0];
            }
            $fileTypes[$ext]['count']++;
            $fileTypes[$ext]['size'] += $size;
        }
    }
    
    return [
        'total_files' => $fileCount,
        'total_size' => $totalSize,
        'total_size_formatted' => formatBytes($totalSize),
        'file_types' => $fileTypes,
        'directory' => $uploadDir
    ];
}

/**
 * Format bytes to human-readable format
 * 
 * @param int $bytes
 * @return string Formatted size
 */
function formatBytes($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>



