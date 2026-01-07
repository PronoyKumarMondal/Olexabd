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
    // 4. Verify Content & Accessibility
    // Assuming $link is the path to the 'storage' directory or symlink
    $linkName = $link; // Use $link as the base for the linked folder
    if (file_exists($linkName)) {
        echo "<h3>Contents of linked folder:</h3>";
        // Check if $linkName is a directory before scanning
        if (is_dir($linkName . '/banners')) {
            $files = scandir($linkName . '/banners'); // Look specifically in banners
            echo "Found in 'storage/banners':<br><ul>";
            $sampleImage = null;
            foreach ($files as $f) {
                if ($f != '.' && $f != '..') {
                    echo "<li>$f</li>";
                    if (!$sampleImage && preg_match('/\.(jpg|png|webp)$/i', $f)) {
                        $sampleImage = $f;
                    }
                }
            }
            echo "</ul>";

            if ($sampleImage) {
                echo "<h3>Test Image: $sampleImage</h3>";
                $relPath = $linkName . '/banners/' . $sampleImage;
                echo "Path via Link: $relPath <br>";
                
                if (file_exists($relPath)) {
                    echo "<strong style='color:green'>PHP can read file via symlink!</strong><br>";
                    echo "<img src='/storage/banners/$sampleImage' style='max-width:200px; border:2px solid red;'><br>";
                    echo "If you see the image above, it works. If broken icon, Apache is blocking Symlinks.";
                } else {
                    echo "<strong style='color:red'>PHP FAILED to read file via symlink! Link is broken.</strong>";
                }
            } else {
                echo "No suitable image found in 'storage/banners' to test.";
            }
        } else {
            echo "'storage/banners' is not a directory or does not exist within the linked 'storage'.";
        }
    } else {
        echo "The 'storage' link/directory itself does not exist, so cannot verify contents.";
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
