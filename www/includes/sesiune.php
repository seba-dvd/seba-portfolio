<?php
/**
 * Sessions: who is logged in, logging in and out, CSRF tokens.
 *
 * Only the pages of the app (login, map, logout) include this file, so casual
 * visitors of the personal site never get a session cookie.
 *
 * Differences from Lab 3, and why:
 * - Lab 3 fixed the id with session_id("mainID"): every visitor shared the same
 *   session. Here PHP picks a random id, and a new one after login
 *   (session_regenerate_id), so an id known before login is useless after it
 *   (session fixation).
 * - The cookie is HttpOnly (JavaScript can't read it, so an XSS can't steal it),
 *   SameSite=Lax (not sent with forms posted from other sites) and Secure on HTTPS.
 * - A session left alone for 30 minutes ends by itself.
 */

const SESIUNE_INACTIVA = 30 * 60;   // seconds

ini_set('session.use_strict_mode', '1');      // refuse session ids the server did not create
session_name('pai_sesiune');
session_set_cookie_params([
    'lifetime' => 0,                               // until the browser is closed
    'path'     => rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/',
    'secure'   => !empty($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

/**
 * The name of the logged-in user, or null.
 */
function utilizatorConectat(): ?string
{
    if (!isset($_SESSION['utilizator'])) {
        return null;
    }
    if (time() - ($_SESSION['ultima_activitate'] ?? 0) > SESIUNE_INACTIVA) {
        $_SESSION = [];                            // expired: forget the user, keep a clean session
        session_regenerate_id(true);
        return null;
    }
    $_SESSION['ultima_activitate'] = time();
    return $_SESSION['utilizator'];
}

/**
 * Called after a correct password or a new account.
 */
function conecteaza(int $id, string $nume): void
{
    session_regenerate_id(true);                   // new id, the old one is deleted
    $_SESSION['id_utilizator'] = $id;
    $_SESSION['utilizator'] = $nume;
    $_SESSION['ultima_activitate'] = time();
    unset($_SESSION['csrf']);                      // a fresh token for the new session
}

/**
 * Empties the session, deletes the cookie and the server-side file.
 */
function deconecteaza(): void
{
    $_SESSION = [];
    $p = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires'  => time() - 3600,
        'path'     => $p['path'],
        'secure'   => $p['secure'],
        'httponly' => $p['httponly'],
        'samesite' => $p['samesite'],
    ]);
    session_destroy();
}

/**
 * A random token kept in the session and put in every form (CSRF protection):
 * another site can make the browser send a form here, but it can't read
 * this page, so it can't know the token.
 */
function tokenCsrf(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verificaCsrf(mixed $token): bool
{
    return is_string($token) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}
