<?php
require_once 'includes/header.php';
require_once 'includes/db.php';

// Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role']!=='admin') {
    header("Location: index.php"); exit;
}

$msg = ''; $msgType = 'success';

// ── ADD BOOK ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_book'])) {
    $title    = trim($_POST['title']    ?? '');
    $author   = trim($_POST['author']   ?? '');
    $genre    = trim($_POST['genre']    ?? '');
    $qty      = max(1, (int)($_POST['quantity'] ?? 1));
    $desc     = trim($_POST['description'] ?? '');
    $emoji    = trim($_POST['cover_emoji'] ?? '📖');
    if ($title && $author && $genre) {
        $stmt = $conn->prepare("INSERT INTO books (title,author,genre,quantity,available,description,cover_emoji) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("sssiiss", $title, $author, $genre, $qty, $qty, $desc, $emoji);
        $stmt->execute() ? $msg='Book added successfully.' : ($msg='Failed to add book.');
    } else { $msg='All required fields must be filled.'; $msgType='error'; }
}

// ── DELETE BOOK ───────────────────────────────────────────────────────────
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM books WHERE id=$id");
    $msg = 'Book deleted.';
}

// ── UPDATE BOOK (inline edit) ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_book'])) {
    $id    = (int)$_POST['book_id'];
    $title = trim($_POST['title']  ?? '');
    $qty   = max(0,(int)($_POST['quantity']??0));
    $avail = max(0,(int)($_POST['available']??0));
    $stmt  = $conn->prepare("UPDATE books SET title=?, quantity=?, available=? WHERE id=?");
    $stmt->bind_param("siii", $title, $qty, $avail, $id);
    $stmt->execute() ? $msg='Book updated.' : ($msg='Update failed.'; $msgType='error');
}

$books   = $conn->query("SELECT * FROM books ORDER BY title");
$users   = $conn->query("SELECT u.*, (SELECT COUNT(*) FROM borrow_log bl WHERE bl.user_id=u.id AND bl.status='active') AS active_borrows FROM users ORDER BY created_at DESC");
$borrows = $conn->query("SELECT bl.*, u.name AS uname, b.title AS btitle FROM borrow_log bl JOIN users u ON bl.user_id=u.id JOIN books b ON bl.book_id=b.id ORDER BY bl.borrow_date DESC LIMIT 30");

$totalB = $conn->query("SELECT COUNT(*) AS c FROM books")->fetch_assoc()['c'];
$totalU = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='user'")->fetch_assoc()['c'];
$totalA = $conn->query("SELECT COUNT(*) AS c FROM borrow_log WHERE status='active'")->fetch_assoc()['c'];
$msgs   = $conn->query("SELECT COUNT(*) AS c FROM contacts")->fetch_assoc()['c'];
?>
<div class="page">
  <h1 class="page-title">⚙️ Admin Panel</h1>

  <?php if ($msg): ?>
  <div class="alert alert-<?= $msgType==='error'?'error':'success' ?>" data-auto-hide><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <!-- Stats -->
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;margin-bottom:2rem;">
    <?php foreach ([['📚',$totalB,'Total Books'],['👤',$totalU,'Members'],['🔄',$totalA,'Active Borrows'],['✉️',$msgs,'Messages']] as [$icon,$val,$label]): ?>
    <div style="background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow);padding:1.2rem;text-align:center;">
      <div style="font-size:1.8rem;"><?= $icon ?></div>
      <strong style="font-family:var(--font-head);font-size:1.8rem;color:var(--brown);"><?= $val ?></strong>
      <div style="font-size:.8rem;color:var(--muted);"><?= $label ?></div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Add Book form -->
  <div style="background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow);padding:1.5rem;margin-bottom:2rem;">
    <h2 style="font-family:var(--font-head);color:var(--brown);margin-bottom:1.2rem;">➕ Add New Book</h2>
    <form method="POST" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;">
      <div class="form-group" style="margin:0;"><label>Title *</label><input type="text" name="title" required></div>
      <div class="form-group" style="margin:0;"><label>Author *</label><input type="text" name="author" required></div>
      <div class="form-group" style="margin:0;"><label>Genre *</label>
        <select name="genre" required>
          <option value="">Select…</option>
          <?php foreach (['Fiction','Science','History','Philosophy','Fantasy','Dystopia','Business','Biography','Other'] as $g): ?>
          <option value="<?= $g ?>"><?= $g ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group" style="margin:0;"><label>Quantity *</label><input type="number" name="quantity" min="1" value="1" required></div>
      <div class="form-group" style="margin:0;"><label>Emoji</label><input type="text" name="cover_emoji" value="📖" maxlength="4"></div>
      <div class="form-group" style="margin:0;grid-column:1/-1;"><label>Description</label><textarea name="description" rows="2"></textarea></div>
      <div style="grid-column:1/-1;"><button type="submit" name="add_book" class="btn btn-primary">Add Book</button></div>
    </form>
  </div>

  <!-- Books table -->
  <h2 class="section-heading">All Books</h2>
  <div class="table-wrap" style="margin-bottom:2rem;">
    <table>
      <thead><tr><th>ID</th><th>Title</th><th>Author</th><th>Genre</th><th>Qty</th><th>Avail</th><th>Actions</th></tr></thead>
      <tbody>
        <?php while ($b = $books->fetch_assoc()): ?>
        <tr>
          <td><?= $b['id'] ?></td>
          <td><?= htmlspecialchars($b['cover_emoji'].' '.$b['title']) ?></td>
          <td><?= htmlspecialchars($b['author']) ?></td>
          <td><?= htmlspecialchars($b['genre']) ?></td>
          <td><?= $b['quantity'] ?></td>
          <td><?= $b['available'] ?></td>
          <td style="white-space:nowrap;">
            <a href="admin.php?delete=<?= $b['id'] ?>"
               class="btn btn-danger btn-sm delete-btn">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Members table -->
  <h2 class="section-heading">Registered Members</h2>
  <div class="table-wrap" style="margin-bottom:2rem;">
    <table>
      <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Active Borrows</th><th>Joined</th></tr></thead>
      <tbody>
        <?php while ($u = $users->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($u['name']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><span class="badge <?= $u['role']==='admin'?'badge-overdue':'badge-active' ?>"><?= $u['role'] ?></span></td>
          <td><?= $u['active_borrows'] ?></td>
          <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Recent borrows -->
  <h2 class="section-heading">Recent Borrow Activity</h2>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Member</th><th>Book</th><th>Borrowed</th><th>Due</th><th>Status</th></tr></thead>
      <tbody>
        <?php while ($r = $borrows->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($r['uname']) ?></td>
          <td><?= htmlspecialchars($r['btitle']) ?></td>
          <td><?= $r['borrow_date'] ?></td>
          <td><?= $r['due_date'] ?></td>
          <td><span class="badge badge-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
