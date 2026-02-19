<?php
/**
 * Parse French GeoJSON ports dataset and filter Mediterranean ports with "Plaisance" activity.
 * Mediterranean = longitude > 2.5 (roughly east of Perpignan) OR Corse (commune code starts with 2A/2B)
 * Run: php scripts/build_french_ports.php
 */

$data = json_decode(file_get_contents(__DIR__ . '/../storage/Port_Maritime_FRA.geojson'), true);

// French department to region mapping (Mediterranean departments only)
$deptToRegion = [
    '06' => 'Provence-Alpes-Côte d\'Azur',   // Alpes-Maritimes
    '13' => 'Provence-Alpes-Côte d\'Azur',   // Bouches-du-Rhône
    '83' => 'Provence-Alpes-Côte d\'Azur',   // Var
    '84' => 'Provence-Alpes-Côte d\'Azur',   // Vaucluse
    '04' => 'Provence-Alpes-Côte d\'Azur',   // Alpes-de-Haute-Provence
    '05' => 'Provence-Alpes-Côte d\'Azur',   // Hautes-Alpes
    '2A' => 'Corse',                          // Corse-du-Sud
    '2B' => 'Corse',                          // Haute-Corse
    '11' => 'Occitanie',                      // Aude
    '30' => 'Occitanie',                      // Gard
    '34' => 'Occitanie',                      // Hérault
    '66' => 'Occitanie',                      // Pyrénées-Orientales
    '48' => 'Occitanie',                      // Lozère
];

$ports = [];

foreach ($data['features'] as $feature) {
    $props = $feature['properties'];
    $coords = $feature['geometry']['coordinates'];

    $lon = (float)($props['CoordXPort'] ?? $coords[0]);
    $lat = (float)($props['CoordYPort'] ?? $coords[1]);
    $commune = $props['LbCommune'] ?? '';
    $communeCode = $props['CdCommune'] ?? '';
    $name = $props['NomPort'] ?? '';

    // Check if it has Plaisance activity
    $isPlaisance = false;
    for ($i = 1; $i <= 6; $i++) {
        if (($props["MnActivitePortuaire_$i"] ?? '') === 'Plaisance') {
            $isPlaisance = true;
            break;
        }
    }

    if (!$isPlaisance) continue;

    // Get department code (first 2 or 3 chars of commune code)
    $dept = '';
    if (preg_match('/^(2[AB]|\d{2})/', $communeCode, $m)) {
        $dept = $m[1];
    }

    // Check if Mediterranean
    $region = $deptToRegion[$dept] ?? null;
    if (!$region) {
        // Fallback: check longitude (east of ~3° is roughly Mediterranean France)
        if ($lon > 3.0 || ($lon > 2.5 && $lat < 43.5)) {
            // Try to guess region from coordinates
            if ($lat > 41.0 && $lat < 43.0 && $lon > 8.5) {
                $region = 'Corse';
            } elseif ($lon > 5.5) {
                $region = 'Provence-Alpes-Côte d\'Azur';
            } elseif ($lon > 3.0) {
                $region = 'Occitanie';
            }
        }
    }

    if (!$region) continue;

    $ports[] = [
        'name' => $name,
        'city' => $commune,
        'province' => $dept,
        'region' => $region,
        'latitude' => round($lat, 7),
        'longitude' => round($lon, 7),
    ];
}

// Sort by region, then name
usort($ports, function ($a, $b) {
    $r = strcmp($a['region'], $b['region']);
    if ($r !== 0) return $r;
    return strcmp($a['name'], $b['name']);
});

echo "Mediterranean Plaisance ports: " . count($ports) . PHP_EOL;

// Count by region
$byRegion = [];
foreach ($ports as $p) {
    $byRegion[$p['region']] = ($byRegion[$p['region']] ?? 0) + 1;
}
foreach ($byRegion as $r => $c) {
    echo "  $r: $c" . PHP_EOL;
}

// Save
file_put_contents(__DIR__ . '/../storage/ports_france_med.json', json_encode($ports, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\nSaved to storage/ports_france_med.json\n";

// Show first 5
echo "\nFirst 5:\n";
foreach (array_slice($ports, 0, 5) as $p) {
    echo "  {$p['name']} ({$p['city']}, {$p['region']}) [{$p['latitude']}, {$p['longitude']}]\n";
}
