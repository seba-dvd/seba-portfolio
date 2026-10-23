<?php
$pagina = 'acasa';
require __DIR__ . '/includes/antet.php';

$proiecte = require __DIR__ . '/includes/proiecte-date.php';
?>

    <main>
        <!-- ===== Hero: text on the left, photo on the right ===== -->
        <section class="hero">
            <div class="hero-text">
                <p class="salut"><?= e($t['hero_salut']) ?></p>
                <h1>David Sebastian Pilat</h1>
                <p class="tagline">
                    <?= e($t['hero_text_inainte']) ?> <strong><?= e($t['hero_accent']) ?></strong>.
                </p>
                <p class="locatie"><?= e($t['locatie']) ?></p>
                <div class="butoane">
                    <a class="btn" href="despre.php"><?= e($t['btn_despre']) ?></a>
                    <a class="btn btn-cyber" href="proiecte.php"><?= e($t['btn_cyber']) ?></a>
                </div>
            </div>

            <div class="hero-poza">
                <img class="poza" src="img/profil.jpeg" alt="<?= e($t['poza_alt']) ?>">
                <?php foreach ($t['insigne'] as $i => $insigna): ?>
                    <span class="insigna insigna-<?= $i + 1 ?>"><?= e($insigna) ?></span>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ===== Bento grid: short previews; the same data as the other pages ===== -->
        <section class="bento">
            <article class="card despre">
                <h2><?= e($t['titlu_despre']) ?></h2>
                <p><?= e($t['acasa_despre']) ?></p>
                <a class="link" href="despre.php"><?= e($t['citeste_tot']) ?></a>
            </article>

            <article class="card hobby">
                <h2><?= e($t['titlu_hobby']) ?></h2>
                <ul class="lista-hobby">
                    <?php foreach ($t['hobby'] as $hobby): ?>
                        <li><span><?= e($hobby['icon']) ?></span> <?= e($hobby['titlu']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </article>

            <article class="card educatie">
                <h2><?= e($t['titlu_parcurs']) ?></h2>
                <ol class="timeline">
                    <?php foreach (array_slice($t['parcurs'], 0, 4) as $pas): ?>
                        <li>
                            <span class="an"><?= e($pas['an']) ?></span>
                            <?= e($pas['text']) ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </article>

            <article class="card competente">
                <h2><?= e($t['titlu_competente']) ?></h2>
                <ul class="taguri">
                    <?php foreach ($t['competente_scurt'] as $competenta): ?>
                        <li><?= e($competenta) ?></li>
                    <?php endforeach; ?>
                </ul>
            </article>

            <!-- This card switches to the cyber theme on its own -->
            <article class="card proiecte tema-cyber">
                <h2>&gt; ls ./proiecte</h2>
                <p><?php foreach ($proiecte as $p): ?><?= e($p['slug']) ?>/  <?php endforeach; ?></p>
                <a class="link" href="proiecte.php"><?= e($t['proiecte_link']) ?></a>
            </article>

            <article class="card harta">
                <h2><?= e($t['harta_titlu']) ?></h2>
                <p><?= e($t['harta_text']) ?></p>
                <p class="legenda">
                    <span class="punct vizitat"></span> <?= e($t['harta_vizitate']) ?>
                    <span class="punct dorit"></span> <?= e($t['harta_dorite']) ?>
                </p>
                <a class="link" href="harta.php"><?= e($t['harta_link']) ?></a>
            </article>
        </section>
    </main>

<?php require __DIR__ . '/includes/subsol.php'; ?>
