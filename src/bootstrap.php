<?php
// Bootstrap: start session, prepare DB connection, ensure tables exist.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function db(): PDO
{
    static $pdo;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dbHost = getenv('DB_HOST') ?: 'mysql';
    $dbName = getenv('DB_NAME') ?: 'korka';
    $dbUser = getenv('DB_USER') ?: 'korka';
    $dbPass = getenv('DB_PASSWORD') ?: 'korka';

    try {
        // Connect to server first to create DB if needed.
        $dsn = "mysql:host={$dbHost};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        $pdo->exec("USE `{$dbName}`;");

        // Users table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                login VARCHAR(64) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                full_name VARCHAR(255) DEFAULT NULL,
                phone VARCHAR(32) DEFAULT NULL,
                is_admin TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // Applications table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS applications (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id INT UNSIGNED DEFAULT NULL,
                course VARCHAR(255) NOT NULL,
                start_date DATE NOT NULL,
                full_name VARCHAR(255) NOT NULL,
                phone VARCHAR(32) NOT NULL,
                payment ENUM('нал','безнал') NOT NULL,
                status VARCHAR(64) NOT NULL DEFAULT 'В обработке',
                date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // Reviews table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS reviews (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id INT UNSIGNED DEFAULT NULL,
                user_login VARCHAR(64) DEFAULT NULL,
                course VARCHAR(255) DEFAULT NULL,
                rating TINYINT UNSIGNED DEFAULT NULL,
                text TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    } catch (Throwable $e) {
        error_log('DB bootstrap failed: ' . $e->getMessage());
        throw $e;
    }

    return $pdo;
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isAdmin(): bool
{
    $user = currentUser();
    return $user && !empty($user['is_admin']);
}

function requireAuth(): void
{
    if (!currentUser()) {
        header('Location: /log.php');
        exit;
    }
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        header('Location: /index.php');
        exit;
    }
}
