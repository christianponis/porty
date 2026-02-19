<?php
$ports = json_decode(file_get_contents(__DIR__ . '/../storage/ports_greece.json'), true);

// Keep only ports with Latin-script name AND city
$filtered = array_values(array_filter($ports, function($p) {
    return !preg_match('/[\x{0370}-\x{03FF}]/u', $p['name']) &&
           !preg_match('/[\x{0370}-\x{03FF}]/u', $p['city']);
}));

file_put_contents(__DIR__ . '/../storage/ports_greece.json', json_encode($filtered, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Saved: " . count($filtered) . " ports\n";

$byRegion = [];
foreach ($filtered as $p) {
    $byRegion[$p['region']] = ($byRegion[$p['region']] ?? 0) + 1;
}
ksort($byRegion);
foreach ($byRegion as $r => $c) {
    echo "  $r: $c\n";
}
