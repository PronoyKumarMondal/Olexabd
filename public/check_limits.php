<?php
echo "<h1>Server Configuration Check</h1>";
echo "Create a file named .user.ini in your public folder with 'upload_max_filesize = 128M' if these values are low.<br><br>";

$upload = ini_get('upload_max_filesize');
$post = ini_get('post_max_size');
$mem = ini_get('memory_limit');

echo "<b>Upload Max Filesize:</b> " . $upload . "<br>";
echo "<b>Post Max Size:</b> " . $post . "<br>";
echo "<b>Memory Limit:</b> " . $mem . "<br>";

echo "<hr>";
echo "<h3>Testing Permissions</h3>";
$testDir = __DIR__ . '/../storage/logs';
if (is_writable($testDir)) {
    echo "<span style='color:green'>Logs Directory is Writable.</span>";
} else {
    echo "<span style='color:red'>Logs Directory is NOT Writable (chmod 777 needed).</span>";
}
