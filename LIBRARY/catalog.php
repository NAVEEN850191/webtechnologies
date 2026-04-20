<?php
require_once 'includes/header.php';
require_once 'includes/db.php';
$genres = $conn->query("SELECT DISTINCT genre FROM books ORDER BY genre");
$books  = $conn->query("SELECT * FROM books ORDER BY title ASC");
?>
<div class="page">
  <h1 class="page-title">Book Catalog</h1>
  <div class="filter-bar">
    <input type="text" id="searchInput" placeholder="Search title or author…"
           value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
    <select id="genreFilter">
      <option value="">All Genres</option>
      <?php while ($g = $genres->fetch_assoc()): ?>
      <option value="<?= strtolower(htmlspecialchars($g['genre'])) ?>">
        <?= htmlspecialchars($g['genre']) ?>
      </option>
      <?php endwhile; ?>
    </select>
  </div>
  <div class="book-grid" id="bookGrid">
    <?php while ($b = $books->fetch_assoc()): ?>
    <div class="book-card"
         data-title="<?= htmlspecialchars(strtolower($b['title'])) ?>"
         data-author="<?= htmlspecialchars(strtolower($b['author'])) ?>"
         data-genre="<?= htmlspecialchars(strtolower($b['genre'])) ?>">
      <div class="book-cover"><?= htmlspecialchars($b['cover_emoji']) ?></div>
      <div class="book-info">
        <div class="book-title"><?= htmlspecialchars($b['title']) ?></div>
        <div class="book-author">by <?= htmlspecialchars($b['author']) ?></div>
        <span class="book-genre"><?= htmlspecialchars($b['genre']) ?></span>
        <p style="font-size:.8rem;color:var(--muted);margin:.4rem 0 .6rem;line-height:1.4;">
          <?= htmlspecialchars(mb_substr($b['description'] ?? '', 0, 80)) ?>…
        </p>
        <div class="availability <?= $b['available']>0?'available':'unavailable' ?>" style="margin-bottom:.75rem;">
          <?= $b['available']>0 ? '✓ '.$b['available'].' available' : '✗ Not available' ?>
        </div>
        <?php if (isset($_SESSION['user_id']) && $b['available']>0): ?>
          <a href="borrow.php?action=borrow&book_id=<?= $b['id'] ?>"
             class="btn btn-gold btn-sm btn-full borrow-btn"
             data-title="<?= htmlspecialchars($b['title']) ?>">Borrow</a>
        <?php elseif (!isset($_SESSION['user_id'])): ?>
          <a href="login.php" class="btn btn-primary btn-sm btn-full">Login to Borrow</a>
        <?php else: ?>
          <button class="btn btn-sm btn-full" disabled style="opacity:.4;cursor:not-allowed;">Unavailable</button>
        <?php endif; ?>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
  <p id="noResults" style="display:none;text-align:center;color:var(--muted);margin-top:2rem;font-size:1.1rem;">
    No books found. Try a different search.
  </p>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const si = document.getElementById('searchInput');
  const gf = document.getElementById('genreFilter');
  const nr = document.getElementById('noResults');
  function filter() {
    const q = si.value.toLowerCase(), g = gf.value.toLowerCase();
    let vis = 0;
    document.querySelectorAll('.book-card').forEach(c => {
      const ok = (!q || c.dataset.title.includes(q) || c.dataset.author.includes(q))
               && (!g || c.dataset.genre === g);
      c.style.display = ok ? '' : 'none';
      if (ok) vis++;
    });
    nr.style.display = vis===0 ? 'block' : 'none';
  }
  si.addEventListener('input', filter);
  gf.addEventListener('change', filter);
  if (si.value) filter();
});
</script>
<?php require_once 'includes/footer.php'; ?>
