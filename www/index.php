<?php
$pagina = 'acasa';
$scripturi = ['js/acasa.js'];       // the typewriter effect on the greeting
require __DIR__ . '/includes/antet.php';
?>

    <main class="acasa">
        <!-- A short greeting and one way forward: the rest is one click away, on the About page -->
        <h1 class="scris">
            <span class="vizual-ascuns"><?= e($t['hero_titlu']) ?></span>
            <span class="scris-text" aria-hidden="true"><?= e($t['hero_titlu']) ?></span><span class="cursor" aria-hidden="true"></span>
        </h1>

        <p><?= e($t['hero_text_inainte']) ?> <strong><?= e($t['hero_accent']) ?></strong>.</p>
        <p><?= e($t['acasa_despre']) ?></p>

        <a class="link-parcurs" href="despre.php"><?= e($t['acasa_link']) ?> <span aria-hidden="true">→</span></a>

        <?= htmlSociale($t['social_eticheta']) ?>
    </main>

<?php require __DIR__ . '/includes/subsol.php'; ?>
