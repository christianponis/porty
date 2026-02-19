<?php

namespace Database\Seeders;

use App\Models\Port;
use Illuminate\Database\Seeder;

class PortSeeder extends Seeder
{
    /**
     * Seed ports from official government datasets:
     * - Italy: Ministero delle Infrastrutture e dei Trasporti (330+ ports)
     * - France: data.gouv.fr / Sandre (Mediterranean plaisance ports)
     *
     * JSON files are pre-built by scripts/build_ports_seeder.php and scripts/build_french_ports.php
     */
    public function run(): void
    {
        $italianPorts = $this->loadJson(storage_path('ports_italy_mapped.json'));
        $frenchPorts = $this->loadJson(storage_path('ports_france_med.json'));
        $greekPorts = $this->loadJson(storage_path('ports_greece.json'));

        $this->seedItalianPorts($italianPorts);
        $this->seedFrenchPorts($frenchPorts);
        $this->seedGreekPorts($greekPorts);
    }

    private function seedItalianPorts(array $ports): void
    {
        $count = 0;
        foreach ($ports as $port) {
            // Check if a port already exists for this city (from old seeder)
            $existing = Port::where('country', 'Italia')
                ->where('city', $port['city'])
                ->first();

            if ($existing) {
                // Update existing record with official data (keep name to preserve berth links)
                $existing->update([
                    'province' => $port['province'],
                    'region' => $port['region'],
                    'latitude' => $port['latitude'] ?: $existing->latitude,
                    'longitude' => $port['longitude'] ?: $existing->longitude,
                    'is_active' => true,
                ]);
            } else {
                Port::create([
                    'name' => $port['name'],
                    'city' => $port['city'],
                    'province' => $port['province'],
                    'region' => $port['region'],
                    'country' => 'Italia',
                    'latitude' => $port['latitude'],
                    'longitude' => $port['longitude'],
                    'is_active' => true,
                ]);
            }
            $count++;
        }

        $this->command->info("Seeded {$count} Italian ports.");
    }

    private function seedFrenchPorts(array $ports): void
    {
        $count = 0;
        foreach ($ports as $port) {
            $name = $this->normalizeFrenchPortName($port['name']);

            $existing = Port::where('country', 'France')
                ->where('city', $port['city'])
                ->where('name', $name)
                ->first();

            if ($existing) {
                $existing->update([
                    'province' => $port['province'],
                    'region' => $port['region'],
                    'latitude' => $port['latitude'] ?: $existing->latitude,
                    'longitude' => $port['longitude'] ?: $existing->longitude,
                    'is_active' => true,
                ]);
            } else {
                Port::create([
                    'name' => $name,
                    'city' => $port['city'],
                    'province' => $port['province'],
                    'region' => $port['region'],
                    'country' => 'France',
                    'latitude' => $port['latitude'],
                    'longitude' => $port['longitude'],
                    'is_active' => true,
                ]);
            }
            $count++;
        }

        $this->command->info("Seeded {$count} French Mediterranean ports.");
    }

    private function normalizeFrenchPortName(string $name): string
    {
        $name = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');

        $name = preg_replace_callback('/\b(De|Du|Des|D\'|Le|La|Les|L\')\b/u', function ($m) {
            return mb_strtolower($m[0], 'UTF-8');
        }, $name);

        // Ensure first letter is uppercase
        return mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($name, 1, null, 'UTF-8');
    }

    private function seedGreekPorts(array $ports): void
    {
        $count = 0;
        foreach ($ports as $port) {
            $existing = Port::where('country', 'Greece')
                ->where('city', $port['city'])
                ->where('name', $port['name'])
                ->first();

            if ($existing) {
                $existing->update([
                    'province'  => $port['province'],
                    'region'    => $port['region'],
                    'latitude'  => $port['latitude'] ?: $existing->latitude,
                    'longitude' => $port['longitude'] ?: $existing->longitude,
                    'is_active' => true,
                ]);
            } else {
                Port::create([
                    'name'      => $port['name'],
                    'city'      => $port['city'],
                    'province'  => $port['province'],
                    'region'    => $port['region'],
                    'country'   => 'Greece',
                    'latitude'  => $port['latitude'],
                    'longitude' => $port['longitude'],
                    'is_active' => true,
                ]);
            }
            $count++;
        }

        $this->command->info("Seeded {$count} Greek ports.");
    }

    private function loadJson(string $path): array
    {
        if (!file_exists($path)) {
            $this->command->warn("File not found: {$path}. Run the build scripts first.");
            return [];
        }

        return json_decode(file_get_contents($path), true) ?: [];
    }
}
