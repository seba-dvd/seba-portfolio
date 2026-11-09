<?php
/**
 * Limits repeated login attempts (brute force), using the incercari_login table.
 *
 * Each failed login adds two rows: one for the user name and one for the
 * visitor's IP address. Both are stored as SHA-256 hashes, so the table never
 * holds a name or an address. Too many failures in the last FEREASTRA_MINUTE
 * minutes block the login form for that name (protects one account) or that
 * address (one visitor trying many names), until the old rows leave the window.
 */
require_once __DIR__ . '/db.php';

const FEREASTRA_MINUTE = 15;    // how far back failures count
const MAX_ESECURI_NUME = 5;     // per user name
const MAX_ESECURI_IP   = 20;    // per IP address

/**
 * The two keys of a login attempt: [name key, address key].
 * Names are compared without case, like the users table does.
 */
function cheiIncercare(string $nume, string $ip): array
{
    return [
        hash('sha256', 'nume:' . strtolower($nume)),
        hash('sha256', 'ip:' . $ip),
    ];
}

/**
 * How many failures a key has in the last FEREASTRA_MINUTE minutes.
 */
function esecuriRecente(string $cheie): int
{
    $minute = FEREASTRA_MINUTE;
    $cerere = mysqli_prepare(conectareBd(),
        'SELECT COUNT(*) FROM incercari_login WHERE cheie = ? AND moment > NOW() - INTERVAL ? MINUTE');
    mysqli_stmt_bind_param($cerere, 'si', $cheie, $minute);
    mysqli_stmt_execute($cerere);
    $numar = (int) mysqli_fetch_row(mysqli_stmt_get_result($cerere))[0];
    mysqli_stmt_close($cerere);
    return $numar;
}

/**
 * True when this name or this address has used up its attempts.
 */
function loginBlocat(string $nume, string $ip): bool
{
    [$cheieNume, $cheieIp] = cheiIncercare($nume, $ip);
    return esecuriRecente($cheieNume) >= MAX_ESECURI_NUME
        || esecuriRecente($cheieIp) >= MAX_ESECURI_IP;
}

/**
 * Remembers a failed login, for the name and for the address.
 */
function noteazaEsec(string $nume, string $ip): void
{
    [$cheieNume, $cheieIp] = cheiIncercare($nume, $ip);
    $cerere = mysqli_prepare(conectareBd(), 'INSERT INTO incercari_login (cheie) VALUES (?), (?)');
    mysqli_stmt_bind_param($cerere, 'ss', $cheieNume, $cheieIp);
    mysqli_stmt_execute($cerere);
    mysqli_stmt_close($cerere);
}

/**
 * After a correct login: forgets this name's failures, and drops rows older
 * than a day. The address keeps its count, so logging in to one's own account
 * can't reset the limit for trying other names.
 */
function stergeEsecuri(string $nume, string $ip): void
{
    [$cheieNume] = cheiIncercare($nume, $ip);
    $cerere = mysqli_prepare(conectareBd(),
        'DELETE FROM incercari_login WHERE cheie = ? OR moment < NOW() - INTERVAL 1 DAY');
    mysqli_stmt_bind_param($cerere, 's', $cheieNume);
    mysqli_stmt_execute($cerere);
    mysqli_stmt_close($cerere);
}
