<?php
// Secure Log Viewer
ini_set('display_errors', 1);
error_reporting(E_ALL);

$logFile = dirname(__DIR__) . '/storage/logs/laravel.log';

echo "<h2>📜 Server Log Viewer</h2>";

if (!file_exists($logFile)) {
    die("❌ Log file not found at: $logFile");
}

// Read last 100 lines
$lines = file($logFile);
$lastLines = array_slice($lines, -100);

echo "<pre style='background:#222; color:#0f0; padding:10px; overflow:auto;'>";
foreach ($lastLines as $line) {
    echo htmlspecialchars($line);
}
echo "</pre>";
?>
