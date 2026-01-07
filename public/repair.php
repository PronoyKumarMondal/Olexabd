<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>🛠️ System Repair Tool</h2>";

$baseDir = dirname(__DIR__);
$storage = $baseDir . '/storage';

// Define required folders (Git ignores these, ensuring they exist is critical)
$folders = [
    $storage . '/app',
    $storage . '/app/public',
    $storage . '/framework',
    $storage . '/framework/cache',
    $storage . '/framework/cache/data',
    $storage . '/framework/sessions',
    $storage . '/framework/testing',
    $storage . '/framework/views',
    $storage . '/logs',
];

echo "<b>Checking & Creating Folders:</b><br>";

foreach ($folders as $folder) {
    if (!file_exists($folder)) {
        if (mkdir($folder, 0755, true)) {
            echo "✅ Created missing: $folder <br>";
        } else {
            echo "❌ Failed to create: $folder <br>";
        }
    } else {
        echo "✅ Exists: $folder <br>";
    }
}

echo "<hr><b>Attempting Boot (Again)...</b><br>";

try {
    require $baseDir . '/vendor/autoload.php';
    $app = require_once $baseDir . '/bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();
    
    echo "<h3>🎉 REPAIR SUCCESSFUL! Kernel Booted.</h3>";
    echo "You can now run <a href='deploy.php'>deploy.php</a> to finish setup.";
    
} catch (Throwable $e) {
    echo "❌ <b>Still Crashing:</b> " . $e->getMessage();
}
?>
