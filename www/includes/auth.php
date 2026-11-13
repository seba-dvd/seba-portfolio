<?php
/**
 * Guard for the protected pages: require it at the very top.
 * Not logged in → sent to the login page. Logged in → $utilizator holds the name.
 * A page that may also be seen without an account sets $accesFaraCont = true
 * before requiring this file: visitors then stay, with $utilizator = null.
 */
require_once __DIR__ . '/sesiune.php';
require_once __DIR__ . '/db.php';

$utilizator = utilizatorConectat();
if ($utilizator !== null && gasesteUtilizator($utilizator) === null) {
    deconecteaza();                                // the account was deleted by an admin
    $utilizator = null;
}
if ($utilizator === null && empty($accesFaraCont)) {
    header('Location: login.php', true, 303);
    exit;
}

// The page holds personal data: the browser must not keep a copy,
// so after logout the Back button can't show it again.
header('Cache-Control: no-store');
