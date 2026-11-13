<?php
$pagina = 'proiecte';
$temaCyber = true;
$scripturi = ['js/proiecte.js'];
require __DIR__ . '/includes/antet.php';

$proiecte = require __DIR__ . '/includes/proiecte-date.php';

// The two sections of the page: type of project => section title
$sectiuni = [
    'solo'   => $t['sectiune_solo'],
    'echipa' => $t['sectiune_echipa'],
];

// Projects of one type, e.g. din('solo') → only the individual ones
$din = fn(string $tip) => array_filter($proiecte, fn($p) => $p['tip'] === $tip);
?>

    <main>
        <!-- ===== Fake terminal window: the folder lists come from the data ===== -->
        <section class="terminal">
            <div class="terminal-bara">
                <span class="buton rosu"></span>
                <span class="buton galben"></span>
                <span class="buton verde"></span>
                <span class="terminal-titlu">seba@pai: ~/proiecte</span>
            </div>
            <div class="terminal-corp">
                <p><span class="prompt">$</span> whoami</p>
                <h1><?= e($t['whoami']) ?></h1>
                <p><span class="prompt">$</span> ls ./individuale</p>
                <p><?php foreach ($din('solo') as $p): ?><?= e($p['slug']) ?>/  <?php endforeach; ?></p>
                <p><span class="prompt">$</span> ls ./echipa</p>
                <p class="echipa-text"><?php foreach ($din('echipa') as $p): ?><?= e($p['slug']) ?>/  <?php endforeach; ?></p>
                <p><span class="prompt">$</span> <span class="cursor">_</span></p>
            </div>
        </section>

        <?php foreach ($sectiuni as $tip => $titluSectiune): ?>
            <h2 class="titlu-sectiune">// <?= e($titluSectiune) ?></h2>
            <section class="grid-proiecte">
                <?php foreach ($din($tip) as $p): ?>
                    <article class="card proiect <?= e($tip) ?>"
                             data-repo="<?= e($p['repo'] ?? '') ?>"<?= $p['privat'] ? ' data-privat' : '' ?>>
                        <div class="proiect-cap">
                            <span class="icon"><?= e($p['icon']) ?></span>
                            <span class="eticheta"><?= e(tr($p['eticheta'])) ?></span>
                        </div>
                        <!-- The title is the keyboard way in: a real button that opens the preview window -->
                        <h3><button class="proiect-deschide" type="button" aria-haspopup="dialog"><?= e(tr($p['titlu'])) ?></button></h3>
                        <p><?= e(tr($p['scurt'])) ?></p>
                        <?php if ($p['rol'] !== null): ?>
                            <p class="rol"><span><?= e($t['rolul_meu']) ?></span> <?= e(tr($p['rol'])) ?></p>
                        <?php endif; ?>
                        <ul class="taguri">
                            <?php foreach ($p['tehnologii'] as $tehnologie): ?>
                                <li><?= e($tehnologie) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <a class="link detalii" href="proiect.php?id=<?= (int) $p['id'] ?>"><?= e($t['detalii']) ?></a>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endforeach; ?>

        <!-- ===== Achievements ===== -->
        <h2 class="titlu-sectiune">// <?= e($t['titlu_realizari']) ?></h2>
        <section class="card">
            <ul class="realizari">
                <?php foreach ($t['realizari'] as $realizare): ?>
                    <li><?= e($realizare) ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>

    <!-- ===== Preview window: filled by js/proiecte.js when a card is clicked ===== -->
    <dialog id="fereastra" aria-labelledby="fereastra-titlu">
        <div class="terminal-bara">
            <span class="buton rosu"></span>
            <span class="buton galben"></span>
            <span class="buton verde"></span>
            <span class="terminal-titlu" id="fereastra-cale"></span>
            <button class="inchide" type="button" aria-label="<?= e($t['inchide_fereastra']) ?>">✕</button>
        </div>
        <div class="fereastra-corp">
            <h2 id="fereastra-titlu"></h2>
            <p id="fereastra-descriere"></p>
            <div id="fereastra-github"></div>
        </div>
    </dialog>

<?php require __DIR__ . '/includes/subsol.php'; ?>
