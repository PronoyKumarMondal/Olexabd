<?php
// fix_storage.php
// Creates a symlink from public/admin/storage to ../storage

$target = '../storage';
$link = 'storage';

if (file_exists($link)) {
    echo "Link 'storage' already exists.<br>";
    if (is_link($link)) {
        echo "It is a symlink to: " . readlink($link) . "<br>";
    } else {
        echo "It is a DIRECTORY (Warning!).<br>";
    }
} else {
    if (symlink($target, $link)) {
        echo "Successfully created symlink: storage -> ../storage<br>";
    } else {
        echo "Failed to create symlink. Check permissions.<br>";
    }
}

echo "Current Directory: " . getcwd() . "<br>";
echo "Files:<br>";
print_r(scandir('.'));
