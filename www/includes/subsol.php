<?php
/**
 * Shared bottom of every page: the footer, the page's own scripts, </body> and </html>.
 *
 * A page may set $scripturi before including it. Each entry is either a local
 * file ('js/....js') or a library from a CDN with its SRI hash:
 *     ['src' => 'https://...', 'integrity' => 'sha384-...']
 * With "integrity" the browser runs the file only if its hash matches, so a
 * changed file on the CDN can't run code on this site.
 * A loader that changes over time (Google Maps) can't have a hash: ['src' => ..., 'async' => true].
 */
$scripturi = $scripturi ?? [];
?>
    <footer>
        <p>&copy; <?= date('Y') ?> David Sebastian Pilat</p>
    </footer>
<?php foreach ($scripturi as $script): ?>
<?php if (is_array($script) && isset($script['integrity'])): ?>
    <script src="<?= e($script['src']) ?>" integrity="<?= e($script['integrity']) ?>" crossorigin="anonymous"></script>
<?php elseif (is_array($script)): ?>
    <script src="<?= e($script['src']) ?>"<?= !empty($script['async']) ? ' async' : '' ?>></script>
<?php else: ?>
    <script src="<?= e($script) ?>"></script>
<?php endif; ?>
<?php endforeach; ?>
</body>
</html>
