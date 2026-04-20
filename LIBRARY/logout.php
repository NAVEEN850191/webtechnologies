<?php
session_start();

// Remove remember-me cookie
if (isset($_COOKIE['lh_remember'])) {
    setcookie('lh_remember', '', time() - 3600, '/');
}

// Destroy the session
session_unset();
session_destroy();

// Redirect to login
header("Location: login.php");
exit;
