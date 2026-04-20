<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

$error = '';
$success = '';

// ── Handle Login ────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];
        // Remember me cookie (7 days)
        if (!empty($_POST['remember'])) {
            setcookie('lh_remember', $user['email'], time()+7*24*3600, '/');
        }
        header("Location: " . ($user['role']==='admin' ? 'admin.php' : 'account.php'));
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}

// ── Handle Register ─────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['register'])) {
    $name  = trim($_POST['name']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password']  ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if ($pass !== $pass2) {
        $error = 'Passwords do not match.';
    } elseif (strlen($pass)<6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
        $stmt->bind_param("sss", $name, $email, $hash);
        if ($stmt->execute()) {
            $success = 'Account created! You can now log in.';
        } else {
            $error = 'Email already registered.';
        }
    }
}

// Pre-fill email from remember cookie
$rememberedEmail = $_COOKIE['lh_remember'] ?? '';
?>
<div class="page">
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:2rem;max-width:860px;margin:0 auto;">

    <!-- LOGIN FORM -->
    <div class="form-card" style="margin:0;">
      <h2>🔑 Login</h2>
      <?php if ($error):   ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
      <form method="POST">
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" required value="<?= htmlspecialchars($rememberedEmail) ?>">
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" required>
        </div>
        <div class="form-group" style="display:flex;align-items:center;gap:.5rem;">
          <input type="checkbox" name="remember" id="remember" style="width:auto;">
          <label for="remember" style="margin:0;font-size:.88rem;">Remember me for 7 days</label>
        </div>
        <button type="submit" name="login" class="btn btn-primary btn-full">Login</button>
      </form>
      <p style="text-align:center;margin-top:1rem;font-size:.85rem;color:var(--muted);">
        Demo admin: admin@library.com / password
      </p>
    </div>

    <!-- REGISTER FORM -->
    <div class="form-card" style="margin:0;">
      <h2>📝 Register</h2>
      <div id="regAlert" class="alert" style="display:none;"></div>
      <form method="POST" id="registerForm">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="name" id="name" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" id="email" required>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" id="password" required>
        </div>
        <div class="form-group">
          <label>Confirm Password</label>
          <input type="password" name="password2" id="password2" required>
        </div>
        <button type="submit" name="register" class="btn btn-gold btn-full">Create Account</button>
      </form>
    </div>

  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
