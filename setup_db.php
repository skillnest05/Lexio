<?php
// ─────────────────────────────────────────────
//  setup_db.php — Run ONCE after deployment
//  Visit: https://your-domain/setup_db.php
//  Then DELETE or rename this file for security
// ─────────────────────────────────────────────

$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (strpos($line, '=') === false) continue;
        [$key, $value] = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
        $_ENV[trim($key)] = trim($value);
    }
}

// ── DB credentials (Supports Railway Native Vars) ──
$host   = getenv('MYSQLHOST')     ?: getenv('DB_HOST')     ?: 'localhost';
$port   = getenv('MYSQLPORT')     ?: getenv('DB_PORT')     ?: '3306';
$user   = getenv('MYSQLUSER')     ?: getenv('DB_USER')     ?: 'root';
$pass   = getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD') ?: '';
$dbname = getenv('MYSQLDATABASE') ?: getenv('DB_NAME')     ?: 'lexio';

try {
    // Connect WITHOUT selecting a DB first so we can create it
    $conn = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->exec("USE `$dbname`");

    // Users table
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        id           INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        first_name   VARCHAR(50)  NOT NULL,
        last_name    VARCHAR(50)  NOT NULL,
        email        VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    // Emails table
    $conn->exec("CREATE TABLE IF NOT EXISTS emails (
        id              INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id         INT(6) UNSIGNED NOT NULL,
        sender_name     VARCHAR(100),
        recipient_name  VARCHAR(100),
        tone            VARCHAR(50),
        length          VARCHAR(20),
        prompt_text     TEXT,
        generated_email LONGTEXT,
        subject_line    VARCHAR(255),
        created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    echo json_encode(['success' => true, 'message' => 'Database and tables set up successfully. Please delete or protect this file now.']);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Setup failed: ' . $e->getMessage()]);
}
?>
