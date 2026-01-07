<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>💥 Cache Nuker</h2>";

$baseDir = dirname(__DIR__);
$cacheDir = $baseDir . '/bootstrap/cache';

echo "Targeting: $cacheDir <br><br>";

// Files to delete
$files = scandir($cacheDir);

foreach ($files as $file) {
    if ($file === '.' || $file === '..' || $file === '.gitignore') {
        continue;
    }
    
    $path = $cacheDir . '/' . $file;
    if (is_file($path)) {
        if (unlink($path)) {
            echo "✅ Deleted: $file <br>";
        } else {
            echo "❌ Failed to delete: $file (Check Permissions)<br>";
        }
    }
}

echo "<hr><h3>Cache Cleared!</h3>";
echo "Now try <a href='deploy.php'>deploy.php</a> again.";
?>
