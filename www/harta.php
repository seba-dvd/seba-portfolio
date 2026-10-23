<?php
/**
 * The protected page: only for logged-in users (includes/auth.php).
 * A map with one marker per row of the points table, the travel progress (level, XP,
 * achievements: includes/progres.php), an explore bar to jump from place to place,
 * and the same places as a list.
 *
 * The map provider is set in includes/config.php:
 *   'demo'   → Leaflet + OpenStreetMap, no key needed (default)
 *   'google' → Google Maps, needs a key with billing; without a key we fall back to demo
 */
require_once __DIR__ . '/includes/functii.php';
require_once __DIR__ . '/includes/limba.php';
require_once __DIR__ . '/includes/auth.php';       // sets $utilizator or redirects to login
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/leaflet.php';    // LEAFLET: demo mode, and the fallback if Google refuses the key
require_once __DIR__ . '/includes/progres.php';

$puncte = toatePunctele();
$progres = progresCalatorii($puncte);

$config = configuratie();
$cheieGoogle = trim((string) ($config['google_maps_cheie'] ?? ''));
$furnizor = (($config['harta_furnizor'] ?? 'demo') === 'google' && $cheieGoogle !== '') ? 'google' : 'demo';

// What js/harta.js needs, as JSON. Coordinates become numbers; the descriptions stay
// text and are shown with textContent, never as HTML.
$dateHarta = [
    'furnizor' => $furnizor,
    'leaflet'  => LEAFLET,
    'puncte'   => array_map(fn(array $p) => [
        'lat'       => (float) $p['lat'],
        'lng'       => (float) $p['long'],
        'descriere' => $p['description'],
        'distanta_km' => $progres['distante'][(int) $p['ID']],
    ], $puncte),
];

$pagina = 'harta';
if ($furnizor === 'demo') {
    $stiluri = [['href' => LEAFLET['css'], 'integrity' => LEAFLET['css_hash']]];
    $scripturi = [
        ['src' => LEAFLET['js'], 'integrity' => LEAFLET['js_hash']],
        'js/harta.js',
    ];
} else {
    // Google's loader changes over time, so it can't have an SRI hash.
    // js/harta.js comes first: it defines the callback the loader calls.
    $scripturi = [
        'js/harta.js',
        ['src' => 'https://maps.googleapis.com/maps/api/js?key=' . urlencode($cheieGoogle)
                  . '&loading=async&callback=pornesteHartaGoogle&language=' . LIMBA,
         'async' => true],
    ];
}
require __DIR__ . '/includes/antet.php';
?>

    <main>
        <section class="card harta-antet">
            <div>
                <h1><?= e(sprintf($t['bun_venit'], $utilizator)) ?></h1>
                <p><?= e($t['harta_intro']) ?></p>
            </div>
            <div class="butoane">
                <?php if (esteAdmin($utilizator)): ?>
                    <a class="btn btn-cyber" href="admin.php"><?= e($t['admin_link']) ?></a>
                <?php endif; ?>
                <a class="btn" href="logout.php?token=<?= e(tokenCsrf()) ?>"><?= e($t['iesire']) ?></a>
            </div>
        </section>

        <section class="card hud" aria-labelledby="titlu-progres">
            <div class="hud-sus">
                <div class="nivel-insigna" aria-hidden="true"><?= e((string) $progres['nivel']) ?></div>
                <div class="hud-xp">
                    <h2 id="titlu-progres"><?= e(sprintf($t['progres_nivel'], $progres['nivel'])) ?></h2>
                    <progress class="bara-xp" max="<?= e((string) $progres['xp_pe_nivel']) ?>" value="<?= e((string) $progres['xp_in_nivel']) ?>"
                              aria-label="<?= e($t['progres_xp_eticheta']) ?>"></progress>
                    <p class="text-slab"><?= e(sprintf($t['progres_xp'], $progres['xp_in_nivel'], $progres['xp_pe_nivel'], $progres['nivel'] + 1)) ?></p>
                </div>
                <ul class="hud-contoare">
                    <li><strong><?= e((string) $progres['numar']) ?></strong> <?= e($t['progres_locuri']) ?></li>
                    <li><strong><?= e((string) $progres['max_km']) ?> km</strong> <?= e($t['progres_km']) ?></li>
                    <li><strong><?= e($progres['deblocate'] . ' / ' . count($progres['realizari'])) ?></strong> <?= e($t['progres_realizari']) ?></li>
                    <li><strong><?= e((string) $progres['xp']) ?> XP</strong> <?= e($t['progres_xp_total']) ?></li>
                </ul>
            </div>

            <h3><?= e($t['realizari_titlu']) ?></h3>
            <ul class="grid-realizari">
                <?php foreach ($progres['realizari'] as $realizare): ?>
                    <li class="realizare<?= $realizare['deblocat'] ? '' : ' blocata' ?>">
                        <span class="realizare-icon" aria-hidden="true"><?= e($realizare['icon']) ?></span>
                        <span>
                            <strong><?= e($t['realizare_' . $realizare['cheie']]) ?></strong>
                            <small><?= e($t['realizare_' . $realizare['cheie'] . '_text']) ?></small>
                            <small class="stare"><?= e($realizare['deblocat'] ? $t['realizare_deblocata'] : $t['realizare_blocata']) ?></small>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section class="card harta-card">
            <div id="harta" class="harta-mare" role="region" aria-label="<?= e($t['harta_eticheta']) ?>"></div>
            <?php if ($puncte): ?>
                <!-- Shown by js/harta.js: without JavaScript there is nothing to move -->
                <div class="explorare" id="explorare" tabindex="0" hidden
                     aria-label="<?= e($t['explorare_eticheta']) ?>"
                     data-text-pozitie="<?= e($t['explorare_pozitie']) ?>"
                     data-text-distanta="<?= e($t['explorare_distanta']) ?>"
                     data-text-acasa="<?= e($t['explorare_acasa']) ?>"
                     data-text-start="<?= e($t['explorare_start']) ?>">
                    <button type="button" class="btn btn-mic" id="explorare-inapoi" aria-label="<?= e($t['explorare_inapoi']) ?>">◀</button>
                    <div class="explorare-info" aria-live="polite">
                        <small id="explorare-pozitie"></small>
                        <strong id="explorare-nume"></strong>
                        <small id="explorare-distanta"></small>
                    </div>
                    <button type="button" class="btn btn-mic" id="explorare-inainte" aria-label="<?= e($t['explorare_inainte']) ?>">▶</button>
                </div>
            <?php endif; ?>
            <?php if ($furnizor === 'demo'): ?>
                <p class="harta-nota"><?= e($t['harta_demo']) ?></p>
            <?php endif; ?>
            <script id="date-harta" type="application/json"><?= json_encode($dateHarta, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) ?></script>
        </section>

        <section class="card">
            <h2><?= e($t['harta_locuri']) ?></h2>
            <?php if ($puncte): ?>
                <ul class="lista-locuri">
                    <?php foreach ($puncte as $punct): ?>
                        <li>
                            <span class="punct vizitat"></span>
                            <span><?= e($punct['description']) ?></span>
                            <small><?= e($punct['lat']) ?>, <?= e($punct['long']) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><?= e($t['harta_gol']) ?></p>
            <?php endif; ?>
        </section>
    </main>

<?php require __DIR__ . '/includes/subsol.php'; ?>
