<?php
// Fix Storage Link for Hostinger
// Place this in public/fix_storage_link.php

$target = __DIR__ . '/../storage/app/public';
$shortcut = __DIR__ . '/storage';

echo "Target: $target<br>";
echo "Shortcut: $shortcut<br>";

if (file_exists($shortcut)) {
    echo "Shortcut exists. Deleting...<br>";
    // Try to remove it (it might be a file or a link)
    if (is_link($shortcut)) {
        unlink($shortcut);
        echo "Deleted existing link.<br>";
    } elseif (is_dir($shortcut)) {
        // If it's a real directory, we might need to be careful, but standard laravel is a link.
        // recursive delete if user accidentally made it a folder
        echo "It is a directory! backing up...<br>";
        rename($shortcut, $shortcut . '_backup_' . time());
    } else {
        unlink($shortcut);
    }
}

if (symlink($target, $shortcut)) {
    echo "<h2 style='color:green'>Success! Symlink created.</h2>";
} else {
    echo "<h2 style='color:red'>Failed to create symlink.</h2>";
}

// Ensure Directories Exist
$dirs = [
    $target . '/products',
    $target . '/products/featured',
    $target . '/banners'
];

echo "<h3>Checking Directories:</h3>";
foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        echo "Creating $dir ... ";
        if (mkdir($dir, 0755, true)) {
            echo "<span style='color:green'>Created.</span><br>";
        } else {
            echo "<span style='color:red'>Failed (Check Permissions).</span><br>";
        }
    } else {
        echo "Exists: $dir<br>";
    }
}

echo "<h3>Diagnostic: Contents of Target Folder ($target)</h3>";
if (is_dir($target)) {
    $files = scandir($target);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>" . $file . (is_dir($target . '/' . $file) ? ' (DIR)' : '') . "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<h3 style='color:red'>CRITICAL: Target directory does not exist!</h3>";
}

