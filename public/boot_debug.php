<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>🕵️ Kernel Boot Debugger</h2>";

// 1. Manually Load Autoloader
echo "1. Loading Vendor Autoload... ";
try {
    require dirname(__DIR__) . '/vendor/autoload.php';
    echo "✅ OK<br>";
} catch (Throwable $e) { die("❌ Failed: " . $e->getMessage()); }

// 2. Load App Instance
echo "2. Loading App Instance... ";
try {
    $app = require_once dirname(__DIR__) . '/bootstrap/app.php';
    echo "✅ OK<br>";
} catch (Throwable $e) { die("❌ Failed: " . $e->getMessage()); }

// 3. Make Kernel
echo "3. Resolving Http Kernel... ";
try {
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    echo "✅ OK<br>";
} catch (Throwable $e) { die("❌ Failed: " . $e->getMessage()); }

// 4. Granular Bootstrap (The tricky part)
echo "4. Starting Granular Bootstrap...<br>";

// We mimic Kernel::bootstrap() manually to see WHICH bootstrapper fails
$bootstrappers = [
    \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
    \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
    \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
    \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
    \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
    \Illuminate\Foundation\Bootstrap\BootProviders::class,
];

foreach ($bootstrappers as $bootstrapper) {
    echo "&nbsp;&nbsp;&nbsp;&nbsp;➡️ Running: " . class_basename($bootstrapper) . "... ";
    try {
        $app->bootstrapWith([$bootstrapper]);
        echo "✅ OK<br>";
    } catch (Throwable $e) {
        echo "❌ <b>CRASHED HERE!</b><br>";
        echo "<pre style='background:#fee; padding:10px; border:1px solid red;'>";
        echo "Error: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . " : " . $e->getLine() . "\n";
        echo "</pre>";
        die(); // Stop script
    }
}

echo "<hr><h3>🎉 KERNEL BOOTED SUCCESSFULLY!</h3>";
echo "If you see this, the app *should* work. Check your index.php.";
?>
