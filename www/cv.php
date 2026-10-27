<?php
$pagina = 'cv';
$scripturi = ['js/cv.js'];
require __DIR__ . '/includes/antet.php';

$s = $t['cv_sectiuni'];
?>

    <main>
        <div class="cv-actiuni">
            <button class="btn" type="button" id="descarca-pdf"><?= e($t['cv_pdf']) ?></button>
            <p><?= e($t['cv_pdf_nota']) ?></p>
        </div>

        <!-- The formal CV: one sheet, printed as-is by css/style.css @media print -->
        <article class="cv">
            <header class="cv-antet">
                <h1>David Sebastian Pilat</h1>
                <p class="cv-rol"><?= e($t['cv_rol']) ?></p>
                <p class="cv-contact">
                    <?= e($t['locatie']) ?><?php foreach (linkuriContact() as $link): ?> ·
                    <a href="<?= e($link['href']) ?>"><?= e($link['text']) ?></a><?php endforeach; ?>
                </p>
            </header>

            <section class="cv-sectiune">
                <h2><?= e($s['profil']) ?></h2>
                <p><?= e($t['cv_profil']) ?></p>
            </section>

            <section class="cv-sectiune">
                <h2><?= e($s['experienta']) ?></h2>
                <?php foreach ($t['cv_experienta'] as $post): ?>
                    <div class="cv-intrare">
                        <div class="cv-rand">
                            <h3><?= e($post['titlu']) ?></h3>
                            <span class="cv-perioada"><?= e($post['perioada']) ?></span>
                        </div>
                        <p class="cv-sub"><?= e($post['sub']) ?></p>
                        <?php if ($post['puncte']): ?>
                            <ul>
                                <?php foreach ($post['puncte'] as $punct): ?>
                                    <li><?= e($punct) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </section>

            <section class="cv-sectiune">
                <h2><?= e($s['educatie']) ?></h2>
                <?php foreach ($t['cv_educatie'] as $scoala): ?>
                    <div class="cv-intrare">
                        <div class="cv-rand">
                            <h3><?= e($scoala['titlu']) ?></h3>
                            <span class="cv-perioada"><?= e($scoala['perioada']) ?></span>
                        </div>
                        <p class="cv-sub"><?= e($scoala['sub']) ?></p>
                    </div>
                <?php endforeach; ?>
            </section>

            <section class="cv-sectiune">
                <h2><?= e($s['proiecte']) ?></h2>
                <ul>
                    <?php foreach ($t['cv_proiecte'] as $proiect): ?>
                        <li><strong><?= e($proiect['nume']) ?></strong> <?= e($proiect['text']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <section class="cv-sectiune">
                <h2><?= e($s['competente']) ?></h2>
                <dl class="cv-competente">
                    <?php foreach ($t['cv_competente'] as [$domeniu, $lista]): ?>
                        <dt><?= e($domeniu) ?></dt>
                        <dd><?= e($lista) ?></dd>
                    <?php endforeach; ?>
                </dl>
            </section>

            <section class="cv-sectiune">
                <h2><?= e($s['interese']) ?></h2>
                <p><?= e($t['cv_interese']) ?></p>
            </section>
        </article>
    </main>

<?php require __DIR__ . '/includes/subsol.php'; ?>
