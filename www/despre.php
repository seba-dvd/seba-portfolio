<?php
$pagina = 'despre';
require __DIR__ . '/includes/antet.php';
?>

    <main>
        <!-- ===== Intro: the story + what I'm working on now ===== -->
        <section class="despre-intro">
            <article class="card">
                <h1><?= e($t['titlu_despre']) ?></h1>
                <?php foreach ($t['despre_paragrafe'] as $paragraf): ?>
                    <p><?= e($paragraf) ?></p>
                <?php endforeach; ?>
            </article>

            <!-- A highlighted card: the current focus -->
            <aside class="card acum">
                <p class="acum-eticheta"><?= e($t['acum_eticheta']) ?></p>
                <h2><?= e($t['acum_titlu']) ?></h2>
                <p><?= e($t['acum_text']) ?></p>
                <ul class="taguri">
                    <?php foreach ($t['acum_taguri'] as $tag): ?>
                        <li><?= e($tag) ?></li>
                    <?php endforeach; ?>
                </ul>
            </aside>
        </section>

        <!-- ===== Hobbies ===== -->
        <h2 class="titlu-sectiune"><?= e($t['titlu_hobby']) ?></h2>
        <section class="grid-hobby">
            <?php foreach ($t['hobby'] as $hobby): ?>
                <article class="card">
                    <span class="icon"><?= e($hobby['icon']) ?></span>
                    <h3><?= e($hobby['titlu']) ?></h3>
                    <p><?= e($hobby['text']) ?></p>
                </article>
            <?php endforeach; ?>
        </section>

        <!-- ===== Skills: one card per category ===== -->
        <h2 class="titlu-sectiune"><?= e($t['titlu_competente']) ?></h2>
        <section class="grid-hobby">
            <?php foreach ($t['competente'] as $categorie): ?>
                <article class="card">
                    <h3><?= e($categorie['titlu']) ?></h3>
                    <ul class="taguri">
                        <?php foreach ($categorie['taguri'] as $tag): ?>
                            <li><?= e($tag) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endforeach; ?>
        </section>

        <!-- ===== Timeline: newest first (the texts are in includes/lang/) ===== -->
        <h2 class="titlu-sectiune"><?= e($t['titlu_parcurs']) ?></h2>
        <section class="card">
            <ol class="timeline">
                <?php foreach ($t['parcurs'] as $pas): ?>
                    <li>
                        <span class="an"><?= e($pas['an']) ?></span>
                        <?= e($pas['text']) ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </section>
    </main>

<?php require __DIR__ . '/includes/subsol.php'; ?>
