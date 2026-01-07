<?php
// deploy.php - Standalone Deployment Script
// Access this via https://yourdomain.com/deploy.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Security Check (Optional: Add a password query param ?key=123)
// if ($_GET['key'] !== 'secret123') die('Access Denied');

// 2. Define Paths - Try to guess if we are in Root or Public
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    // We are in ROOT (public_html)
    $baseDir = __DIR__;
} else {
    // We are likely in PUBLIC (public_html/public)
    $baseDir = __DIR__ . '/..';
}

$vendorPath = $baseDir . '/vendor/autoload.php';
$bootstrapPath = $baseDir . '/bootstrap/app.php';

// 3. Check for Vendor (Critical)
if (!file_exists($vendorPath)) {
    die("<h1>Error: Vendor Missing</h1><p>The 'vendor' folder was not found. Please upload 'vendor.zip' to your public_html folder and extract it.</p>");
}

// 4. Load Composer & Boot Laravel
require $vendorPath;
$app = require_once $bootstrapPath;

// 5. Bootstrap Kernel
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "<html><body style='font-family: sans-serif; padding: 2rem;'>";
echo "<h2>🚀 Manual Deployment Tool</h2>";

// Helper function to run artisan commands
function runCommand($command, $params = []) {
    echo "<h3>Executing: <code>php artisan $command</code></h3>";
    try {
        \Illuminate\Support\Facades\Artisan::call($command, $params);
        echo "<pre style='background: #f4f4f4; padding: 10px; border-radius: 5px; color: green;'>" . \Illuminate\Support\Facades\Artisan::output() . "</pre>";
    } catch (\Exception $e) {
        echo "<pre style='background: #ffe6e6; padding: 10px; border-radius: 5px; color: red;'>Error: " . $e->getMessage() . "</pre>";
    }
}

// 6. Run Commands
runCommand('optimize:clear');
runCommand('config:clear');

echo "<h3>Migrating Database...</h3>";
runCommand('migrate:fresh', ['--seed' => true, '--force' => true]);

echo "<h3>Linking Storage...</h3>";
if (!file_exists(__DIR__ . '/storage')) {
    runCommand('storage:link');
} else {
    echo "<pre>Storage already linked.</pre>";
}

echo "<h3>✅ Deployment Finished! Visit your homepage.</h3>";
echo "</body></html>";
?>
