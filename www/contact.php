<?php
$pagina = 'contact';
require __DIR__ . '/includes/antet.php';
?>

    <main>
        <section class="card contact-intro">
            <h1><?= e($t['titluri']['contact']) ?></h1>
            <p><?= e($t['contact_text']) ?></p>
        </section>

        <section class="grid-hobby">
            <!-- The links come from includes/contacte.php (an empty one is not shown) -->
            <?php foreach (linkuriContact() as $link): ?>
                <a class="card contact-card" href="<?= e($link['href']) ?>"<?= $link['extern'] ? ' target="_blank" rel="noopener noreferrer"' : '' ?>>
                    <span class="icon-cerc"><?= iconita($link['cheie']) ?></span>
                    <h2><?= e($link['nume']) ?></h2>
                    <p><?= e($link['text']) ?></p>
                </a>
            <?php endforeach; ?>

            <div class="card contact-card">
                <span class="icon-cerc"><?= iconita('locatie') ?></span>
                <h2><?= e($t['contact_locatie']) ?></h2>
                <p><?= e($t['locatie']) ?></p>
            </div>
        </section>
    </main>

<?php require __DIR__ . '/includes/subsol.php'; ?>
