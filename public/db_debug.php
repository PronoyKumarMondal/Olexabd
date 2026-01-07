<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Laravel Config Debugger</h2>";

require dirname(__DIR__) . '/vendor/autoload.php';
$app = require_once dirname(__DIR__) . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$config = config('database.connections.mysql');

echo "<b>Laravel thinks your DB config is:</b><br>";
echo "Host: " . $config['host'] . "<br>";
echo "Database: " . $config['database'] . "<br>";
echo "Username: " . $config['username'] . "<br>";

$pass = $config['password'];
echo "Password Length: " . strlen($pass) . "<br>";
echo "Password Start: " . substr($pass, 0, 3) . "***<br>";
echo "Password End: ***" . substr($pass, -3) . "<br>";

echo "<hr>";
echo "<b>Raw .env check:</b><br>";
$envValue = env('DB_PASSWORD');
echo "env('DB_PASSWORD') returns: " . ($envValue ? (substr($envValue, 0, 3) . '...') : 'NULL/FALSE') . "<br>";
?>
