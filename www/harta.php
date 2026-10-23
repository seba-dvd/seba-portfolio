<?php
/**
 * The protected page: only for logged-in users (includes/auth.php).
 * For now it lists the places from the database; the map itself comes in M7.
 */
require_once __DIR__ . '/includes/functii.php';
require_once __DIR__ . '/includes/limba.php';
require_once __DIR__ . '/includes/auth.php';       // sets $utilizator or redirects to login
require_once __DIR__ . '/includes/db.php';

$puncte = toatePunctele();

$pagina = 'harta';
require __DIR__ . '/includes/antet.php';
?>

    <main>
        <section class="card harta-antet">
            <div>
                <h1><?= e(sprintf($t['bun_venit'], $utilizator)) ?></h1>
                <p><?= e($t['harta_in_lucru']) ?></p>
            </div>
            <a class="btn" href="logout.php?token=<?= e(tokenCsrf()) ?>"><?= e($t['iesire']) ?></a>
        </section>

        <section class="card">
            <h2><?= e($t['harta_locuri']) ?></h2>
            <?php if ($puncte): ?>
                <ul class="lista-locuri">
                    <?php foreach ($puncte as $punct): ?>
                        <li>
                            <span class="punct vizitat"></span>
                            <span><?= e($punct['description']) ?></span>
                            <small><?= e($punct['lat']) ?>, <?= e($punct['long']) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><?= e($t['harta_gol']) ?></p>
            <?php endif; ?>
        </section>
    </main>

<?php require __DIR__ . '/includes/subsol.php'; ?>
