<?php
// fix_storage.php
// FORCE Link: public/admin/storage -> storage/app/public (Direct)

// Relative path from public/admin/ to storage/app/public/
$targetProjectRoot = __DIR__ . '/../../'; 
$targetRelative = '../../storage/app/public';
$linkName = 'storage';

echo "<h1>Storage Fixer (Direct Mode)</h1>";
echo "Current Dir: " . __DIR__ . "<br>";

// 1. Check if Target Exists
if (!is_dir($targetRelative)) {
    echo "<strong style='color:red'>Target Directory NOT FOUND at: $targetRelative</strong><br>";
    // Try to find it?
    echo "Files in ../../: " . implode(', ', scandir('../../')) . "<br>";
} else {
    echo "<strong style='color:green'>Target Directory FOUND at: $targetRelative</strong><br>";
}

// 2. Remove existing link/folder if exists
if (file_exists($linkName)) {
    echo "Removing existing '$linkName'... ";
    if (is_link($linkName)) {
        unlink($linkName); // Delete symlink
        echo "Deleted Symlink.<br>";
    } elseif (is_dir($linkName)) {
        // Warning: Don't delete a real directory full of files!
        // But in public/admin/storage, it should only be a link.
        // Let's check if empty
        if (count(scandir($linkName)) <= 2) {
             rmdir($linkName);
             echo "Deleted Directory.<br>";
        } else {
            echo "<strong style='color:red'>WARNING: '$linkName' is a real directory and not empty! I won't delete it.</strong><br>";
        }
    }
}

// 3. Create New Symlink
if (symlink($targetRelative, $linkName)) {
    echo "<strong style='color:green'>SUCCESS: Created symlink '$linkName' -> '$targetRelative'</strong><br>";
} else {
    echo "<strong style='color:red'>FAILED to create symlink.</strong> (Check permissions)<br>";
}

// 4. Verify Content
if (file_exists($linkName)) {
    echo "<h3>Contents of linked folder:</h3>";
    $files = scandir($linkName);
    echo "<ul>";
    foreach ($files as $f) {
        if ($f != '.' && $f != '..') {
            echo "<li>$f</li>";
        }
    }
    echo "</ul>";
}
