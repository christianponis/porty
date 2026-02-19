<?php

namespace Database\Seeders;

use App\Enums\AssessmentStatus;
use App\Models\AssessmentQuestion;
use App\Models\Berth;
use App\Models\SelfAssessment;
use App\Models\SelfAssessmentAnswer;
use Illuminate\Database\Seeder;

class SelfAssessmentSeeder extends Seeder
{
    public function run(): void
    {
        $berths = Berth::with('owner')->get();
        $questions = AssessmentQuestion::all();

        if ($berths->isEmpty() || $questions->isEmpty()) {
            return;
        }

        // Berth index 0: A-12 Rimini (owner 0 - Marco Rossi)
        $berthRimini = $berths[0];
        // Berth index 2: C-22 Sanremo (owner 1 - Giulia Bianchi)
        $berthSanremo = $berths[2];

        $assessments = [
            [
                'berth' => $berthRimini,
                'total_score' => 68.50,
                'anchor_count' => 3,
                // Risposte realistiche per un ormeggio medio-buono a Rimini
                'answer_values' => [
                    4, 1, 1, 3,     // Infrastructure: buon pontile, elettricità ok, acqua ok, bitte medie
                    1, 4, 6,        // Services: wifi sì, raccolta buona, carburante medio-vicino
                    1, 0, 3,        // Security: CCTV sì, guardiania no, antincendio sufficiente
                    8, 4, 7, 3,     // Location: accesso facile, buon riparo, vicino al centro, paesaggio medio
                    1, 3, 1,        // LandServices: parcheggio sì, docce medie, ristoranti sì
                    1, 0, 2,        // Sustainability: differenziata sì, rinnovabili no, certificazioni poche
                ],
            ],
            [
                'berth' => $berthSanremo,
                'total_score' => 76.20,
                'anchor_count' => 4,
                // Risposte realistiche per un ormeggio di qualità superiore a Sanremo
                'answer_values' => [
                    5, 1, 1, 4,     // Infrastructure: ottimo pontile, elettricità ok, acqua ok, bitte buone
                    1, 5, 8,        // Services: wifi sì, raccolta ottima, carburante vicino
                    1, 1, 4,        // Security: CCTV sì, guardiania sì, antincendio buono
                    7, 5, 9, 5,     // Location: buon accesso, ottimo riparo, vicinissimo centro, panorama eccellente
                    1, 4, 1,        // LandServices: parcheggio sì, docce buone, ristoranti sì
                    1, 1, 4,        // Sustainability: differenziata sì, rinnovabili sì, certificazioni buone
                ],
            ],
        ];

        foreach ($assessments as $data) {
            $berth = $data['berth'];

            $assessment = SelfAssessment::updateOrCreate(
                ['berth_id' => $berth->id],
                [
                    'owner_id' => $berth->owner_id,
                    'status' => AssessmentStatus::Submitted,
                    'total_score' => $data['total_score'],
                    'anchor_count' => $data['anchor_count'],
                    'submitted_at' => now(),
                ]
            );

            // Crea le risposte per tutte le 20 domande
            foreach ($questions as $index => $question) {
                $answerValue = $data['answer_values'][$index] ?? 1;

                // Per domande foto-richieste, aggiungi un percorso foto simulato
                $photoPath = null;
                if ($question->requires_photo) {
                    $photoPath = 'assessments/' . $berth->code . '/q' . $question->id . '.jpg';
                }

                SelfAssessmentAnswer::updateOrCreate(
                    [
                        'self_assessment_id' => $assessment->id,
                        'question_id' => $question->id,
                    ],
                    [
                        'answer_value' => $answerValue,
                        'photo_path' => $photoPath,
                    ]
                );
            }

            // Aggiorna il berth con il rating grey
            $berth->update([
                'grey_anchor_count' => $data['anchor_count'],
                'rating_level' => 'grey',
            ]);
        }
    }
}
