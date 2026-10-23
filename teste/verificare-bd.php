<?php
/**
 * Database checks: structure, constraints, rights of pai_app, prepared statements.
 *
 * Needs: MySQL running (XAMPP), sql/*.sql imported, www/includes/config.php.
 * Run from the project folder:  php teste/verificare-bd.php
 * (verificare.sh runs it too). Test rows are added inside a transaction and
 * rolled back, so the database is left as it was.
 */
require __DIR__ . '/../www/includes/db.php';

$trecute = 0;
$picate = 0;

function verifica(string $descriere, $asteptat, $primit): void
{
    global $trecute, $picate;
    if ($asteptat === $primit) {
        $trecute++;
        echo "  ok    $descriere\n";
    } else {
        $picate++;
        echo "  FAIL  $descriere (expected: " . var_export($asteptat, true) . ', got: ' . var_export($primit, true) . ")\n";
    }
}

/** Runs $sql and returns the MySQL error code, or 0 if it worked. */
function codEroare(mysqli $bd, string $sql): int
{
    try {
        mysqli_query($bd, $sql);
        return 0;
    } catch (mysqli_sql_exception $e) {
        return $e->getCode();
    }
}

$bd = conectareBd();

echo "11. Database\n";
verifica('connected as pai_app', 'pai_app@localhost',
    mysqli_fetch_row(mysqli_query($bd, 'SELECT CURRENT_USER()'))[0]);
verifica('connection charset is utf8mb4', 'utf8mb4', mysqli_character_set_name($bd));

$coloane = fn(string $tabel) => array_column(
    mysqli_fetch_all(mysqli_query($bd, "SHOW COLUMNS FROM $tabel"), MYSQLI_ASSOC), 'Field');
verifica('users has ID, user, password', ['ID', 'user', 'password'], $coloane('users'));
verifica('points has ID, lat, long, description', ['ID', 'lat', 'long', 'description'], $coloane('points'));

$puncte = toatePunctele();
verifica('points has at least one place', true, count($puncte) >= 1);
verifica('diacritics survive the round trip', true,
    in_array('Brașov – acasă, Universitatea Transilvania', array_column($puncte, 'description'), true));

// Prepared statement: the input is compared as text, it can't change the query
verifica("login name x' OR '1'='1 finds nobody", null, gasesteUtilizator("x' OR '1'='1"));
verifica('unknown name finds nobody', null, gasesteUtilizator('nu-exista-' . bin2hex(random_bytes(4))));

// Constraints, inside a transaction that is rolled back
mysqli_begin_transaction($bd);
$nume = 'test_' . bin2hex(random_bytes(4));
verifica('a new user can be inserted', 0, codEroare($bd, "INSERT INTO users (user, password) VALUES ('$nume', 'x')"));
verifica('the same name twice is refused (1062)', 1062, codEroare($bd, "INSERT INTO users (user, password) VALUES ('$nume', 'x')"));
verifica('latitude 100 is refused (CHECK, 4025)', 4025,
    codEroare($bd, "INSERT INTO points (lat, `long`, description) VALUES (100, 0, 'test')"));
verifica('longitude -200 is refused (CHECK, 4025)', 4025,
    codEroare($bd, "INSERT INTO points (lat, `long`, description) VALUES (0, -200, 'test')"));
mysqli_rollback($bd);

// Least privilege: pai_app can't delete, change or drop anything (1142 = command denied)
verifica('pai_app cannot DELETE', 1142, codEroare($bd, 'DELETE FROM points WHERE ID = 0'));
verifica('pai_app cannot UPDATE', 1142, codEroare($bd, 'UPDATE users SET user = user WHERE ID = 0'));
verifica('pai_app cannot DROP', 1142, codEroare($bd, 'DROP TABLE points'));
verifica('pai_app cannot read the mysql database', 1142, codEroare($bd, 'SELECT user FROM mysql.user'));

echo "\nDatabase passed: $trecute   failed: $picate\n";
exit($picate === 0 ? 0 : 1);
