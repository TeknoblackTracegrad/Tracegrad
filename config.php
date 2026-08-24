<?php
/**
 * TRACEGRAD — Database Configuration
 * ------------------------------------------------
 * Default values below match a fresh XAMPP install
 * (Apache + MariaDB/MySQL, phpMyAdmin default account).
 * Edit these four constants if your setup differs.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'tracegrad_db');
define('DB_USER', 'root');
define('DB_PASS', '');          // XAMPP's default MySQL root password is blank

// ---- Do not edit below this line ----

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('<div style="font-family:sans-serif;max-width:600px;margin:80px auto;padding:24px;
        border:1px solid #f0c0c0;background:#fdf2f2;color:#8b1a1a;border-radius:10px">
        <h2 style="margin-top:0">Database connection failed</h2>
        <p>TRACEGRAD could not connect to MySQL. Please check:</p>
        <ul>
          <li>XAMPP\'s Apache <strong>and</strong> MySQL modules are both running</li>
          <li>A database named <code>tracegrad_db</code> exists (import the provided .sql file in phpMyAdmin)</li>
          <li>The credentials in <code>config.php</code> match your MySQL setup</li>
        </ul>
        <p style="font-size:13px;color:#666">Technical detail: ' . htmlspecialchars($e->getMessage()) . '</p>
        </div>');
}

// Start session for login state (used by login pages / dashboards)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ============================================================
   GEMINI AI CONFIGURATION
   ============================================================ */

if (!defined('GEMINI_API_KEY')) {

    define(
        'GEMINI_API_KEY',
        'AQ.Ab8RN6IJI_DEAXhi97r19DoAw4-sHFr3WAEh10tfpkNgs_RW_w'
    );
}


if (!defined('GEMINI_MODEL')) {

    define(
        'GEMINI_MODEL',
        'gemini-3.7-flash'
    );
}