<?php
/**
 * Test file upload functionality
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define constants
define('BASEPATH', dirname(__FILE__) . '/');
define('APPPATH', BASEPATH . 'application/');
define('FCPATH', BASEPATH);

echo "<h1>Test File Upload</h1>";
echo "<hr>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "<h2>Upload Test Results</h2>";
    
    $file = $_FILES['test_file'];
    echo "File name: " . $file['name'] . "<br>";
    echo "File type: " . $file['type'] . "<br>";
    echo "File size: " . $file['size'] . " bytes<br>";
    echo "Temp file: " . $file['tmp_name'] . "<br>";
    echo "Error code: " . $file['error'] . "<br>";
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo "<span style='color: red;'>Upload error: ";
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
                echo "File exceeds upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                echo "File exceeds MAX_FILE_SIZE directive in HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                echo "File was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                echo "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                echo "Missing temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                echo "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                echo "File upload stopped by extension";
                break;
            default:
                echo "Unknown upload error";
        }
        echo "</span><br>";
    } else {
        // Try to move the uploaded file
        $upload_dir = FCPATH . 'uploads/website/';
        $target_file = $upload_dir . basename($file['name']);
        
        echo "Target directory: " . $upload_dir . "<br>";
        echo "Target file: " . $target_file . "<br>";
        
        // Check if directory exists and is writable
        if (!file_exists($upload_dir)) {
            echo "Creating directory: " . $upload_dir . "<br>";
            if (!mkdir($upload_dir, 0755, true)) {
                echo "<span style='color: red;'>Failed to create directory</span><br>";
            }
        }
        
        if (!is_writable($upload_dir)) {
            echo "<span style='color: red;'>Directory is not writable</span><br>";
            echo "Current permissions: " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "<br>";
        } else {
            echo "Directory is writable<br>";
            
            // Try to move the file
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                echo "<span style='color: green;'>File uploaded successfully!</span><br>";
                echo "File saved to: " . $target_file . "<br>";
                
                // Test if file exists and is readable
                if (file_exists($target_file)) {
                    echo "File exists on disk<br>";
                    echo "File size on disk: " . filesize($target_file) . " bytes<br>";
                } else {
                    echo "<span style='color: red;'>File does not exist on disk after move_uploaded_file</span><br>";
                }
            } else {
                echo "<span style='color: red;'>Failed to move uploaded file</span><br>";
                
                // Check for specific errors
                echo "Checking error conditions:<br>";
                echo "is_uploaded_file(): " . (is_uploaded_file($file['tmp_name']) ? 'Yes' : 'No') . "<br>";
                echo "file_exists(tmp): " . (file_exists($file['tmp_name']) ? 'Yes' : 'No') . "<br>";
                
                // Check PHP error log
                echo "Last PHP error: ";
                $error = error_get_last();
                if ($error) {
                    print_r($error);
                } else {
                    echo "None";
                }
                echo "<br>";
            }
        }
    }
    
    echo "<hr>";
}

// Show link back to debug page
echo "<a href='debug_upload.php'>Back to Debug Page</a>";
?>