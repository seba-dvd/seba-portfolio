<?php
/**
 * The page of one project: proiect.php?id=4
 *
 * The id comes from the address, so it is untrusted input:
 * filter_input() accepts only a whole number >= 1, anything else
 * (?id=abc, ?id=-1, ?id=<script>) becomes false and the visitor gets a 404.
 */
require_once __DIR__ . '/includes/functii.php';
$proiecte = require __DIR__ . '/includes/proiecte-date.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$proiect = is_int($id) ? gasesteProiect($proiecte, $id) : null;

if ($proiect === null) {
    require __DIR__ . '/404.php';
    exit;
}

$pagina = 'proiecte';
$titlu = tr($proiect['titlu']);
$temaCyber = true;
require __DIR__ . '/includes/antet.php';
?>

    <main>
        <p><a class="link" href="proiecte.php">cd ../proiecte</a></p>

        <article class="card proiect-detaliu <?= e($proiect['tip']) ?>">
            <div class="proiect-cap">
                <span class="icon"><?= e($proiect['icon']) ?></span>
                <span class="eticheta"><?= e(tr($proiect['eticheta'])) ?></span>
            </div>

            <h1><?= e(tr($proiect['titlu'])) ?></h1>
            <p class="proiect-scurt"><?= e(tr($proiect['scurt'])) ?></p>

            <?php if ($proiect['rol'] !== null): ?>
                <p class="rol"><span>rolul meu:</span> <?= e(tr($proiect['rol'])) ?></p>
            <?php endif; ?>

            <h2>// Ce face</h2>
            <ul class="functii">
                <?php foreach (tr($proiect['functii']) as $functie): ?>
                    <li><?= e($functie) ?></li>
                <?php endforeach; ?>
            </ul>

            <h2>// Tehnologii</h2>
            <ul class="taguri">
                <?php foreach ($proiect['tehnologii'] as $tehnologie): ?>
                    <li><?= e($tehnologie) ?></li>
                <?php endforeach; ?>
            </ul>

            <h2>// Cod sursă</h2>
            <?php if ($proiect['repo'] === null): ?>
                <p class="nota">Codul nu este publicat pe GitHub.</p>
            <?php elseif ($proiect['privat']): ?>
                <p class="nota">🔒 Depozit privat: codul este disponibil la cerere.</p>
            <?php else: ?>
                <a class="btn-github" href="https://github.com/<?= e($proiect['repo']) ?>" target="_blank" rel="noopener noreferrer">Deschide pe GitHub ↗</a>
            <?php endif; ?>
        </article>
    </main>

<?php require __DIR__ . '/includes/subsol.php'; ?>
