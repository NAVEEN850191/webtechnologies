/* assets/js/main.js – shared JavaScript for LibraryHub */

/* ── Live search / filter on catalog page ─────────────────────────────── */
const searchInput  = document.getElementById('searchInput');
const genreFilter  = document.getElementById('genreFilter');
const bookCards    = document.querySelectorAll('.book-card');

function filterBooks() {
  const q = (searchInput ? searchInput.value : '').toLowerCase();
  const g = (genreFilter ? genreFilter.value : '').toLowerCase();

  bookCards.forEach(card => {
    const title  = (card.dataset.title  || '').toLowerCase();
    const author = (card.dataset.author || '').toLowerCase();
    const genre  = (card.dataset.genre  || '').toLowerCase();

    const matchQ = !q || title.includes(q) || author.includes(q);
    const matchG = !g || genre === g;
    card.style.display = matchQ && matchG ? '' : 'none';
  });
}

if (searchInput) searchInput.addEventListener('input', filterBooks);
if (genreFilter) genreFilter.addEventListener('change', filterBooks);

/* ── Hero search → redirects to catalog with ?q= ─────────────────────── */
const heroForm = document.getElementById('heroSearchForm');
if (heroForm) {
  heroForm.addEventListener('submit', e => {
    e.preventDefault();
    const q = document.getElementById('heroSearch').value.trim();
    if (q) window.location.href = 'catalog.php?q=' + encodeURIComponent(q);
  });
}

/* ── Register form validation ─────────────────────────────────────────── */
const regForm = document.getElementById('registerForm');
if (regForm) {
  regForm.addEventListener('submit', e => {
    const pass  = document.getElementById('password').value;
    const pass2 = document.getElementById('password2').value;
    const name  = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();

    if (!name || !email || !pass || !pass2) {
      e.preventDefault();
      showAlert('regAlert', 'All fields are required.', 'error');
      return;
    }
    if (pass.length < 6) {
      e.preventDefault();
      showAlert('regAlert', 'Password must be at least 6 characters.', 'error');
      return;
    }
    if (pass !== pass2) {
      e.preventDefault();
      showAlert('regAlert', 'Passwords do not match.', 'error');
    }
  });
}

/* ── Contact form: basic validation ──────────────────────────────────── */
const contactForm = document.getElementById('contactForm');
if (contactForm) {
  contactForm.addEventListener('submit', e => {
    const name    = document.getElementById('cname').value.trim();
    const email   = document.getElementById('cemail').value.trim();
    const message = document.getElementById('cmessage').value.trim();
    const emailRx = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!name || !email || !message) {
      e.preventDefault();
      showAlert('contactAlert', 'Please fill in all fields.', 'error');
      return;
    }
    if (!emailRx.test(email)) {
      e.preventDefault();
      showAlert('contactAlert', 'Please enter a valid email address.', 'error');
    }
  });
}

/* ── Admin: confirm before delete ────────────────────────────────────── */
document.querySelectorAll('.delete-btn').forEach(btn => {
  btn.addEventListener('click', e => {
    if (!confirm('Delete this book? This cannot be undone.')) e.preventDefault();
  });
});

/* ── Borrow: confirm borrow action ──────────────────────────────────── */
document.querySelectorAll('.borrow-btn').forEach(btn => {
  btn.addEventListener('click', e => {
    if (!confirm('Borrow "' + (btn.dataset.title||'this book') + '"?')) e.preventDefault();
  });
});

/* ── Return: confirm return action ──────────────────────────────────── */
document.querySelectorAll('.return-btn').forEach(btn => {
  btn.addEventListener('click', e => {
    if (!confirm('Mark this book as returned?')) e.preventDefault();
  });
});

/* ── Helper: show inline alert ───────────────────────────────────────── */
function showAlert(id, msg, type) {
  const el = document.getElementById(id);
  if (!el) return;
  el.textContent = msg;
  el.className = 'alert alert-' + (type === 'error' ? 'error' : 'success');
  el.style.display = 'block';
}

/* ── Auto-hide flash alerts after 4s ────────────────────────────────── */
document.querySelectorAll('.alert[data-auto-hide]').forEach(el => {
  setTimeout(() => el.style.opacity = '0', 4000);
  setTimeout(() => el.remove(), 4500);
});
