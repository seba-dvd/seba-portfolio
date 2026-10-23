<?php
/**
 * Login and register, two forms on one page. Both post back to this file;
 * the hidden field "actiune" says which form was sent.
 *
 * After a successful POST the browser is redirected (303) to the map, so a
 * refresh there doesn't send the form again (Post/Redirect/Get).
 */
require_once __DIR__ . '/includes/functii.php';
require_once __DIR__ . '/includes/limba.php';
require_once __DIR__ . '/includes/sesiune.php';
require_once __DIR__ . '/includes/db.php';

// A random bcrypt hash: for an unknown name we still run password_verify on it,
// so the answer takes as long as for a real name and timing doesn't reveal
// which names exist.
const HASH_FALS = '$2y$10$WDtRCIQ3jt498bSs56F5c.0tM0Q6ySUPRGBrSx.yftGFIeUjnADV.';

if (utilizatorConectat() !== null) {
    header('Location: harta.php', true, 303);
    exit;
}

$eroareLogin = null;
$eroriCont = [];
$numeLogin = '';
$numeCont = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actiune = $_POST['actiune'] ?? '';

    if (!verificaCsrf($_POST['csrf'] ?? null)) {
        // Expired session or a form sent from another site
        if ($actiune === 'inregistrare') {
            $eroriCont[] = $t['eroare_csrf'];
        } else {
            $eroareLogin = $t['eroare_csrf'];
        }
    } elseif ($actiune === 'login') {
        $numeLogin = trim((string) ($_POST['user'] ?? ''));
        $parola = (string) ($_POST['password'] ?? '');

        $cont = $numeLogin === '' ? null : gasesteUtilizator($numeLogin);
        $parolaCorecta = password_verify($parola, $cont['password'] ?? HASH_FALS);

        if ($cont !== null && $parolaCorecta) {
            conecteaza((int) $cont['ID'], $cont['user']);
            header('Location: harta.php', true, 303);
            exit;
        }
        // One message for both cases: it doesn't tell which part was wrong
        $eroareLogin = $t['eroare_login'];
    } elseif ($actiune === 'inregistrare') {
        $numeCont = trim((string) ($_POST['user'] ?? ''));
        $parola = (string) ($_POST['password'] ?? '');
        $confirmare = (string) ($_POST['password2'] ?? '');

        // Letters, digits, . _ - only: the name appears on pages and in the autocomplete list
        if (!preg_match('/^[A-Za-z0-9._-]{3,30}$/', $numeCont)) {
            $eroriCont[] = $t['eroare_utilizator'];
        }
        if (strlen($parola) < 8) {
            $eroriCont[] = $t['eroare_parola_scurta'];
        } elseif (strlen($parola) > 72) {
            // bcrypt ignores everything after 72 bytes: refuse instead of cutting silently
            $eroriCont[] = $t['eroare_parola_lunga'];
        }
        if ($parola !== $confirmare) {
            $eroriCont[] = $t['eroare_parole_diferite'];
        }

        if (!$eroriCont) {
            try {
                $id = adaugaUtilizator($numeCont, password_hash($parola, PASSWORD_DEFAULT));
                conecteaza($id, $numeCont);
                header('Location: harta.php', true, 303);
                exit;
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() !== 1062) {     // 1062 = duplicate key: the name is taken
                    throw $e;
                }
                $eroriCont[] = $t['eroare_nume_ocupat'];
            }
        }
    }
}

$pagina = 'harta';
$cheieTitlu = 'login';
$temaCyber = true;
$scripturi = [
    // jQuery + jQuery UI from the official CDN, pinned versions with SRI hashes
    ['src' => 'https://code.jquery.com/jquery-3.7.1.min.js',
     'integrity' => 'sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo='],
    ['src' => 'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js',
     'integrity' => 'sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0='],
    'js/autocomplete.js',
];
require __DIR__ . '/includes/antet.php';
$token = tokenCsrf();
?>

    <main>
        <section class="terminal">
            <div class="terminal-bara">
                <span class="buton rosu"></span>
                <span class="buton galben"></span>
                <span class="buton verde"></span>
                <span class="terminal-titlu">seba@pai: ~/harta</span>
            </div>
            <div class="terminal-corp">
                <p><span class="prompt">$</span> <?= e($t['login_comanda']) ?></p>
                <h1><?= e($t['login_titlu_pagina']) ?></h1>
                <p><?= e($t['login_intro']) ?></p>
            </div>
        </section>

        <?php if (isset($_GET['iesire'])): ?>
            <p class="mesaj succes" role="status"><?= e($t['mesaj_iesire']) ?></p>
        <?php endif; ?>

        <div class="grid-conturi">
            <form class="card formular" method="post" action="login.php">
                <h2><?= e($t['login_titlu']) ?></h2>
                <input type="hidden" name="actiune" value="login">
                <input type="hidden" name="csrf" value="<?= e($token) ?>">

                <?php if ($eroareLogin): ?>
                    <p class="mesaj eroare" role="alert"><?= e($eroareLogin) ?></p>
                <?php endif; ?>

                <label for="login-user"><?= e($t['camp_utilizator']) ?></label>
                <input id="login-user" name="user" type="text" value="<?= e($numeLogin) ?>"
                       autocomplete="username" autocapitalize="none" spellcheck="false" required>

                <label for="login-parola"><?= e($t['camp_parola']) ?></label>
                <input id="login-parola" name="password" type="password" autocomplete="current-password" required>

                <button class="btn" type="submit"><?= e($t['buton_login']) ?></button>
            </form>

            <form class="card formular" method="post" action="login.php">
                <h2><?= e($t['inregistrare_titlu']) ?></h2>
                <input type="hidden" name="actiune" value="inregistrare">
                <input type="hidden" name="csrf" value="<?= e($token) ?>">

                <?php if ($eroriCont): ?>
                    <ul class="mesaj eroare" role="alert">
                        <?php foreach ($eroriCont as $eroare): ?>
                            <li><?= e($eroare) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <label for="cont-user"><?= e($t['camp_utilizator']) ?></label>
                <input id="cont-user" name="user" type="text" value="<?= e($numeCont) ?>"
                       autocomplete="username" autocapitalize="none" spellcheck="false"
                       minlength="3" maxlength="30" pattern="[A-Za-z0-9._\-]{3,30}" aria-describedby="regula-user" required>
                <small id="regula-user"><?= e($t['regula_utilizator']) ?></small>

                <label for="cont-parola"><?= e($t['camp_parola']) ?></label>
                <input id="cont-parola" name="password" type="password" autocomplete="new-password"
                       minlength="8" maxlength="72" aria-describedby="regula-parola" required>
                <small id="regula-parola"><?= e($t['regula_parola']) ?></small>

                <label for="cont-parola2"><?= e($t['camp_parola2']) ?></label>
                <input id="cont-parola2" name="password2" type="password" autocomplete="new-password" required>

                <button class="btn" type="submit"><?= e($t['buton_inregistrare']) ?></button>
            </form>
        </div>
    </main>

<?php require __DIR__ . '/includes/subsol.php'; ?>
