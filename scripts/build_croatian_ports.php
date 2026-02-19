<?php
/**
 * Fetch Croatian marinas from OpenStreetMap via Overpass API.
 * Filters: leisure=marina OR harbour=marina in Croatia.
 * Run: php scripts/build_croatian_ports.php
 */

$query = <<<'OVERPASS'
[out:json][timeout:90];
area["ISO3166-1"="HR"][admin_level=2]->.croatia;
(
  node["leisure"="marina"](area.croatia);
  way["leisure"="marina"](area.croatia);
  relation["leisure"="marina"](area.croatia);
  node["harbour"="marina"](area.croatia);
  way["harbour"="marina"](area.croatia);
);
out center tags;
OVERPASS;

$url = 'https://overpass-api.de/api/interpreter';
$ctx = stream_context_create([
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => 'data=' . urlencode($query),
        'timeout' => 120,
    ],
]);

echo "Fetching Croatian marinas from Overpass API...\n";
$raw = file_get_contents($url, false, $ctx);

if (!$raw) {
    die("ERROR: Could not fetch data from Overpass API.\n");
}

$data = json_decode($raw, true);
if (!isset($data['elements'])) {
    die("ERROR: Invalid response from Overpass API.\n");
}

// Croatian county (županija) to region mapping
$countyToRegion = [
    'Dubrovačko-neretvanska županija' => 'Dalmazia Meridionale',
    'Splitsko-dalmatinska županija'   => 'Dalmazia Centrale',
    'Šibensko-kninska županija'       => 'Dalmazia Settentrionale',
    'Zadarska županija'               => 'Dalmazia Settentrionale',
    'Ličko-senjska županija'          => 'Quarnero',
    'Primorsko-goranska županija'     => 'Quarnero',
    'Istarska županija'               => 'Istria',
    'Dubrovnik-Neretva County'        => 'Dalmazia Meridionale',
    'Split-Dalmatia County'           => 'Dalmazia Centrale',
    'Šibenik-Knin County'             => 'Dalmazia Settentrionale',
    'Zadar County'                    => 'Dalmazia Settentrionale',
    'Lika-Senj County'                => 'Quarnero',
    'Primorje-Gorski Kotar County'    => 'Quarnero',
    'Istria County'                   => 'Istria',
];

function guessRegionHR(float $lat, float $lon): string
{
    // Istria (northwest peninsula)
    if ($lat > 44.8 && $lon < 14.2) return 'Istria';
    // Quarnero (Rijeka, Krk, Cres, Losinj)
    if ($lat > 44.3 && $lat <= 45.5 && $lon >= 14.2 && $lon < 15.0) return 'Quarnero';
    if ($lat > 44.3 && $lat <= 45.0 && $lon >= 14.0 && $lon < 14.8) return 'Quarnero';
    // Dalmazia Settentrionale (Zadar, Sibenik, Kornati)
    if ($lat > 43.5 && $lat <= 44.3) return 'Dalmazia Settentrionale';
    // Dalmazia Centrale (Split, Hvar, Brac, Vis)
    if ($lat > 43.0 && $lat <= 43.5) return 'Dalmazia Centrale';
    // Dalmazia Meridionale (Dubrovnik, Korcula, Mljet)
    if ($lat <= 43.0) return 'Dalmazia Meridionale';
    return 'Croazia';
}

$ports = [];
$seen = [];

foreach ($data['elements'] as $el) {
    $tags = $el['tags'] ?? [];

    $lat = $el['lat'] ?? ($el['center']['lat'] ?? null);
    $lon = $el['lon'] ?? ($el['center']['lon'] ?? null);

    if (!$lat || !$lon) continue;

    // Prefer Italian, then English, then Croatian name
    $name = $tags['name:it'] ?? $tags['name:en'] ?? $tags['name'] ?? null;
    if (!$name) continue;

    // City: prefer English, then Croatian
    $city = $tags['addr:city'] ?? $tags['addr:town'] ?? $tags['addr:municipality'] ?? null;
    if (!$city) {
        $city = $tags['name:en'] ?? $tags['name'] ?? $name;
    }

    // Region from county tag
    $county = $tags['addr:county'] ?? $tags['addr:state'] ?? '';
    $region = $countyToRegion[$county] ?? guessRegionHR((float)$lat, (float)$lon);

    // Deduplicate
    $key = round($lat, 3) . ',' . round($lon, 3);
    if (isset($seen[$key])) continue;
    $seen[$key] = true;

    // Filter out non-Latin names (Croatian uses Latin alphabet — no filter needed,
    // but skip if name has Cyrillic or Greek)
    if (preg_match('/[\x{0370}-\x{03FF}\x{0400}-\x{04FF}]/u', $name . $city)) continue;

    $name = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
    $city = mb_convert_case($city, MB_CASE_TITLE, 'UTF-8');

    $ports[] = [
        'name'      => $name,
        'city'      => $city,
        'province'  => $county,
        'region'    => $region,
        'latitude'  => round((float)$lat, 7),
        'longitude' => round((float)$lon, 7),
    ];
}

// Sort by region, then city
usort($ports, function ($a, $b) {
    $r = strcmp($a['region'], $b['region']);
    return $r !== 0 ? $r : strcmp($a['city'], $b['city']);
});

echo "Croatian marinas found: " . count($ports) . "\n";

$byRegion = [];
foreach ($ports as $p) {
    $byRegion[$p['region']] = ($byRegion[$p['region']] ?? 0) + 1;
}
ksort($byRegion);
foreach ($byRegion as $r => $c) {
    echo "  $r: $c\n";
}

$outPath = __DIR__ . '/../storage/ports_croatia.json';
file_put_contents($outPath, json_encode($ports, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\nSaved to storage/ports_croatia.json\n";

echo "\nFirst 10:\n";
foreach (array_slice($ports, 0, 10) as $p) {
    echo "  {$p['name']} ({$p['city']}, {$p['region']}) [{$p['latitude']}, {$p['longitude']}]\n";
}
