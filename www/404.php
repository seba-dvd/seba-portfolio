<?php
// Shown by Apache for any missing page (ErrorDocument in .htaccess),
// and by proiect.php for an id that does not exist.
http_response_code(404);

$pagina = '';
$cheieTitlu = '404';
$temaCyber = true;
require __DIR__ . '/includes/antet.php';
?>

    <main>
        <section class="terminal">
            <div class="terminal-bara">
                <span class="buton rosu"></span>
                <span class="buton galben"></span>
                <span class="buton verde"></span>
                <span class="terminal-titlu">seba@pai: ~</span>
            </div>
            <div class="terminal-corp">
                <p><span class="prompt">$</span> <?= e($t['e404_comanda']) ?></p>
                <h1><?= e($t['e404_titlu']) ?></h1>
                <p><?= e($t['e404_text']) ?></p>
                <p><span class="prompt">$</span> cd <a class="link" href="index.php"><?= e($t['e404_acasa']) ?></a></p>
            </div>
        </section>
    </main>

<?php require __DIR__ . '/includes/subsol.php'; ?>
