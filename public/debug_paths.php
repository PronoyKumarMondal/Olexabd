<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>🕵️ Environment Debugger</h2>";

echo "<b>Current Folder:</b> " . __DIR__ . "<br>";
echo "<b>PHP Version:</b> " . phpversion() . "<br><br>";

// Define expected paths
$root = dirname(__DIR__); // Go up one level
$vendor = $root . '/vendor/autoload.php';
$bootstrap = $root . '/bootstrap/app.php';
$env = $root . '/.env';

echo "<b>Checking Paths:</b><br>";

// Check Vendor
if (file_exists($vendor)) {
    echo "✅ Vendor found at: $vendor <br>";
} else {
    echo "❌ <b>VENDOR MISSING</b> at: $vendor <br>";
    echo "Did you upload it to <code>public_html/vendor</code>?<br>";
}

// Check Bootstrap
if (file_exists($bootstrap)) {
    echo "✅ Bootstrap found at: $bootstrap <br>";
} else {
    echo "❌ Bootstrap missing at: $bootstrap <br>";
}

// Check .env
if (file_exists($env)) {
    echo "✅ .env found at: $env <br>";
} else {
    echo "❌ .env missing at: $env <br>";
}

// Try to require vendor to see if it crashes
echo "<br><b>Attempting to load autoload.php...</b><br>";
try {
    if (file_exists($vendor)) {
        require $vendor;
        echo "✅ Autoload loaded successfully!";
    } else {
        echo "⚠️ Skipping load (file missing).";
    }
} catch (Throwable $e) {
    echo "❌ <b>CRASH while loading vendor:</b> " . $e->getMessage();
}
?>
