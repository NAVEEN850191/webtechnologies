<?php
// ── Database Configuration ──────────────────────────────────────────────────
// Change these values to match your hosting provider's MySQL credentials.
define('DB_HOST', 'localhost');     // usually 'localhost' or an IP address
define('DB_USER', 'root');        // your MySQL username
define('DB_PASS', '');            // your MySQL password
define('DB_NAME', 'library_db');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
