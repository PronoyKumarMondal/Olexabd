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
    echo "Images should work now.";
} else {
    echo "<h2 style='color:red'>Failed to create symlink.</h2>";
    echo "Check permissions or strictly allow symlinks in configuration.";
}
