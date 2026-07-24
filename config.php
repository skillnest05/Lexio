<?php
// ──────────────────────────────────────────────
// Lexio — Config
// Reads credentials from .env (local) or from
// Railway environment variables (production).
// ──────────────────────────────────────────────
session_start();

$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (strpos($line, '=') === false) continue;
        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);
        if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
            putenv("$key=$value");
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// ── DB credentials (Supports Railway Native Vars) ──
$servername = getenv('MYSQLHOST')     ?: getenv('DB_HOST')     ?: 'localhost';
$port       = getenv('MYSQLPORT')     ?: getenv('DB_PORT')     ?: '3306';
$username   = getenv('MYSQLUSER')     ?: getenv('DB_USER')     ?: 'root';
$password   = getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD') ?: '';
$dbname     = getenv('MYSQLDATABASE') ?: getenv('DB_NAME')     ?: 'lexio';

// ── Gemini API key ──────────────────────────────
$gemini_api_key = getenv('GEMINI_API_KEY') ?: '';

// ── Database connection ─────────────────────────
try {
    $conn = new PDO(
        "mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'DB connection failed: ' . $e->getMessage()]));
}
?>
