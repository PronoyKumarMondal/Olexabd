<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>🩺 Deep Diagnostic Tool</h2>";

$baseDir = dirname(__DIR__);
$storage = $baseDir . '/storage';
$cache = $baseDir . '/bootstrap/cache';
$logs = $storage . '/logs';

echo "<b>Checking Write Permissions:</b><br>";

// Helper to check write
function checkWrite($path) {
    if (is_writable($path)) {
        echo "✅ Writable: $path <br>";
        return true;
    } else {
        echo "❌ <b>NOT WRITABLE:</b> $path <br>";
        return false;
    }
}

$ok = true;
$ok &= checkWrite($storage);
$ok &= checkWrite($logs);
$ok &= checkWrite($cache);

if (!$ok) {
    echo "<h3>⚠️ Permission Issue Detected!</h3>";
    echo "Please use Hostinger File Manager to set permissions for <code>storage</code> and <code>bootstrap/cache</code> to <b>775</b> or <b>777</b>.<br><hr>";
} else {
    echo "<h3>✅ Permissions look good.</h3><hr>";
}

echo "<b>Attempting to Boot App...</b><br>";

try {
    require $baseDir . '/vendor/autoload.php';
    $app = require_once $baseDir . '/bootstrap/app.php';
    
    echo "✅ App Instance Created<br>";
    
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    echo "✅ Kernel Resolved<br>";
    
    $kernel->bootstrap();
    echo "✅ <b>Kernel Booted Successfully!</b><br>";
    
    echo "<hr><h3>🚀 Everything seems fine!</h3>";
    echo "Try running <a href='deploy.php'>deploy.php</a> again.";
    
} catch (Throwable $e) {
    echo "❌ <b>CRASH DURING BOOT:</b><br>";
    echo "Class: " . get_class($e) . "<br>";
    echo "Message: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
