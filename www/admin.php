<?php
/**
 * Admin panel: the places on the map and the user accounts.
 *
 * - Only logged-in users listed in config.php ('admini'); everyone else gets 403.
 * - Changes go through the pai_admin database account (includes/db-admin.php);
 *   the public pages keep using pai_app, which can't change or delete anything.
 * - Every form carries the CSRF token. After a change the browser is redirected
 *   (Post/Redirect/Get) and a short message waits in the session.
 * - Admin accounts can't be deleted from the panel (no locking yourself out).
 */
require_once __DIR__ . '/includes/functii.php';
require_once __DIR__ . '/includes/limba.php';
require_once __DIR__ . '/includes/auth.php';       // sets $utilizator or redirects to login
require_once __DIR__ . '/includes/db-admin.php';
require_once __DIR__ . '/includes/leaflet.php';

if (!esteAdmin($utilizator)) {
    require __DIR__ . '/403.php';
    exit;
}

/** A coordinate typed in the form: a number within ±$limita (comma accepted), or null. */
function coordonata(mixed $valoare, float $limita): ?float
{
    if (!is_string($valoare)) {
        return null;
    }
    $numar = filter_var(str_replace(',', '.', trim($valoare)), FILTER_VALIDATE_FLOAT);
    return ($numar !== false && abs($numar) <= $limita) ? round($numar, 6) : null;
}

/** Remembers a message for the next page and reloads the panel (Post/Redirect/Get). */
function inapoiLaPanou(string $tip, string $cheie): never
{
    $_SESSION['mesaj_admin'] = [$tip, $cheie];
    header('Location: admin.php', true, 303);
    exit;
}

$eroriLoc = [];
$mesaj = $_SESSION['mesaj_admin'] ?? null;          // [type, dictionary key], shown once
unset($_SESSION['mesaj_admin']);

// The place in the form: empty for a new one, or admin.php?loc=ID to edit
$loc = ['ID' => null, 'lat' => '', 'long' => '', 'description' => ''];
$idEditat = filter_input(INPUT_GET, 'loc', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($idEditat) {
    $loc = gasesteLoc($idEditat) ?? $loc;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actiune = $_POST['actiune'] ?? '';
    $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null;

    if (!verificaCsrf($_POST['csrf'] ?? null)) {
        $mesaj = ['eroare', 'eroare_csrf'];

    } elseif ($actiune === 'salveaza_loc') {
        $lat = coordonata($_POST['lat'] ?? null, 90);
        $lng = coordonata($_POST['long'] ?? null, 180);
        $descriere = trim((string) ($_POST['description'] ?? ''));

        if ($lat === null) {
            $eroriLoc[] = $t['eroare_lat'];
        }
        if ($lng === null) {
            $eroriLoc[] = $t['eroare_long'];
        }
        if ($descriere === '' || mb_strlen($descriere) > 255) {
            $eroriLoc[] = $t['eroare_descriere'];
        }
        if ($id !== null && gasesteLoc($id) === null) {
            $eroriLoc[] = $t['eroare_loc_inexistent'];
        }

        if (!$eroriLoc) {
            if ($id === null) {
                adaugaLoc($lat, $lng, $descriere);
                inapoiLaPanou('succes', 'mesaj_loc_adaugat');
            }
            modificaLoc($id, $lat, $lng, $descriere);
            inapoiLaPanou('succes', 'mesaj_loc_modificat');
        }
        // Show the form again with what was typed
        $loc = ['ID' => $id, 'lat' => (string) ($_POST['lat'] ?? ''), 'long' => (string) ($_POST['long'] ?? ''),
                'description' => $descriere];

    } elseif ($actiune === 'sterge_loc') {
        $id !== null && stergeLoc($id)
            ? inapoiLaPanou('succes', 'mesaj_loc_sters')
            : inapoiLaPanou('eroare', 'eroare_loc_inexistent');

    } elseif ($actiune === 'sterge_utilizator') {
        $cont = $id !== null ? gasesteUtilizatorDupaId($id) : null;
        if ($cont === null) {
            inapoiLaPanou('eroare', 'eroare_utilizator_inexistent');
        }
        if (esteAdmin($cont['user'])) {
            inapoiLaPanou('eroare', 'eroare_sterge_admin');
        }
        stergeUtilizator($id);
        inapoiLaPanou('succes', 'mesaj_utilizator_sters');
    }
}

$puncte = toatePunctele();
$utilizatori = totiUtilizatorii();
$admini = array_values(array_filter(array_column($utilizatori, 'user'), 'esteAdmin'));
$token = tokenCsrf();

// For js/admin.js: the existing places (shown on the picker map)
$dateAdmin = array_map(fn(array $p) => [
    'lat' => (float) $p['lat'], 'lng' => (float) $p['long'], 'descriere' => $p['description'],
], $puncte);

$pagina = '';
$cheieTitlu = 'admin';
$temaCyber = true;
$stiluri = [['href' => LEAFLET['css'], 'integrity' => LEAFLET['css_hash']]];
$scripturi = [['src' => LEAFLET['js'], 'integrity' => LEAFLET['js_hash']], 'js/admin.js'];
require __DIR__ . '/includes/antet.php';
?>

    <main>
        <section class="terminal">
            <div class="terminal-bara">
                <span class="buton rosu"></span>
                <span class="buton galben"></span>
                <span class="buton verde"></span>
                <span class="terminal-titlu">root@pai: ~/admin</span>
            </div>
            <div class="terminal-corp">
                <p><span class="prompt">$</span> <?= e($t['admin_comanda']) ?></p>
                <h1><?= e($t['admin_titlu']) ?></h1>
                <p><?= e($t['admin_intro']) ?> <a class="link" href="harta.php"><?= e($t['admin_inapoi']) ?></a></p>
            </div>
        </section>

        <?php if ($mesaj): ?>
            <p class="mesaj <?= $mesaj[0] === 'succes' ? 'succes' : 'eroare' ?>" role="status"><?= e($t[$mesaj[1]]) ?></p>
        <?php endif; ?>

        <section class="grid-statistici">
            <div class="card statistica"><span class="numar"><?= count($puncte) ?></span> <?= e($t['admin_stat_locuri']) ?></div>
            <div class="card statistica"><span class="numar"><?= count($utilizatori) ?></span> <?= e($t['admin_stat_utilizatori']) ?></div>
            <div class="card statistica">
                <span class="numar"><?= count($admini) ?></span> <?= e($t['admin_stat_admini']) ?>
                <small><?= e(implode(', ', $admini)) ?></small>
            </div>
        </section>

        <section class="card" id="formular-loc">
            <h2><?= e($loc['ID'] ? sprintf($t['admin_loc_modifica'], $loc['ID']) : $t['admin_loc_nou']) ?></h2>

            <?php if ($eroriLoc): ?>
                <ul class="mesaj eroare" role="alert">
                    <?php foreach ($eroriLoc as $eroare): ?>
                        <li><?= e($eroare) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div class="admin-loc">
                <form class="formular" method="post" action="admin.php">
                    <input type="hidden" name="actiune" value="salveaza_loc">
                    <input type="hidden" name="csrf" value="<?= e($token) ?>">
                    <?php if ($loc['ID']): ?>
                        <input type="hidden" name="id" value="<?= e((string) $loc['ID']) ?>">
                    <?php endif; ?>

                    <label for="loc-lat"><?= e($t['camp_lat']) ?></label>
                    <input id="loc-lat" name="lat" type="text" inputmode="decimal" value="<?= e((string) $loc['lat']) ?>" required>

                    <label for="loc-long"><?= e($t['camp_long']) ?></label>
                    <input id="loc-long" name="long" type="text" inputmode="decimal" value="<?= e((string) $loc['long']) ?>" required>

                    <label for="loc-descriere"><?= e($t['camp_descriere']) ?></label>
                    <input id="loc-descriere" name="description" type="text" maxlength="255" value="<?= e($loc['description']) ?>" required>

                    <div class="butoane">
                        <button class="btn" type="submit"><?= e($t['buton_salveaza']) ?></button>
                        <?php if ($loc['ID']): ?>
                            <a class="btn btn-secundar" href="admin.php"><?= e($t['buton_anuleaza']) ?></a>
                        <?php endif; ?>
                    </div>
                </form>

                <div>
                    <div id="harta-admin" class="harta-mare harta-admin" role="region" aria-label="<?= e($t['admin_harta_ajutor']) ?>"></div>
                    <p class="harta-nota"><?= e($t['admin_harta_ajutor']) ?></p>
                </div>
            </div>
            <script id="date-admin" type="application/json"><?= json_encode($dateAdmin, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?></script>
        </section>

        <section class="card admin-sectiune">
            <h2><?= e($t['admin_locuri']) ?></h2>
            <?php if ($puncte): ?>
                <div class="tabel-container">
                    <table class="tabel-admin">
                        <thead>
                            <tr><th><?= e($t['col_id']) ?></th><th><?= e($t['camp_descriere']) ?></th><th><?= e($t['col_coordonate']) ?></th><th><?= e($t['col_actiuni']) ?></th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($puncte as $punct): ?>
                                <tr>
                                    <td><?= e((string) $punct['ID']) ?></td>
                                    <td><?= e($punct['description']) ?></td>
                                    <td class="numeric"><?= e($punct['lat']) ?>, <?= e($punct['long']) ?></td>
                                    <td><div class="actiuni">
                                        <a class="btn btn-mic" href="admin.php?loc=<?= e((string) $punct['ID']) ?>#formular-loc"><?= e($t['buton_modifica']) ?></a>
                                        <form method="post" action="admin.php" data-confirmare="<?= e(sprintf($t['confirma_sterge_loc'], $punct['description'])) ?>">
                                            <input type="hidden" name="actiune" value="sterge_loc">
                                            <input type="hidden" name="id" value="<?= e((string) $punct['ID']) ?>">
                                            <input type="hidden" name="csrf" value="<?= e($token) ?>">
                                            <button class="btn btn-mic btn-pericol" type="submit"><?= e($t['buton_sterge']) ?></button>
                                        </form>
                                    </div></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p><?= e($t['harta_gol']) ?></p>
            <?php endif; ?>
        </section>

        <section class="card">
            <h2><?= e($t['admin_utilizatori']) ?></h2>
            <div class="tabel-container">
                <table class="tabel-admin">
                    <thead>
                        <tr><th><?= e($t['col_id']) ?></th><th><?= e($t['camp_utilizator']) ?></th><th><?= e($t['col_rol']) ?></th><th><?= e($t['col_actiuni']) ?></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilizatori as $cont): ?>
                            <tr>
                                <td><?= e((string) $cont['ID']) ?></td>
                                <td><?= e($cont['user']) ?></td>
                                <td><?= e(esteAdmin($cont['user']) ? $t['rol_admin'] : $t['rol_utilizator']) ?></td>
                                <td><div class="actiuni">
                                    <?php if (esteAdmin($cont['user'])): ?>
                                        <span class="text-slab">—</span>
                                    <?php else: ?>
                                        <form method="post" action="admin.php" data-confirmare="<?= e(sprintf($t['confirma_sterge_utilizator'], $cont['user'])) ?>">
                                            <input type="hidden" name="actiune" value="sterge_utilizator">
                                            <input type="hidden" name="id" value="<?= e((string) $cont['ID']) ?>">
                                            <input type="hidden" name="csrf" value="<?= e($token) ?>">
                                            <button class="btn btn-mic btn-pericol" type="submit"><?= e($t['buton_sterge']) ?></button>
                                        </form>
                                    <?php endif; ?>
                                </div></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

<?php require __DIR__ . '/includes/subsol.php'; ?>
