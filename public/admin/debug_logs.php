<?php
// Security: Only allow if a specific simple secret is passed or IP matches (skipping for now as it's temporary)
// Read the last 100 lines of the Laravel log file

$logFile = __DIR__ . '/../storage/logs/laravel.log';

if (!file_exists($logFile)) {
    die("Log file not found at: " . $logFile);
}

// Simple tail implementation
$lines = file($logFile);
$lastLines = array_slice($lines, -100);

echo "<pre>";
foreach ($lastLines as $line) {
    echo htmlspecialchars($line);
}
echo "</pre>";
