<?php

namespace Database\Seeders;

use App\Models\Berth;
use App\Models\BerthAvailability;
use App\Models\Port;
use App\Models\User;
use Illuminate\Database\Seeder;

class BerthSeeder extends Seeder
{
    public function run(): void
    {
        $owners = User::where('role', 'owner')->get();
        $ports = Port::all();

        if ($owners->isEmpty() || $ports->isEmpty()) {
            return;
        }

        $berths = [
            [
                'owner_index' => 0,
                'port_name' => 'Marina di Rimini',
                'code' => 'A-12',
                'title' => 'Posto barca Rimini - Zona A',
                'description' => 'Posto barca ben riparato nella zona A del porto, ideale per barche fino a 12 metri.',
                'length_m' => 12.00,
                'width_m' => 4.50,
                'max_draft_m' => 2.50,
                'price_per_day' => 85.00,
                'price_per_week' => 500.00,
                'price_per_month' => 1800.00,
                'amenities' => ['electricity', 'water', 'wifi'],
            ],
            [
                'owner_index' => 0,
                'port_name' => 'Marina di Rimini',
                'code' => 'B-07',
                'title' => 'Ormeggio Rimini zona B - Medio',
                'description' => 'Posto barca nella zona B, comodo accesso ai servizi del porto.',
                'length_m' => 8.00,
                'width_m' => 3.20,
                'max_draft_m' => 1.80,
                'price_per_day' => 55.00,
                'price_per_week' => 330.00,
                'price_per_month' => 1200.00,
                'amenities' => ['electricity', 'water'],
            ],
            [
                'owner_index' => 1,
                'port_name' => 'Porto di Sanremo',
                'code' => 'C-22',
                'title' => 'Ormeggio Sanremo - Vista mare aperto',
                'description' => 'Posto barca con vista mare aperto, posizione privilegiata nel porto vecchio.',
                'length_m' => 15.00,
                'width_m' => 5.00,
                'max_draft_m' => 3.00,
                'price_per_day' => 120.00,
                'price_per_week' => 750.00,
                'price_per_month' => 2800.00,
                'amenities' => ['electricity', 'water', 'wifi', 'security'],
            ],
            [
                'owner_index' => 0,
                'port_name' => 'Marina di Porto Cervo',
                'code' => 'D-03',
                'title' => 'Posto barca Porto Cervo - Premium',
                'description' => 'Ormeggio premium nella Costa Smeralda, servizi di lusso inclusi.',
                'length_m' => 20.00,
                'width_m' => 6.00,
                'max_draft_m' => 3.50,
                'price_per_day' => 250.00,
                'price_per_week' => 1500.00,
                'price_per_month' => 5500.00,
                'amenities' => ['electricity', 'water', 'wifi', 'security', 'concierge'],
            ],
            [
                'owner_index' => 1,
                'port_name' => 'Porto di Tropea',
                'code' => 'A-05',
                'title' => 'Ormeggio Tropea - Costa degli Dei',
                'description' => 'Posto barca nel suggestivo porto di Tropea, perfetto per esplorare la costa calabrese.',
                'length_m' => 10.00,
                'width_m' => 3.80,
                'max_draft_m' => 2.00,
                'price_per_day' => 70.00,
                'price_per_week' => 420.00,
                'price_per_month' => 1500.00,
                'amenities' => ['electricity', 'water'],
            ],
            [
                'owner_index' => 1,
                'port_name' => 'Marina di Loano',
                'code' => 'F-18',
                'title' => 'Posto barca Loano - Grande',
                'description' => 'Ampio posto barca in uno dei marina più attrezzati della Liguria.',
                'length_m' => 14.00,
                'width_m' => 4.80,
                'max_draft_m' => 2.80,
                'price_per_day' => 95.00,
                'price_per_week' => 580.00,
                'price_per_month' => 2100.00,
                'amenities' => ['electricity', 'water', 'wifi', 'security'],
            ],
        ];

        foreach ($berths as $berthData) {
            $owner = $owners[$berthData['owner_index']];
            $port = $ports->firstWhere('name', $berthData['port_name']);

            if (! $port) {
                continue;
            }

            $berth = Berth::create([
                'owner_id' => $owner->id,
                'port_id' => $port->id,
                'code' => $berthData['code'],
                'title' => $berthData['title'],
                'description' => $berthData['description'],
                'length_m' => $berthData['length_m'],
                'width_m' => $berthData['width_m'],
                'max_draft_m' => $berthData['max_draft_m'],
                'price_per_day' => $berthData['price_per_day'],
                'price_per_week' => $berthData['price_per_week'],
                'price_per_month' => $berthData['price_per_month'],
                'amenities' => $berthData['amenities'],
                'status' => 'available',
                'is_active' => true,
            ]);

            // Disponibilita per i prossimi 6 mesi
            BerthAvailability::create([
                'berth_id' => $berth->id,
                'start_date' => now()->addDays(1)->toDateString(),
                'end_date' => now()->addMonths(6)->toDateString(),
                'is_available' => true,
                'note' => 'Disponibile stagione 2026',
            ]);
        }
    }
}
