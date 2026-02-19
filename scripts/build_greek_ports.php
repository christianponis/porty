<?php
/**
 * Fetch Greek marinas from OpenStreetMap via Overpass API.
 * Filters: leisure=marina OR harbour=marina in Greece.
 * Run: php scripts/build_greek_ports.php
 */

$query = <<<'OVERPASS'
[out:json][timeout:90];
area["ISO3166-1"="GR"][admin_level=2]->.greece;
(
  node["leisure"="marina"](area.greece);
  way["leisure"="marina"](area.greece);
  relation["leisure"="marina"](area.greece);
  node["harbour"="marina"](area.greece);
  way["harbour"="marina"](area.greece);
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

echo "Fetching Greek marinas from Overpass API...\n";
$raw = file_get_contents($url, false, $ctx);

if (!$raw) {
    die("ERROR: Could not fetch data from Overpass API.\n");
}

$data = json_decode($raw, true);
if (!isset($data['elements'])) {
    die("ERROR: Invalid response from Overpass API.\n");
}

// Greek region mapping by administrative area
// We'll derive region from addr:state, addr:county, or coordinates
$regionByPerifereia = [
    'Αττική'                          => 'Attiki',
    'Attica'                          => 'Attiki',
    'Αττικη'                          => 'Attiki',
    'Πελοπόννησος'                    => 'Peloponneso',
    'Peloponnese'                     => 'Peloponneso',
    'Δυτική Ελλάδα'                   => 'Grecia Occidentale',
    'Western Greece'                  => 'Grecia Occidentale',
    'Ιόνιοι Νήσοι'                   => 'Isole Ionie',
    'Ionian Islands'                  => 'Isole Ionie',
    'Βόρειο Αιγαίο'                  => 'Egeo Settentrionale',
    'North Aegean'                    => 'Egeo Settentrionale',
    'Νότιο Αιγαίο'                   => 'Egeo Meridionale',
    'South Aegean'                    => 'Egeo Meridionale',
    'Κρήτη'                           => 'Creta',
    'Crete'                           => 'Creta',
    'Μακεδονία'                       => 'Macedonia',
    'Central Macedonia'               => 'Macedonia',
    'Κεντρική Μακεδονία'             => 'Macedonia',
    'Ανατολική Μακεδονία και Θράκη'  => 'Macedonia Orientale e Tracia',
    'Eastern Macedonia and Thrace'   => 'Macedonia Orientale e Tracia',
    'Θεσσαλία'                        => 'Tessaglia',
    'Thessaly'                        => 'Tessaglia',
    'Στερεά Ελλάδα'                  => 'Grecia Centrale',
    'Central Greece'                  => 'Grecia Centrale',
    'Ήπειρος'                         => 'Epiro',
    'Epirus'                          => 'Epiro',
    'Δυτική Μακεδονία'               => 'Macedonia Occidentale',
    'Western Macedonia'               => 'Macedonia Occidentale',
];

// Coordinate-based region fallback
function guessRegion(float $lat, float $lon): string
{
    // Crete
    if ($lat < 36.2 && $lon > 23.0 && $lon < 26.5) return 'Creta';
    // South Aegean (Cyclades, Dodecanese)
    if ($lat < 38.0 && $lon > 24.0) return 'Egeo Meridionale';
    // Ionian Islands
    if ($lon < 22.0 && $lat > 37.0 && $lat < 40.0) return 'Isole Ionie';
    // North Aegean (Lesbos, Chios, Samos, Thassos)
    if ($lat > 37.5 && $lat < 41.5 && $lon > 24.5) return 'Egeo Settentrionale';
    // Attica
    if ($lat > 37.5 && $lat < 38.3 && $lon > 23.0 && $lon < 24.5) return 'Attiki';
    // Peloponnese
    if ($lat > 36.5 && $lat < 38.0 && $lon > 21.0 && $lon < 23.5) return 'Peloponneso';
    // Macedonia (north coast)
    if ($lat > 40.0) return 'Macedonia';
    // Default
    return 'Grecia';
}

$ports = [];
$seen = [];

foreach ($data['elements'] as $el) {
    $tags = $el['tags'] ?? [];

    // Get coordinates
    $lat = $el['lat'] ?? ($el['center']['lat'] ?? null);
    $lon = $el['lon'] ?? ($el['center']['lon'] ?? null);

    if (!$lat || !$lon) continue;

    // Get name (prefer English, fallback to Greek)
    $name = $tags['name:it'] ?? $tags['name:en'] ?? $tags['name'] ?? null;
    if (!$name) continue;

    // Get city
    $city = $tags['addr:city'] ?? $tags['addr:town'] ?? $tags['addr:municipality'] ?? null;
    if (!$city) {
        // Use name as city if no explicit city
        $city = $tags['name:en'] ?? $tags['name'] ?? $name;
    }
    // Prefer English city name
    $city = $tags['addr:city:en'] ?? $city;

    // Get region
    $state = $tags['addr:state'] ?? $tags['addr:county'] ?? $tags['addr:region'] ?? '';
    $region = $regionByPerifereia[$state] ?? guessRegion((float)$lat, (float)$lon);

    // Deduplicate by lat/lon proximity
    $key = round($lat, 3) . ',' . round($lon, 3);
    if (isset($seen[$key])) continue;
    $seen[$key] = true;

    // Normalize name to Italian title case style
    $name = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
    $city = mb_convert_case($city, MB_CASE_TITLE, 'UTF-8');

    $ports[] = [
        'name'      => $name,
        'city'      => $city,
        'province'  => $tags['addr:county'] ?? $tags['addr:district'] ?? '',
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

echo "Greek marinas found: " . count($ports) . "\n";

// Count by region
$byRegion = [];
foreach ($ports as $p) {
    $byRegion[$p['region']] = ($byRegion[$p['region']] ?? 0) + 1;
}
foreach ($byRegion as $r => $c) {
    echo "  $r: $c\n";
}

// Save
$outPath = __DIR__ . '/../storage/ports_greece.json';
file_put_contents($outPath, json_encode($ports, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\nSaved to storage/ports_greece.json\n";

// Show first 10
echo "\nFirst 10:\n";
foreach (array_slice($ports, 0, 10) as $p) {
    echo "  {$p['name']} ({$p['city']}, {$p['region']}) [{$p['latitude']}, {$p['longitude']}]\n";
}
