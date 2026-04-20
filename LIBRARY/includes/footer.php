<?php // includes/footer.php ?>
<footer class="footer">
  <p>© <?= date('Y') ?> LibraryHub &nbsp;|&nbsp; Built with HTML, CSS, JavaScript, PHP &amp; MySQL</p>
</footer>
<script src="<?= str_repeat('../', substr_count(basename($_SERVER['PHP_SELF']),'/')  ) ?>assets/js/main.js"></script>
</body>
</html>
