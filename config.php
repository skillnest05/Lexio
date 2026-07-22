<?php
// ──────────────────────────────────────────────
// Lexio — Config
// Reads credentials from .env (local) or from
// Railway environment variables (production).
// ──────────────────────────────────────────────
session_start();

/**
 * Load .env file manually (no Composer needed).
 * On Railway the env vars are already injected, so
 * the .env file won't exist and this is safely skipped.
 */
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

// ── DB credentials ──────────────────────────────
$servername = getenv('DB_HOST')     ?: 'localhost';
$username   = getenv('DB_USER')     ?: 'root';
$password   = getenv('DB_PASSWORD') ?: '';
$dbname     = getenv('DB_NAME')     ?: 'lexio';

// ── Gemini API key ──────────────────────────────
$gemini_api_key = getenv('GEMINI_API_KEY') ?: '';

// ── Database connection ─────────────────────────
try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'DB connection failed: ' . $e->getMessage()]));
}
?>
