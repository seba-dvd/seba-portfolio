<?php
/**
 * Travel progress for the map page: XP, level and achievements, computed from
 * the places in the points table. Plain PHP (no database access here), so the
 * page shows the progress even without JavaScript and the rules are easy to test
 * (teste/verificare-progres.php).
 *
 * "Home" is the place with the smallest ID: the first one added. It can be
 * changed from the admin panel like any other place.
 */

const XP_PE_LOC = 100;
const XP_PE_REALIZARE = 50;
const XP_PE_NIVEL = 250;

/**
 * The achievements: key (texts in the dictionaries: realizare_<key>, realizare_<key>_text),
 * icon, and the rule that unlocks it. $d has: numar (places), maxKm (farthest place
 * from home), directii (N/S/E/W of home that have a place).
 */
function listaRealizari(): array
{
    return [
        'primul_pas'      => ['👣', fn(array $d) => $d['numar'] >= 1],
        'explorator'      => ['🧭', fn(array $d) => $d['numar'] >= 5],
        'calator'         => ['🎒', fn(array $d) => $d['numar'] >= 10],
        'drum_lung'       => ['🚗', fn(array $d) => $d['maxKm'] >= 100],
        'departe'         => ['✈️', fn(array $d) => $d['maxKm'] >= 500],
        'roza_vanturilor' => ['🌐', fn(array $d) => count($d['directii']) === 4],
    ];
}

/**
 * Distance in km between two points on Earth (haversine formula, radius 6371 km).
 */
function distantaKm(float $lat1, float $lng1, float $lat2, float $lng2): float
{
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
    return 6371 * 2 * atan2(sqrt($a), sqrt(1 - $a));
}

/**
 * @param array $puncte rows with ID, lat, long (as from toatePunctele())
 * @return array xp, nivel, xp_in_nivel, xp_pe_nivel, realizari [[cheie, icon, deblocat]],
 *               deblocate, distante [ID => km from home], max_km (farthest place), numar
 */
function progresCalatorii(array $puncte): array
{
    $distante = [];
    $directii = [];
    $maxKm = 0.0;

    if ($puncte) {
        $acasa = $puncte[array_search(min(array_column($puncte, 'ID')), array_column($puncte, 'ID'))];
        foreach ($puncte as $punct) {
            $km = distantaKm((float) $acasa['lat'], (float) $acasa['long'], (float) $punct['lat'], (float) $punct['long']);
            $distante[(int) $punct['ID']] = round($km, 1);
            $maxKm = max($maxKm, $km);

            // Which side of home: at least 0.1° (about 10 km) away, so the home town itself doesn't count
            $dLat = (float) $punct['lat'] - (float) $acasa['lat'];
            $dLng = (float) $punct['long'] - (float) $acasa['long'];
            if ($dLat >= 0.1)  { $directii['N'] = true; }
            if ($dLat <= -0.1) { $directii['S'] = true; }
            if ($dLng >= 0.1)  { $directii['E'] = true; }
            if ($dLng <= -0.1) { $directii['V'] = true; }
        }
    }

    $date = ['numar' => count($puncte), 'maxKm' => $maxKm, 'directii' => $directii];
    $realizari = [];
    foreach (listaRealizari() as $cheie => [$icon, $regula]) {
        $realizari[] = ['cheie' => $cheie, 'icon' => $icon, 'deblocat' => $regula($date)];
    }
    $deblocate = count(array_filter(array_column($realizari, 'deblocat')));

    $xp = XP_PE_LOC * count($puncte) + XP_PE_REALIZARE * $deblocate;
    return [
        'numar'       => count($puncte),
        'xp'          => $xp,
        'nivel'       => intdiv($xp, XP_PE_NIVEL) + 1,
        'xp_in_nivel' => $xp % XP_PE_NIVEL,
        'xp_pe_nivel' => XP_PE_NIVEL,
        'realizari'   => $realizari,
        'deblocate'   => $deblocate,
        'distante'    => $distante,
        'max_km'      => (int) round($maxKm),
    ];
}
