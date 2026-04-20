<?php
// includes/header.php
// Include this at the TOP of every page:
//   require_once 'includes/header.php';  (adjust path depth as needed)
session_start();
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LibraryHub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= str_repeat('../', substr_count($current,'/')  ) ?>assets/css/style.css">
</head>
<body>

<nav class="navbar">
  <a href="index.php" class="nav-brand">📚 LibraryHub</a>
  <button class="nav-toggle" onclick="document.querySelector('.nav-links').classList.toggle('open')">☰</button>
  <ul class="nav-links">
    <li><a href="index.php"      class="<?= $current=='index.php'?'active':'' ?>">Home</a></li>
    <li><a href="catalog.php"    class="<?= $current=='catalog.php'?'active':'' ?>">Books</a></li>
    <?php if (isset($_SESSION['user_id'])): ?>
      <li><a href="borrow.php"   class="<?= $current=='borrow.php'?'active':'' ?>">Borrow/Return</a></li>
      <li><a href="account.php"  class="<?= $current=='account.php'?'active':'' ?>">My Account</a></li>
      <?php if ($_SESSION['role']==='admin'): ?>
        <li><a href="admin.php"  class="<?= $current=='admin.php'?'active':'' ?>">Admin</a></li>
      <?php endif; ?>
      <li><a href="logout.php">Logout</a></li>
    <?php else: ?>
      <li><a href="login.php"    class="<?= $current=='login.php'?'active':'' ?>">Login</a></li>
    <?php endif; ?>
    <li><a href="contact.php"    class="<?= $current=='contact.php'?'active':'' ?>">Contact</a></li>
  </ul>
</nav>
