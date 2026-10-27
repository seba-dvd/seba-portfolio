<?php
$pagina = 'despre';
require __DIR__ . '/includes/antet.php';

$proiecte = require __DIR__ . '/includes/proiecte-date.php';
?>

    <main class="despre-pagina">
        <h1 class="titlu-pagina"><?= e($t['titlu_despre']) ?></h1>

        <!-- ===== Who I am: photo, short intro and four quick facts ===== -->
        <section class="card profil">
            <img class="profil-poza" src="img/profil-rotund.jpg" alt="<?= e($t['poza_alt']) ?>" width="160" height="160">
            <div>
                <h2><?= e($t['profil_salut']) ?></h2>
                <p><?= e($t['acasa_despre']) ?></p>
            </div>
        </section>

        <ul class="info-tiles">
            <?php foreach ($t['despre_info'] as $info): ?>
                <li class="card info-tile">
                    <?= iconita($info['icon']) ?>
                    <span class="info-eticheta"><?= e($info['eticheta']) ?></span>
                    <strong><?= e($info['valoare']) ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>

        <!-- ===== The story + what I'm working on now ===== -->
        <h2 class="titlu-sectiune"><?= e($t['titlu_poveste']) ?></h2>
        <section class="despre-intro">
            <article class="card">
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
                    <span class="icon-cerc" aria-hidden="true"><?= e($hobby['icon']) ?></span>
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

        <!-- ===== Timeline: newest first, one card per step (the texts are in includes/lang/) ===== -->
        <h2 class="titlu-sectiune"><?= e($t['titlu_parcurs']) ?></h2>
        <ol class="timeline-carduri">
            <?php foreach ($t['parcurs'] as $pas): ?>
                <li class="card">
                    <span class="an"><?= e($pas['an']) ?></span>
                    <p><?= e($pas['text']) ?></p>
                </li>
            <?php endforeach; ?>
        </ol>

        <!-- ===== Where to go next: the projects (in the cyber theme) and the travel map ===== -->
        <h2 class="titlu-sectiune"><?= e($t['titlu_mai_departe']) ?></h2>
        <section class="mai-departe">
            <a class="card card-link tema-cyber" href="proiecte.php">
                <h3>&gt; ls ./proiecte</h3>
                <p><?php foreach ($proiecte as $p): ?><?= e($p['slug']) ?>/  <?php endforeach; ?></p>
                <span class="link"><?= e($t['proiecte_link']) ?></span>
            </a>

            <a class="card card-link" href="harta.php">
                <h3><?= e($t['harta_titlu']) ?></h3>
                <p><?= e($t['harta_text']) ?></p>
                <p class="legenda">
                    <span class="punct vizitat"></span> <?= e($t['harta_vizitate']) ?>
                    <span class="punct dorit"></span> <?= e($t['harta_dorite']) ?>
                </p>
                <span class="link"><?= e($t['harta_link']) ?></span>
            </a>
        </section>
    </main>

<?php require __DIR__ . '/includes/subsol.php'; ?>
