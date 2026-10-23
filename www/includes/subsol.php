<?php
/**
 * Shared bottom of every page: the footer, the page's own scripts, </body> and </html>.
 *
 * A page may set $scripturi = ['js/....js', ...] before including it.
 */
$scripturi = $scripturi ?? [];
?>
    <footer>
        <p>&copy; <?= date('Y') ?> David Sebastian Pilat</p>
    </footer>
<?php foreach ($scripturi as $script): ?>
    <script src="<?= e($script) ?>"></script>
<?php endforeach; ?>
</body>
</html>
