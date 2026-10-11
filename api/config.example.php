<?php
// ============================================================
//  api/config.php — Database connection
//  IMPORTANT: Replace the values below with your Hostinger
//  MySQL credentials from hPanel → Databases
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'your_db_name');       // e.g. u123456789_tiktonomics
define('DB_USER', 'your_db_user');       // e.g. u123456789_admin
define('DB_PASS', 'your_db_password');   // your MySQL password
define('DB_CHARSET', 'utf8mb4');

// Allowed origin for CORS — change to your domain
define('ALLOWED_ORIGIN', 'https://tiktonomics.com');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

function setCorsHeaders(): void {
    header('Access-Control-Allow-Origin: ' . ALLOWED_ORIGIN);
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json; charset=utf-8');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
}

function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function jsonError(string $message, int $code = 400): void {
    jsonResponse(['error' => $message], $code);
}
