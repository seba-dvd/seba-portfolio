<?php
// Shown by Apache for forbidden addresses (ErrorDocument in .htaccess),
// and by admin.php for logged-in users who are not admins.
http_response_code(403);

$pagina = '';
$cheieTitlu = '403';
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
                <p><span class="prompt">$</span> <?= e($t['e403_comanda']) ?></p>
                <h1><?= e($t['e403_titlu']) ?></h1>
                <p><?= e($t['e403_text']) ?></p>
                <p><span class="prompt">$</span> cd <a class="link" href="index.php"><?= e($t['e404_acasa']) ?></a></p>
            </div>
        </section>
    </main>

<?php require __DIR__ . '/includes/subsol.php'; ?>
