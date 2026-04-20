<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
}

$uid = $_SESSION['user_id'];
$msg = ''; $msgType = 'success';

// ── BORROW action ───────────────────────────────────────────────────────
if (isset($_GET['action'], $_GET['book_id']) && $_GET['action']==='borrow') {
    $book_id = (int)$_GET['book_id'];

    // Check how many active borrows this user has (limit 3)
    $activeCount = $conn->query("SELECT COUNT(*) AS c FROM borrow_log WHERE user_id=$uid AND status='active'")->fetch_assoc()['c'];
    if ($activeCount >= 3) {
        $msg = 'You can borrow at most 3 books at a time. Please return one first.';
        $msgType = 'error';
    } else {
        // Check book available
        $book = $conn->query("SELECT * FROM books WHERE id=$book_id AND available>0")->fetch_assoc();
        if (!$book) {
            $msg = 'Book not available.'; $msgType = 'error';
        } else {
            // Already borrowed this book?
            $dup = $conn->query("SELECT id FROM borrow_log WHERE user_id=$uid AND book_id=$book_id AND status='active'")->num_rows;
            if ($dup) {
                $msg = 'You already have this book borrowed.'; $msgType = 'error';
            } else {
                $borrow_date = date('Y-m-d');
                $due_date    = date('Y-m-d', strtotime('+14 days'));
                $conn->query("INSERT INTO borrow_log (user_id,book_id,borrow_date,due_date) VALUES ($uid,$book_id,'$borrow_date','$due_date')");
                $conn->query("UPDATE books SET available=available-1 WHERE id=$book_id");
                $msg = 'Successfully borrowed "'.htmlspecialchars($book['title']).'". Due: '.$due_date;
            }
        }
    }
}

// ── RETURN action ────────────────────────────────────────────────────────
if (isset($_GET['action'], $_GET['log_id']) && $_GET['action']==='return') {
    $log_id = (int)$_GET['log_id'];
    $log = $conn->query("SELECT * FROM borrow_log WHERE id=$log_id AND user_id=$uid AND status='active'")->fetch_assoc();
    if ($log) {
        $return_date = date('Y-m-d');
        $conn->query("UPDATE borrow_log SET status='returned', return_date='$return_date' WHERE id=$log_id");
        $conn->query("UPDATE books SET available=available+1 WHERE id=".$log['book_id']);
        $msg = 'Book returned successfully. Thank you!';
    } else {
        $msg = 'Invalid return request.'; $msgType = 'error';
    }
}

// ── Fetch user's borrow history ──────────────────────────────────────────
$active   = $conn->query("SELECT bl.*, b.title, b.author, b.cover_emoji FROM borrow_log bl JOIN books b ON bl.book_id=b.id WHERE bl.user_id=$uid AND bl.status='active' ORDER BY bl.borrow_date DESC");
$history  = $conn->query("SELECT bl.*, b.title, b.author FROM borrow_log bl JOIN books b ON bl.book_id=b.id WHERE bl.user_id=$uid AND bl.status!='active' ORDER BY bl.borrow_date DESC LIMIT 20");

// Available books to borrow
$available = $conn->query("SELECT * FROM books WHERE available>0 ORDER BY title");
?>
<div class="page">
  <h1 class="page-title">Borrow &amp; Return</h1>

  <?php if ($msg): ?>
  <div class="alert alert-<?= $msgType==='error'?'error':'success' ?>" data-auto-hide>
    <?= htmlspecialchars($msg) ?>
  </div>
  <?php endif; ?>

  <!-- Currently Borrowed -->
  <h2 class="section-heading">Currently Borrowed (<?= $active->num_rows ?>/ 3)</h2>
  <?php if ($active->num_rows===0): ?>
    <p style="color:var(--muted);">You haven't borrowed any books yet.</p>
  <?php else: ?>
  <div class="table-wrap" style="margin-bottom:2rem;">
    <table>
      <thead><tr><th>Book</th><th>Author</th><th>Borrowed</th><th>Due Date</th><th>Status</th><th>Action</th></tr></thead>
      <tbody>
        <?php while ($row = $active->fetch_assoc()):
          $overdue = strtotime($row['due_date']) < time();
        ?>
        <tr>
          <td><?= htmlspecialchars($row['cover_emoji'].' '.$row['title']) ?></td>
          <td><?= htmlspecialchars($row['author']) ?></td>
          <td><?= $row['borrow_date'] ?></td>
          <td style="color:<?= $overdue?'var(--danger)':'inherit' ?>">
            <?= $row['due_date'] ?><?= $overdue?' ⚠️ Overdue':'' ?>
          </td>
          <td><span class="badge badge-<?= $overdue?'overdue':'active' ?>"><?= $overdue?'Overdue':'Active' ?></span></td>
          <td>
            <a href="borrow.php?action=return&log_id=<?= $row['id'] ?>"
               class="btn btn-success btn-sm return-btn">Return</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

  <!-- Available to borrow -->
  <h2 class="section-heading">Available Books</h2>
  <div class="book-grid">
    <?php while ($b = $available->fetch_assoc()): ?>
    <div class="book-card">
      <div class="book-cover"><?= htmlspecialchars($b['cover_emoji']) ?></div>
      <div class="book-info">
        <div class="book-title"><?= htmlspecialchars($b['title']) ?></div>
        <div class="book-author">by <?= htmlspecialchars($b['author']) ?></div>
        <span class="book-genre"><?= htmlspecialchars($b['genre']) ?></span>
        <div class="availability available" style="margin:.5rem 0 .75rem;">✓ <?= $b['available'] ?> left</div>
        <a href="borrow.php?action=borrow&book_id=<?= $b['id'] ?>"
           class="btn btn-gold btn-sm btn-full borrow-btn"
           data-title="<?= htmlspecialchars($b['title']) ?>">Borrow</a>
      </div>
    </div>
    <?php endwhile; ?>
  </div>

  <!-- Borrow History -->
  <?php if ($history->num_rows > 0): ?>
  <h2 class="section-heading" style="margin-top:2.5rem;">Borrow History</h2>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Book</th><th>Author</th><th>Borrowed</th><th>Returned</th></tr></thead>
      <tbody>
        <?php while ($row = $history->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td><?= htmlspecialchars($row['author']) ?></td>
          <td><?= $row['borrow_date'] ?></td>
          <td><?= $row['return_date'] ?? '—' ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php require_once 'includes/footer.php'; ?>
