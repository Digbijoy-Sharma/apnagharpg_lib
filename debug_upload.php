<?php
/**
 * Debug script for owner agreement upload functionality
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define constants
define('BASEPATH', dirname(__FILE__) . '/');
define('APPPATH', BASEPATH . 'application/');
define('FCPATH', BASEPATH);

// Start session
session_start();

// Mock session data for testing
$_SESSION['user_type'] = 'admin';
$_SESSION['user_id'] = 1;
$_SESSION['permissions'] = [1, 2, 3, 4, 5];

echo "<h1>Debug Owner Agreement Upload</h1>";
echo "<hr>";

// Test 1: Check PHP upload configuration
echo "<h2>Test 1: PHP Upload Configuration</h2>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "upload_tmp_dir: " . ini_get('upload_tmp_dir') . "<br>";
echo "file_uploads: " . ini_get('file_uploads') . "<br>";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "<br>";

// Test 2: Check directory permissions
echo "<h2>Test 2: Directory Permissions</h2>";
$upload_dir = FCPATH . 'uploads/website/';
echo "Upload directory: " . $upload_dir . "<br>";
echo "Directory exists: " . (file_exists($upload_dir) ? 'Yes' : 'No') . "<br>";
if (file_exists($upload_dir)) {
    echo "Is writable: " . (is_writable($upload_dir) ? 'Yes' : 'No') . "<br>";
    echo "Permissions: " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "<br>";
} else {
    echo "Creating directory...<br>";
    if (mkdir($upload_dir, 0755, true)) {
        echo "Directory created successfully<br>";
    } else {
        echo "Failed to create directory<br>";
    }
}

// Test 3: Check database connection
echo "<h2>Test 3: Database Connection</h2>";
try {
    $db = new mysqli('localhost', 'root', '', 'apnagharpg1');
    if ($db->connect_error) {
        echo "Database connection failed: " . $db->connect_error . "<br>";
    } else {
        echo "Database connection successful<br>";
        
        // Check if setting table exists and has rent_agreement record
        $result = $db->query("SELECT * FROM setting WHERE name = 'rent_agreement'");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "Found rent_agreement record:<br>";
            echo "Content: " . $row['content'] . "<br>";
            echo "Timestamp: " . $row['timestamp'] . "<br>";
            echo "Updated by: " . $row['updated_by'] . "<br>";
        } else {
            echo "No rent_agreement record found in setting table<br>";
        }
        
        $db->close();
    }
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "<br>";
}

// Test 4: Check FCPATH constant
echo "<h2>Test 4: FCPATH Constant</h2>";
echo "FCPATH: " . FCPATH . "<br>";
echo "Real path: " . realpath(FCPATH) . "<br>";

// Test 5: Check if CodeIgniter is loaded
echo "<h2>Test 5: CodeIgniter Files</h2>";
$ci_core = APPPATH . 'core/CodeIgniter.php';
echo "CodeIgniter core file exists: " . (file_exists($ci_core) ? 'Yes' : 'No') . "<br>";

// Test 6: Check Model.php file
echo "<h2>Test 6: Model.php File</h2>";
$model_file = APPPATH . 'models/Model.php';
echo "Model.php exists: " . (file_exists($model_file) ? 'Yes' : 'No') . "<br>";
if (file_exists($model_file)) {
    // Check if update_owner_agreement_settings function exists
    $content = file_get_contents($model_file);
    if (strpos($content, 'function update_owner_agreement_settings') !== false) {
        echo "update_owner_agreement_settings function found<br>";
        
        // Extract the function to check its code
        preg_match('/function update_owner_agreement_settings\(\)\s*\{([^}]+(?:\{[^}]*\}[^}]*)*)\}/', $content, $matches);
        if (!empty($matches[1])) {
            echo "Function code found (first 500 chars):<br>";
            echo "<pre>" . htmlspecialchars(substr($matches[1], 0, 500)) . "</pre><br>";
        }
    } else {
        echo "update_owner_agreement_settings function NOT found<br>";
    }
}

// Test 7: Create a simple upload test form
echo "<h2>Test 7: Simple Upload Test Form</h2>";
echo "<form method='post' enctype='multipart/form-data' action='debug_upload_test.php'>";
echo "<input type='file' name='test_file'><br>";
echo "<input type='submit' value='Test Upload'>";
echo "</form>";

echo "<hr>";
echo "<h2>Summary</h2>";
echo "Check the above tests to identify any configuration issues.<br>";
echo "Common issues:<br>";
echo "1. Directory permissions (uploads/website/ should be writable)<br>";
echo "2. PHP upload limits (upload_max_filesize should be sufficient)<br>";
echo "3. Database record missing or incorrect<br>";
echo "4. Session data not set properly<br>";
?>