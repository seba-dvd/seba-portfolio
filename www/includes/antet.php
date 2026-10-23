<?php
/**
 * Shared top of every page: <head>, the menu and the opening <body>.
 *
 * Before including it, a page sets:
 *   $pagina     which menu item is active: 'acasa', 'despre', 'proiecte', 'cv', 'contact'
 *   $titlu      the page name shown in the browser tab
 *   $temaCyber  true for pages in the cyber theme (optional, default false)
 */
require_once __DIR__ . '/functii.php';

$temaCyber = $temaCyber ?? false;

// Menu items: key => [file, label]
$meniu = [
    'acasa'    => ['index.php', 'Acasă'],
    'despre'   => ['despre.php', 'Despre'],
    'proiecte' => ['proiecte.php', 'Proiecte'],
    'cv'       => ['cv.php', 'CV'],
    'contact'  => ['contact.php', 'Contact'],
];
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titlu) ?> – David Sebastian Pilat</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/tranzitii.js"></script>
</head>

<body<?= $temaCyber ? ' class="tema-cyber"' : '' ?>>
    <header>
        <nav>
            <a class="logo" href="index.php">Seba<span>.</span></a>
            <div class="meniu">
                <?php foreach ($meniu as $cheie => [$fisier, $eticheta]): ?>
                    <a href="<?= e($fisier) ?>"<?= $cheie === $pagina ? ' class="activ" aria-current="page"' : '' ?>><?= e($eticheta) ?></a>
                <?php endforeach; ?>
                <button class="comuta-tema" type="button" aria-label="Schimbă tema"><span class="tema-ic-cyber">&gt;_</span><span class="tema-ic-mint">🌿</span></button>
            </div>
        </nav>
    </header>
