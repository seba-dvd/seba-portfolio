<?php
/**
 * Checks for the travel progress on the map page (www/includes/progres.php):
 * distances, achievements, XP and levels. No database or web server needed.
 * Run from the project folder:  php teste/verificare-progres.php
 * (verificare.sh runs it too).
 */
require __DIR__ . '/../www/includes/progres.php';

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

/** Places as the database returns them (strings), from [ID, lat, long] lists. */
function locuri(array ...$date): array
{
    return array_map(fn(array $d) => ['ID' => $d[0], 'lat' => (string) $d[1], 'long' => (string) $d[2]], $date);
}

/** Keys of the unlocked achievements. */
function deblocate(array $progres): array
{
    return array_values(array_column(array_filter($progres['realizari'], fn($r) => $r['deblocat']), 'cheie'));
}

echo "13. Travel progress (includes/progres.php)\n";

verifica('Brașov – Codlea is about 12.6 km', 12.6, round(distantaKm(45.6425, 25.5886, 45.6967, 25.4469), 1));
verifica('Brașov – București is about 141 km (straight line)', 141.0, round(distantaKm(45.6427, 25.5887, 44.4268, 26.1025)));
verifica('a place to itself is 0 km', 0.0, distantaKm(45.0, 25.0, 45.0, 25.0));

$gol = progresCalatorii([]);
verifica('no places: level 1, 0 XP', [1, 0], [$gol['nivel'], $gol['xp']]);
verifica('no places: nothing unlocked', [], deblocate($gol));

$brasov = [1, 45.6425, 25.5886];
$codlea = [2, 45.6967, 25.4469];
$doua = progresCalatorii(locuri($brasov, $codlea));
verifica('Brașov + Codlea: only "first step"', ['primul_pas'], deblocate($doua));
verifica('Brașov + Codlea: 2×100 + 1×50 = 250 XP, level 2, 0 XP into it', [250, 2, 0], [$doua['xp'], $doua['nivel'], $doua['xp_in_nivel']]);
verifica('home (smallest ID) is 0 km from home', 0.0, $doua['distante'][1]);

// Home is the smallest ID, even when it is not the first row
$invers = progresCalatorii(locuri($codlea, $brasov));
verifica('home = smallest ID, whatever the order', 0.0, $invers['distante'][1]);

// Five places within the town: "explorer", but no long road
$cinci = progresCalatorii(locuri([1, 45.64, 25.58], [2, 45.65, 25.59], [3, 45.66, 25.60], [4, 45.67, 25.61], [5, 45.68, 25.62]));
verifica('5 places nearby: first step + explorer', ['primul_pas', 'explorator'], deblocate($cinci));

$zece = progresCalatorii(locuri(...array_map(fn($i) => [$i, 45.64 + $i / 1000, 25.58], range(1, 10))));
verifica('10 places: traveller too', ['primul_pas', 'explorator', 'calator'], deblocate($zece));

$departe = progresCalatorii(locuri($brasov, [2, 44.4268, 26.1025]));                         // București
verifica('a place 141 km away: long road, not far from home', ['primul_pas', 'drum_lung'], deblocate($departe));
// București is south-east of Brașov, Paris north-west: all four directions are covered
$departe = progresCalatorii(locuri($brasov, [2, 44.4268, 26.1025], [3, 48.8566, 2.3522]));   // + Paris
verifica('… plus Paris: far from home, and the compass rose (S+E, N+W)',
    ['primul_pas', 'drum_lung', 'departe', 'roza_vanturilor'], deblocate($departe));
verifica('the farthest place is Paris, about 1700 km', true, $departe['max_km'] > 1600 && $departe['max_km'] < 1800);

$roza = progresCalatorii(locuri($brasov, [2, 46.5, 25.6], [3, 44.5, 25.6], [4, 45.6, 26.5], [5, 45.6, 24.5]));
verifica('places N, S, E and W of home: compass rose', true, in_array('roza_vanturilor', deblocate($roza), true));
$trei = progresCalatorii(locuri($brasov, [2, 46.5, 25.6], [3, 44.5, 25.6], [4, 45.6, 26.5]));
verifica('only three directions: no compass rose', false, in_array('roza_vanturilor', deblocate($trei), true));

verifica('XP inside the level never reaches the level size', true,
    $departe['xp_in_nivel'] >= 0 && $departe['xp_in_nivel'] < XP_PE_NIVEL);
verifica('level = XP / 250 + 1', intdiv($departe['xp'], 250) + 1, $departe['nivel']);

echo "\nProgress passed: $trecute   failed: $picate\n";
exit($picate === 0 ? 0 : 1);
