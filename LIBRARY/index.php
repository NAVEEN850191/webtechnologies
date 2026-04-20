<?php
require_once 'includes/header.php';
require_once 'includes/db.php';
$totalBooks   = $conn->query("SELECT SUM(quantity) AS t FROM books")->fetch_assoc()['t'] ?? 0;
$totalMembers = $conn->query("SELECT COUNT(*) AS t FROM users WHERE role='user'")->fetch_assoc()['t'] ?? 0;
$borrowed     = $conn->query("SELECT COUNT(*) AS t FROM borrow_log WHERE status='active'")->fetch_assoc()['t'] ?? 0;
$featured     = $conn->query("SELECT * FROM books ORDER BY added_at DESC LIMIT 8");
?>
<div class="hero">
  <h1>📚 Welcome to LibraryHub</h1>
  <p>Discover, borrow, and return books with ease. Your knowledge journey starts here.</p>
  <form class="hero-search" id="heroSearchForm">
    <input type="text" id="heroSearch" placeholder="Search by title or author…">
    <button type="submit">Search</button>
  </form>
</div>
<div class="stats-bar">
  <div class="stat"><strong><?= $totalBooks ?></strong><span>Total Books</span></div>
  <div class="stat"><strong><?= $totalMembers ?></strong><span>Members</span></div>
  <div class="stat"><strong><?= $borrowed ?></strong><span>Borrowed Now</span></div>
</div>
<div class="page">
  <h2 class="section-heading">Recently Added</h2>
  <div class="book-grid">
    <?php while ($b = $featured->fetch_assoc()): ?>
    <div class="book-card">
      <div class="book-cover"><?= htmlspecialchars($b['cover_emoji']) ?></div>
      <div class="book-info">
        <div class="book-title"><?= htmlspecialchars($b['title']) ?></div>
        <div class="book-author">by <?= htmlspecialchars($b['author']) ?></div>
        <span class="book-genre"><?= htmlspecialchars($b['genre']) ?></span>
        <div class="availability <?= $b['available']>0?'available':'unavailable' ?>">
          <?= $b['available']>0 ? '✓ Available ('.$b['available'].' left)' : '✗ All borrowed' ?>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
  <div style="text-align:center;margin-top:2rem;">
    <a href="catalog.php" class="btn btn-primary">Browse All Books →</a>
  </div>
  <div style="margin-top:3rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1.5rem;">
    <div style="background:var(--white);padding:1.5rem;border-radius:var(--radius);box-shadow:var(--shadow);">
      <div style="font-size:2rem;margin-bottom:.5rem;">📖</div>
      <h3 style="font-family:var(--font-head);color:var(--brown);margin-bottom:.4rem;">Easy Borrowing</h3>
      <p style="font-size:.9rem;color:var(--muted);">Borrow up to 3 books at a time with instant confirmation.</p>
    </div>
    <div style="background:var(--white);padding:1.5rem;border-radius:var(--radius);box-shadow:var(--shadow);">
      <div style="font-size:2rem;margin-bottom:.5rem;">🔍</div>
      <h3 style="font-family:var(--font-head);color:var(--brown);margin-bottom:.4rem;">Smart Search</h3>
      <p style="font-size:.9rem;color:var(--muted);">Find any book by title, author, or genre instantly.</p>
    </div>
    <div style="background:var(--white);padding:1.5rem;border-radius:var(--radius);box-shadow:var(--shadow);">
      <div style="font-size:2rem;margin-bottom:.5rem;">📅</div>
      <h3 style="font-family:var(--font-head);color:var(--brown);margin-bottom:.4rem;">Track Returns</h3>
      <p style="font-size:.9rem;color:var(--muted);">See due dates and return books from your dashboard.</p>
    </div>
  </div>
</div>
<?php require_once 'includes/footer.php'; ?>
