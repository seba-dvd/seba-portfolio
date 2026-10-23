<?php
/**
 * The protected page: only for logged-in users (includes/auth.php).
 * A map with one marker per row of the points table, and the same places as a list.
 *
 * The map provider is set in includes/config.php:
 *   'demo'   → Leaflet + OpenStreetMap, no key needed (default)
 *   'google' → Google Maps, needs a key with billing; without a key we fall back to demo
 */
require_once __DIR__ . '/includes/functii.php';
require_once __DIR__ . '/includes/limba.php';
require_once __DIR__ . '/includes/auth.php';       // sets $utilizator or redirects to login
require_once __DIR__ . '/includes/db.php';

// Leaflet from unpkg, pinned version with SRI hashes. Used in demo mode, and in
// Google mode as the fallback if Google refuses the key (js/harta.js loads it then).
const LEAFLET = [
    'css'      => 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
    'css_hash' => 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=',
    'js'       => 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
    'js_hash'  => 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=',
];

$puncte = toatePunctele();

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
            <a class="btn" href="logout.php?token=<?= e(tokenCsrf()) ?>"><?= e($t['iesire']) ?></a>
        </section>

        <section class="card harta-card">
            <div id="harta" class="harta-mare" role="region" aria-label="<?= e($t['harta_eticheta']) ?>"></div>
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
