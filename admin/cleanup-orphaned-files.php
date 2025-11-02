<?php
/**
 * Admin Cleanup Orphaned Files Endpoint
 * 
 * Manually trigger cleanup of orphaned files
 * Admin-only access required
 */

require_once 'common/config.php';
require_once 'common/functions.php';
require_once 'common/cleanup_functions.php';

// Check if user is admin (you may need to adjust this check based on your auth system)
$user_id = getActiveUser();
if (!$user_id) {
    die('Unauthorized access');
}

// Set content type
header('Content-Type: application/json');

// Get action parameter
$action = isset($_GET['action']) ? $_GET['action'] : 'scan';
$confirmDelete = isset($_GET['confirm']) && $_GET['confirm'] == '1';

$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    switch ($action) {
        case 'scan':
            // Scan for orphaned files without deleting
            $result = cleanupOrphanedFiles(true);
            $response['success'] = true;
            $response['message'] = 'Found ' . $result['count'] . ' orphaned files';
            $response['data'] = $result;
            break;
            
        case 'delete':
            if (!$confirmDelete) {
                throw new Exception('Confirmation required to delete files');
            }
            
            // Delete orphaned files
            $result = cleanupOrphanedFiles(false);
            $response['success'] = true;
            $response['message'] = 'Deleted ' . $result['deleted'] . ' orphaned files';
            $response['data'] = $result;
            break;
            
        case 'clean_temp':
            // Clean temporary passport files
            $deletedCount = cleanupTempPassportFiles();
            $response['success'] = true;
            $response['message'] = 'Cleaned up ' . $deletedCount . ' temporary files';
            $response['data'] = ['deleted_count' => $deletedCount];
            break;
            
        case 'stats':
            // Get upload directory statistics
            $stats = getUploadDirectoryStats();
            $response['success'] = true;
            $response['message'] = 'Upload directory statistics retrieved';
            $response['data'] = $stats;
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// Return JSON if it's an AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    echo json_encode($response);
    exit;
}

// Otherwise, display HTML interface
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cleanup Orphaned Files - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        <h3 class="mb-0"><i class="fas fa-broom me-2"></i>Cleanup Orphaned Files</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> This tool will find and optionally delete files from the upload directory
                            that are not referenced in the database. Use with caution!
                        </div>
                        
                        <!-- Stats Section -->
                        <div class="mb-4">
                            <h5>Upload Directory Statistics</h5>
                            <button class="btn btn-info" id="loadStatsBtn">
                                <i class="fas fa-chart-bar me-2"></i>Load Statistics
                            </button>
                            <div id="statsContainer" class="mt-3" style="display: none;"></div>
                        </div>
                        
                        <!-- Scan Section -->
                        <div class="mb-4">
                            <h5>Scan for Orphaned Files</h5>
                            <button class="btn btn-primary" id="scanBtn">
                                <i class="fas fa-search me-2"></i>Scan for Orphaned Files
                            </button>
                            <div id="scanResults" class="mt-3" style="display: none;"></div>
                        </div>
                        
                        <!-- Cleanup Temp Section -->
                        <div class="mb-4">
                            <h5>Clean Temporary Files</h5>
                            <button class="btn btn-warning" id="cleanTempBtn">
                                <i class="fas fa-trash me-2"></i>Clean Temp Files (>1 hour old)
                            </button>
                            <div id="tempResults" class="mt-3" style="display: none;"></div>
                        </div>
                        
                        <!-- Delete Section -->
                        <div id="deleteSection" style="display: none;">
                            <h5 class="text-danger">Delete Orphaned Files</h5>
                            <div class="alert alert-danger">
                                <strong>DANGER:</strong> This will permanently delete the files listed above!
                            </div>
                            <button class="btn btn-danger" id="deleteBtn">
                                <i class="fas fa-trash-alt me-2"></i>Confirm Delete All Orphaned Files
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Load Statistics
        document.getElementById('loadStatsBtn').addEventListener('click', async function() {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
            
            try {
                const response = await fetch('cleanup-orphaned-files.php?action=stats', {
                    headers: {'X-Requested-With': 'XMLHttpRequest'}
                });
                const result = await response.json();
                
                if (result.success) {
                    displayStats(result.data);
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error loading stats: ' + error.message);
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-chart-bar me-2"></i>Load Statistics';
            }
        });
        
        // Scan for orphaned files
        document.getElementById('scanBtn').addEventListener('click', async function() {
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Scanning...';
            
            try {
                const response = await fetch('cleanup-orphaned-files.php?action=scan', {
                    headers: {'X-Requested-With': 'XMLHttpRequest'}
                });
                const result = await response.json();
                
                if (result.success) {
                    displayScanResults(result.data);
                    if (result.data.count > 0) {
                        document.getElementById('deleteSection').style.display = 'block';
                    }
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error scanning: ' + error.message);
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-search me-2"></i>Scan for Orphaned Files';
            }
        });
        
        // Clean temp files
        document.getElementById('cleanTempBtn').addEventListener('click', async function() {
            if (!confirm('Clean temporary files older than 1 hour?')) return;
            
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Cleaning...';
            
            try {
                const response = await fetch('cleanup-orphaned-files.php?action=clean_temp', {
                    headers: {'X-Requested-With': 'XMLHttpRequest'}
                });
                const result = await response.json();
                
                if (result.success) {
                    const tempResults = document.getElementById('tempResults');
                    tempResults.innerHTML = `
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>${result.message}
                        </div>
                    `;
                    tempResults.style.display = 'block';
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error cleaning: ' + error.message);
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-trash me-2"></i>Clean Temp Files (>1 hour old)';
            }
        });
        
        // Delete orphaned files
        document.getElementById('deleteBtn').addEventListener('click', async function() {
            if (!confirm('Are you ABSOLUTELY SURE you want to delete these files? This cannot be undone!')) return;
            
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';
            
            try {
                const response = await fetch('cleanup-orphaned-files.php?action=delete&confirm=1', {
                    headers: {'X-Requested-With': 'XMLHttpRequest'}
                });
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error deleting: ' + error.message);
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-trash-alt me-2"></i>Confirm Delete All Orphaned Files';
            }
        });
        
        function displayStats(stats) {
            const container = document.getElementById('statsContainer');
            let html = `
                <div class="alert alert-info">
                    <strong>Total Files:</strong> ${stats.total_files}<br>
                    <strong>Total Size:</strong> ${stats.total_size_formatted}<br>
                    <strong>Directory:</strong> ${stats.directory}
                </div>
                <h6>File Types:</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Extension</th>
                            <th>Count</th>
                            <th>Total Size</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            for (const [ext, data] of Object.entries(stats.file_types)) {
                html += `<tr>
                    <td>.${ext}</td>
                    <td>${data.count}</td>
                    <td>${formatBytes(data.size)}</td>
                </tr>`;
            }
            
            html += `</tbody></table>`;
            container.innerHTML = html;
            container.style.display = 'block';
        }
        
        function displayScanResults(data) {
            const container = document.getElementById('scanResults');
            
            if (data.count === 0) {
                container.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>No orphaned files found!
                    </div>
                `;
            } else {
                let html = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Found ${data.count} orphaned files
                    </div>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Size</th>
                                <th>Modified</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                
                data.files.forEach(file => {
                    html += `<tr>
                        <td><code>${file.filename}</code></td>
                        <td>${formatBytes(file.size)}</td>
                        <td>${file.modified}</td>
                    </tr>`;
                });
                
                html += `</tbody></table>`;
                container.innerHTML = html;
            }
            
            container.style.display = 'block';
        }
        
        function formatBytes(bytes) {
            if (bytes >= 1073741824) {
                return (bytes / 1073741824).toFixed(2) + ' GB';
            } else if (bytes >= 1048576) {
                return (bytes / 1048576).toFixed(2) + ' MB';
            } else if (bytes >= 1024) {
                return (bytes / 1024).toFixed(2) + ' KB';
            } else {
                return bytes + ' bytes';
            }
        }
    });
    </script>
</body>
</html>

