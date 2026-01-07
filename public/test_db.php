<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>🔐 Database Connection Tester (Raw PHP)</h2>";

// 1. Manually Parse .env
$envPath = dirname(__DIR__) . '/.env';
if (!file_exists($envPath)) {
    die("❌ .env file not found at: $envPath");
}

$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];

foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    
    $parts = explode('=', $line, 2);
    if (count($parts) === 2) {
        $env[trim($parts[0])] = trim($parts[1]);
    }
}

// 2. Extract Credentials
$host = $env['DB_HOST'] ?? '127.0.0.1';
$db   = $env['DB_DATABASE'] ?? '';
$user = $env['DB_USERNAME'] ?? '';
$pass = $env['DB_PASSWORD'] ?? '';

echo "<b>Trying to Connect with:</b><br>";
echo "Host: <code>$host</code><br>";
echo "Database: <code>$db</code><br>";
echo "User: <code>$user</code><br>";
echo "Password: <code>" . substr($pass, 0, 3) . "***</code> (Hidden)<br><br>";

// 3. Attempt Connection
try {
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h3>✅ SUCCESS: Connected to Database!</h3>";
    echo "Credentials are correct. The issue is likely Laravel Cache.";
    echo "<br><br><a href='nuke_cache.php'>Click here to Nuke Cache again</a>";
    
} catch (PDOException $e) {
    echo "<h3>❌ FAILED: Access Denied</h3>";
    echo "Error: " . $e->getMessage() . "<br><br>";
    echo "<b>Diagnosis:</b> The password or username in `.env` is 100% WRONG for this server.";
    echo "<br>Please go to Hostinger -> Databases -> Change Password again.";
}
?>
