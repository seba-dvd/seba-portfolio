<?php
/**
 * Picks the site language (ro or en) and loads its texts into $t.
 *
 * 1. index.php?lang=en  → the choice is saved in a cookie, then the browser is
 *    redirected to the same page without ?lang= (one address per page).
 * 2. Otherwise the cookie decides; with no cookie, Romanian.
 *
 * Security: the value from the address or cookie is only compared against the
 * allowlist below. It is never used to build a file path, so ?lang=../../x
 * cannot make PHP load another file (Local File Inclusion).
 */

const LIMBI = ['ro', 'en'];

$limbaCeruta = $_GET['lang'] ?? null;

if (is_string($limbaCeruta) && in_array($limbaCeruta, LIMBI, true)) {
    $caleSite = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';

    setcookie('lang', $limbaCeruta, [
        'expires'  => time() + 60 * 60 * 24 * 365,   // one year
        'path'     => $caleSite,
        'secure'   => !empty($_SERVER['HTTPS']),
        'httponly' => true,                            // JavaScript does not need it
        'samesite' => 'Lax',
    ]);

    // Same page, same parameters, without lang
    $parametri = $_GET;
    unset($parametri['lang']);
    $adresa = basename($_SERVER['SCRIPT_NAME']) . ($parametri ? '?' . http_build_query($parametri) : '');

    header('Location: ' . $adresa, true, 303);
    exit;
}

$limba = $_COOKIE['lang'] ?? 'ro';
if (!in_array($limba, LIMBI, true)) {
    $limba = 'ro';
}
define('LIMBA', $limba);

// File names are fixed here, never built from input
$dictionare = [
    'ro' => __DIR__ . '/lang/ro.php',
    'en' => __DIR__ . '/lang/en.php',
];
$t = require $dictionare[LIMBA];

/**
 * Address of the current page in another language: keeps the other
 * parameters (like ?id=4) and adds lang=...
 */
function adresaInLimba(string $cod): string
{
    $parametri = $_GET;
    $parametri['lang'] = $cod;
    return basename($_SERVER['SCRIPT_NAME']) . '?' . http_build_query($parametri);
}
