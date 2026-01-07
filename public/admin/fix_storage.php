<?php
// fix_storage.php
// LIST Files in storage/banners to debug missing images
error_reporting(E_ALL);
ini_set('display_errors', 1);

$dir = __DIR__ . '/storage/banners';

echo "<h1>Storage File Inspector</h1>";
echo "Looking in: $dir<br>";

if (!is_dir($dir)) {
    echo "<strong style='color:red'>Directory NOT FOUND!</strong><br>";
    echo "Is 'storage' linked? Check below:<br>";
    $link = __DIR__ . '/storage';
    if (file_exists($link)) {
        echo "'storage' exists. Is it a link? " . (is_link($link) ? "YES" : "NO") . "<br>";
        if (is_link($link)) echo "Target: " . readlink($link) . "<br>";
    } else {
        echo "'storage' does not exist in administrative public folder.<br>";
    }
} else {
    echo "Files found:<br><ul>";
    $files = scandir($dir);
    foreach ($files as $f) {
        if ($f != '.' && $f != '..') {
            echo "<li><a href='/storage/banners/$f' target='_blank'>$f</a></li>";
        }
    }
    echo "</ul>";
}
