<?php
/**
 * Logout: logout.php?token=... (the link on the map page).
 *
 * The token stops other sites from logging the user out with a hidden image or
 * link (CSRF). Without a valid token nothing happens and the user goes back to
 * the map, where a fresh link is waiting.
 */
require_once __DIR__ . '/includes/sesiune.php';

if (verificaCsrf($_GET['token'] ?? null)) {
    deconecteaza();
    header('Location: login.php?iesire=1', true, 303);
} else {
    header('Location: harta.php', true, 303);
}
exit;
