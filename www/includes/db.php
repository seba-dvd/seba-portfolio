<?php
/**
 * Database access with mysqli, in the procedural style from Lab 3.
 *
 * Differences from the lab, and why:
 * - the account is pai_app (rights: SELECT, INSERT), not root;
 * - the password comes from config.php, which is not in git;
 * - every value that comes from a visitor goes through a prepared statement
 *   (? placeholders), never glued into the SQL text, so it can't change the
 *   query (SQL injection);
 * - on a connection error the visitor sees a short message; the details go to
 *   the server's error log, not to the page.
 */

/**
 * The connection, opened once per request and reused.
 * $admin = true uses the pai_admin account (admin panel only); otherwise pai_app.
 */
function conectareBd(bool $admin = false): mysqli
{
    static $conexiuni = [];
    $cont = $admin ? 'admin' : 'app';
    if (isset($conexiuni[$cont])) {
        return $conexiuni[$cont];
    }

    $config = configuratie();
    $utilizator = $admin ? ($config['db_admin_user'] ?? null) : $config['db_user'];
    $parola = $admin ? ($config['db_admin_pass'] ?? null) : $config['db_pass'];
    if ($utilizator === null || $parola === null) {
        eroareBd('db_admin_user / db_admin_pass are missing in config.php (see config.example.php)');
    }

    // Errors become exceptions, so a failed query can't go unnoticed
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conexiune = mysqli_connect($config['db_host'], $utilizator, $parola, $config['db_name']);
        mysqli_set_charset($conexiune, 'utf8mb4');
    } catch (mysqli_sql_exception $e) {
        eroareBd('connection failed (' . $cont . '): ' . $e->getMessage());
    }
    return $conexiuni[$cont] = $conexiune;
}

/**
 * The settings from config.php (database, map provider), read once per request.
 */
function configuratie(): array
{
    static $config = null;
    if ($config === null) {
        $fisierConfig = __DIR__ . '/config.php';
        if (!is_file($fisierConfig)) {
            eroareBd('config.php is missing: copy includes/config.example.php to includes/config.php');
        }
        $config = require $fisierConfig;
    }
    return $config;
}

/**
 * Can visitors see the map without an account? ('harta_publica' in config.php)
 * Off by default: harta.php is the course project's protected page. The public
 * site turns it on, so employers see the map; the login is then only for the admin.
 */
function hartaPublica(): bool
{
    return (configuratie()['harta_publica'] ?? false) === true;
}

/**
 * Can anyone create an account? ('inregistrare_deschisa' in config.php)
 * On by default (the course project needs it); off on the public site.
 */
function inregistrareDeschisa(): bool
{
    return (configuratie()['inregistrare_deschisa'] ?? true) !== false;
}

/**
 * Is this user an admin? The admin usernames are listed in config.php ('admini').
 * Compared without case, like the usernames in the database (Seba = seba).
 */
function esteAdmin(?string $nume): bool
{
    if ($nume === null) {
        return false;
    }
    $admini = array_map('mb_strtolower', configuratie()['admini'] ?? []);
    return in_array(mb_strtolower($nume), $admini, true);
}

/**
 * Stops the page with a general message (HTTP 503) and logs the details.
 */
function eroareBd(string $detalii): never
{
    error_log('[pai] database: ' . $detalii);
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, 'Database error: ' . $detalii . PHP_EOL);
        exit(1);
    }
    http_response_code(503);
    exit('Baza de date nu este disponibilă momentan. / The database is not available right now.');
}

/**
 * All the places for the map, in the order they were added.
 * No visitor input here, so a plain query is enough.
 */
function toatePunctele(): array
{
    $rezultat = mysqli_query(conectareBd(), 'SELECT ID, lat, `long`, description FROM points ORDER BY ID');
    return mysqli_fetch_all($rezultat, MYSQLI_ASSOC);
}

/**
 * Creates an account and returns its ID. $hash comes from password_hash().
 * A name that already exists throws mysqli_sql_exception with code 1062
 * (the UNIQUE key on users.user), which the register form turns into a message.
 */
function adaugaUtilizator(string $nume, string $hash): int
{
    $cerere = mysqli_prepare(conectareBd(), 'INSERT INTO users (user, password) VALUES (?, ?)');
    mysqli_stmt_bind_param($cerere, 'ss', $nume, $hash);
    mysqli_stmt_execute($cerere);
    $id = mysqli_insert_id(conectareBd());
    mysqli_stmt_close($cerere);
    return $id;
}

/**
 * One account by its name, or null. Used by the login.
 * The column uses a case-insensitive collation, so "Seba" finds "seba".
 * The name comes from a form, so it is sent separately from the SQL.
 */
function gasesteUtilizator(string $nume): ?array
{
    $cerere = mysqli_prepare(conectareBd(), 'SELECT ID, user, password FROM users WHERE user = ?');
    mysqli_stmt_bind_param($cerere, 's', $nume);     // 's' = one string parameter
    mysqli_stmt_execute($cerere);
    $rand = mysqli_fetch_assoc(mysqli_stmt_get_result($cerere));
    mysqli_stmt_close($cerere);
    return $rand ?: null;
}
