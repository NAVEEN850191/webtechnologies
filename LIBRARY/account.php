<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$uid  = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$uid")->fetch_assoc();

$active  = $conn->query("SELECT bl.*, b.title, b.author, b.cover_emoji FROM borrow_log bl JOIN books b ON bl.book_id=b.id WHERE bl.user_id=$uid AND bl.status='active'");
$history = $conn->query("SELECT COUNT(*) AS c FROM borrow_log WHERE user_id=$uid AND status='returned'")->fetch_assoc()['c'];
$overdue = $conn->query("SELECT COUNT(*) AS c FROM borrow_log WHERE user_id=$uid AND status='active' AND due_date < CURDATE()")->fetch_assoc()['c'];

$msg = '';
// Update profile
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_profile'])) {
    $name  = trim($_POST['name']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $stmt  = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
    $stmt->bind_param("ssi", $name, $email, $uid);
    if ($stmt->execute()) {
        $_SESSION['name'] = $name;
        $msg = 'Profile updated successfully.';
        $user['name']  = $name;
        $user['email'] = $email;
    } else {
        $msg = 'Update failed. Email may already be in use.';
    }
}
?>
<div class="page">
  <h1 class="page-title">My Account</h1>

  <?php if ($msg): ?>
  <div class="alert alert-success" data-auto-hide><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:2rem;">

    <!-- Profile card -->
    <div>
      <div style="background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow);padding:1.5rem;margin-bottom:1.5rem;">
        <div style="text-align:center;margin-bottom:1.2rem;">
          <div style="width:72px;height:72px;background:var(--brown);color:var(--gold-light);border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:var(--font-head);font-size:1.8rem;margin:0 auto .75rem;">
            <?= strtoupper(mb_substr($user['name'],0,1)) ?>
          </div>
          <strong style="font-family:var(--font-head);font-size:1.2rem;color:var(--brown);"><?= htmlspecialchars($user['name']) ?></strong><br>
          <span style="font-size:.85rem;color:var(--muted);"><?= htmlspecialchars($user['email']) ?></span><br>
          <span style="font-size:.78rem;background:var(--parchment);color:var(--brown-light);padding:.2rem .6rem;border-radius:20px;margin-top:.4rem;display:inline-block;">
            <?= ucfirst($user['role']) ?>
          </span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:.5rem;text-align:center;border-top:1px solid var(--parchment);padding-top:1rem;">
          <div><strong style="color:var(--brown);font-size:1.3rem;"><?= $active->num_rows ?></strong><br><span style="font-size:.78rem;color:var(--muted);">Active</span></div>
          <div><strong style="color:var(--brown);font-size:1.3rem;"><?= $history ?></strong><br><span style="font-size:.78rem;color:var(--muted);">Returned</span></div>
          <div><strong style="color:<?= $overdue>0?'var(--danger)':'var(--brown)' ?>;font-size:1.3rem;"><?= $overdue ?></strong><br><span style="font-size:.78rem;color:var(--muted);">Overdue</span></div>
        </div>
      </div>

      <!-- Edit profile -->
      <div style="background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow);padding:1.5rem;">
        <h3 style="font-family:var(--font-head);color:var(--brown);margin-bottom:1rem;">Edit Profile</h3>
        <form method="POST">
          <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
          </div>
          <button type="submit" name="update_profile" class="btn btn-primary btn-full">Save Changes</button>
        </form>
      </div>
    </div>

    <!-- Currently borrowed -->
    <div>
      <h2 class="section-heading" style="margin-top:0;">Currently Borrowed</h2>
      <?php
      $active->data_seek(0);
      if ($active->num_rows===0): ?>
        <div style="background:var(--white);border-radius:var(--radius);padding:2rem;text-align:center;box-shadow:var(--shadow);color:var(--muted);">
          <div style="font-size:3rem;margin-bottom:.5rem;">📚</div>
          <p>No books borrowed. <a href="catalog.php">Browse catalog →</a></p>
        </div>
      <?php else:
        while ($row = $active->fetch_assoc()):
          $overdue_item = strtotime($row['due_date']) < time();
      ?>
      <div style="background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow);padding:1.1rem;display:flex;gap:1rem;align-items:flex-start;margin-bottom:1rem;border-left:4px solid <?= $overdue_item?'var(--danger)':'var(--success)' ?>;">
        <div style="font-size:2.5rem;flex-shrink:0;"><?= htmlspecialchars($row['cover_emoji']) ?></div>
        <div style="flex:1;">
          <div style="font-family:var(--font-head);color:var(--brown);font-size:1rem;"><?= htmlspecialchars($row['title']) ?></div>
          <div style="font-size:.82rem;color:var(--muted);">by <?= htmlspecialchars($row['author']) ?></div>
          <div style="font-size:.82rem;margin-top:.4rem;color:<?= $overdue_item?'var(--danger)':'var(--success)' ?>;">
            <?= $overdue_item?'⚠️ Overdue! ' : '📅 Due: ' ?><?= $row['due_date'] ?>
          </div>
          <a href="borrow.php?action=return&log_id=<?= $row['id'] ?>"
             class="btn btn-success btn-sm return-btn" style="margin-top:.5rem;">Return</a>
        </div>
      </div>
      <?php endwhile; endif; ?>
      <div style="margin-top:1rem;">
        <a href="borrow.php" class="btn btn-primary">View All Borrows →</a>
      </div>
    </div>

  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
