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
 */
function conectareBd(): mysqli
{
    static $conexiune = null;
    if ($conexiune !== null) {
        return $conexiune;
    }

    $fisierConfig = __DIR__ . '/config.php';
    if (!is_file($fisierConfig)) {
        eroareBd('config.php is missing: copy includes/config.example.php to includes/config.php');
    }
    $config = require $fisierConfig;

    // Errors become exceptions, so a failed query can't go unnoticed
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conexiune = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
        mysqli_set_charset($conexiune, 'utf8mb4');
    } catch (mysqli_sql_exception $e) {
        eroareBd('connection failed: ' . $e->getMessage());
    }
    return $conexiune;
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
 * One account by its exact name, or null. Used by the login (M5).
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
