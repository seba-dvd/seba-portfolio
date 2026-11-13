<?php
/**
 * Usernames for the autocomplete on the login form (M6).
 *
 * GET api/utilizatori.php?term=se  →  ["seba","sebastian"]  (JSON, at most 10)
 *
 * jQuery UI's autocomplete sends what the visitor typed as "term" and expects
 * a JSON array of strings.
 *
 * The brief asks for this list, so it shows which accounts exist (user
 * enumeration). To keep that small: at least 2 characters, only names that
 * start with the text, at most 10 results. The text may only contain the
 * characters allowed in a username, so "%" (the LIKE wildcard) is refused and
 * "_" is escaped: neither can turn the search into "list everyone".
 * On the public site (config: 'inregistrare_deschisa' => false) the list is always empty.
 */
require_once __DIR__ . '/../includes/db.php';

const MINIM_CARACTERE = 2;
const MAXIM_REZULTATE = 10;

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');        // the browser must treat it as JSON, nothing else
header('Cache-Control: no-store');

// On the public site only the admin logs in: listing names would only reveal the admin's
if (!inregistrareDeschisa()) {
    echo '[]';
    exit;
}

$text = $_GET['term'] ?? '';

// Same characters as a valid username (login.php); anything else can't match
if (!is_string($text) || !preg_match('/^[A-Za-z0-9._-]{' . MINIM_CARACTERE . ',30}$/', $text)) {
    echo '[]';
    exit;
}

// "_" is a wildcard in LIKE (and could be in names): escape it, then add the % for "starts with"
$model = addcslashes($text, '%_\\') . '%';

$cerere = mysqli_prepare(conectareBd(), 'SELECT user FROM users WHERE user LIKE ? ORDER BY user LIMIT ' . MAXIM_REZULTATE);
mysqli_stmt_bind_param($cerere, 's', $model);
mysqli_stmt_execute($cerere);
$nume = array_column(mysqli_fetch_all(mysqli_stmt_get_result($cerere), MYSQLI_ASSOC), 'user');
mysqli_stmt_close($cerere);

echo json_encode($nume, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
