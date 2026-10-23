<?php
/**
 * Shared top of every page: <head>, the menu and the opening <body>.
 *
 * Before including it, a page sets:
 *   $pagina      which menu item is active: 'acasa', 'despre', 'proiecte', 'cv', 'contact' ('' for none)
 *   $titlu       the page name in the browser tab (optional: by default the menu title of $pagina)
 *   $cheieTitlu  a key of $t['titluri'] to use instead (optional, e.g. '404')
 *   $temaCyber   true for pages in the cyber theme (optional, default false)
 */
require_once __DIR__ . '/functii.php';
require_once __DIR__ . '/limba.php';

$temaCyber = $temaCyber ?? false;
$titlu = $titlu ?? ($t['titluri'][$cheieTitlu ?? $pagina] ?? '');

// Menu items: key => file. The labels come from the dictionary.
$meniu = [
    'acasa'    => 'index.php',
    'despre'   => 'despre.php',
    'proiecte' => 'proiecte.php',
    'cv'       => 'cv.php',
    'contact'  => 'contact.php',
];

// Texts for JavaScript, embedded as JSON. The JSON_HEX_* flags turn < > & ' "
// into <-style escapes, so no text can close the <script> tag early.
$texteJs = json_encode($t['js'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
?>
<!DOCTYPE html>
<html lang="<?= e(LIMBA) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($t['descriere_site']) ?>">
    <title><?= e($titlu) ?> – David Sebastian Pilat</title>
    <link rel="stylesheet" href="css/style.css">
    <script id="texte-js" type="application/json"><?= $texteJs ?></script>
    <script src="js/tranzitii.js"></script>
</head>

<body<?= $temaCyber ? ' class="tema-cyber"' : '' ?>>
    <header>
        <nav aria-label="<?= e($t['meniu_eticheta']) ?>">
            <a class="logo" href="index.php">Seba<span>.</span></a>
            <div class="meniu">
                <?php foreach ($meniu as $cheie => $fisier): ?>
                    <a href="<?= e($fisier) ?>"<?= $cheie === $pagina ? ' class="activ" aria-current="page"' : '' ?>><?= e($t['meniu'][$cheie]) ?></a>
                <?php endforeach; ?>

                <span class="limbi" role="group" aria-label="<?= e($t['limba_eticheta']) ?>">
                    <?php foreach (LIMBI as $cod): ?>
                        <a href="<?= e(adresaInLimba($cod)) ?>" hreflang="<?= e($cod) ?>" lang="<?= e($cod) ?>"<?= $cod === LIMBA ? ' class="activ" aria-current="true"' : '' ?>><?= e(strtoupper($cod)) ?></a>
                    <?php endforeach; ?>
                </span>

                <button class="comuta-tema" type="button" aria-label="<?= e($t['schimba_tema']) ?>"><span class="tema-ic-cyber">&gt;_</span><span class="tema-ic-mint">🌿</span></button>
            </div>
        </nav>
    </header>
