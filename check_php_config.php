<?php
echo "PHP Upload Configuration Check\n";
echo "==============================\n\n";

// Check upload settings
$settings = [
    'upload_max_filesize',
    'post_max_size',
    'max_file_uploads',
    'memory_limit',
    'upload_tmp_dir',
    'file_uploads',
    'max_execution_time',
    'max_input_time'
];

foreach ($settings as $setting) {
    $value = ini_get($setting);
    echo "$setting: $value\n";
}

echo "\n\nChecking upload directory permissions:\n";
echo "====================================\n";

$upload_dir = '/Applications/XAMPP/xamppfiles/htdocs/apnagharpg/pg1/uploads/website/';
echo "Upload directory: $upload_dir\n";

if (file_exists($upload_dir)) {
    echo "Directory exists: Yes\n";
    echo "Is writable: " . (is_writable($upload_dir) ? 'Yes' : 'No') . "\n";
    echo "Permissions: " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "\n";
} else {
    echo "Directory exists: No\n";
    echo "Attempting to create directory...\n";
    if (mkdir($upload_dir, 0755, true)) {
        echo "Directory created successfully\n";
    } else {
        echo "Failed to create directory\n";
    }
}

echo "\n\nChecking FCPATH constant:\n";
echo "=========================\n";
if (defined('FCPATH')) {
    echo "FCPATH is defined: " . FCPATH . "\n";
} else {
    echo "FCPATH is NOT defined\n";
}

echo "\n\nChecking if we can write to error log:\n";
echo "=====================================\n";
error_log("Test error log message from check_php_config.php");
echo "Test error log message written\n";