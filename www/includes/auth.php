<?php
/**
 * Guard for the protected pages: require it at the very top.
 * Not logged in → sent to the login page. Logged in → $utilizator holds the name.
 */
require_once __DIR__ . '/sesiune.php';

$utilizator = utilizatorConectat();
if ($utilizator === null) {
    header('Location: login.php', true, 303);
    exit;
}

// The page holds personal data: the browser must not keep a copy,
// so after logout the Back button can't show it again.
header('Cache-Control: no-store');
