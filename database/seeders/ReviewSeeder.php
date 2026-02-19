<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Models\Berth;
use App\Models\Booking;
use App\Models\Review;
use App\Models\ReviewVerification;
use App\Models\SelfAssessment;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $completedBookings = Booking::where('status', BookingStatus::Completed)
            ->with(['berth', 'guest'])
            ->get();

        if ($completedBookings->isEmpty()) {
            return;
        }

        // Berth IDs che hanno un self-assessment (per decidere is_verified)
        $assessedBerthIds = SelfAssessment::pluck('berth_id')->toArray();

        // Commenti realistici in italiano per le recensioni
        $comments = [
            'Ormeggio molto comodo e ben tenuto. Il personale del marina è stato gentilissimo e disponibile. Torneremo sicuramente!',
            'Buona posizione, vicino al centro. Le colonnine elettriche funzionano bene. Unica pecca: le docce potrebbero essere più pulite.',
            'Posto barca fantastico con una vista mozzafiato. Servizi eccellenti e sicurezza garantita. Consiglio vivamente.',
            'Rapporto qualità-prezzo nella media. Il pontile è in buone condizioni ma il Wi-Fi è instabile. Marina comunque gradevole.',
            'Esperienza complessivamente positiva. L\'accesso è facile e il riparo dal vento è ottimo. I servizi a terra sono adeguati.',
            'Marina ben organizzato con tutti i servizi necessari. La raccolta rifiuti è puntuale e l\'area è sempre pulita.',
            'Ottimo ormeggio per soste brevi. Posizione strategica per esplorare la costa. Personale cortese e professionale.',
            'Ormeggio sicuro e ben protetto. La vicinanza ai ristoranti e ai negozi è un plus notevole. Soddisfatto della scelta.',
        ];

        // Chiavi di verifica disponibili
        $verificationKeys = [
            'electricity_available',
            'water_available',
            'wifi_working',
            'secure_area',
            'clean_facilities',
        ];

        foreach ($completedBookings as $index => $booking) {
            $hasAssessment = in_array($booking->berth_id, $assessedBerthIds);

            // Genera rating realistici tra 3 e 5
            $ratingOrmeggio = rand(3, 5);
            $ratingServizi = rand(3, 5);
            $ratingPosizione = rand(3, 5);
            $ratingQualitaPrezzo = rand(3, 5);
            $ratingAccoglienza = rand(3, 5);

            $averageRating = round(
                ($ratingOrmeggio + $ratingServizi + $ratingPosizione + $ratingQualitaPrezzo + $ratingAccoglienza) / 5,
                2
            );

            // Se il berth ha un self-assessment, 70% chance di essere verificata
            $isVerified = $hasAssessment && (rand(1, 100) <= 70);

            $review = Review::firstOrCreate(
                ['booking_id' => $booking->id],
                [
                    'berth_id' => $booking->berth_id,
                    'guest_id' => $booking->guest_id,
                    'rating_ormeggio' => $ratingOrmeggio,
                    'rating_servizi' => $ratingServizi,
                    'rating_posizione' => $ratingPosizione,
                    'rating_qualita_prezzo' => $ratingQualitaPrezzo,
                    'rating_accoglienza' => $ratingAccoglienza,
                    'average_rating' => $averageRating,
                    'comment' => $comments[$index % count($comments)],
                    'is_verified' => $isVerified,
                ]
            );

            // Crea verifiche solo se la review è nuova
            if ($review->wasRecentlyCreated) {
                $numVerifications = rand(3, 4);
                $selectedKeys = collect($verificationKeys)->shuffle()->take($numVerifications);

                foreach ($selectedKeys as $key) {
                    ReviewVerification::firstOrCreate([
                        'review_id' => $review->id,
                        'question_key' => $key,
                    ], [
                        'answer' => (bool) rand(0, 1),
                    ]);
                }
            }
        }

        // Aggiorna le statistiche di ogni berth che ha ricevuto recensioni
        $reviewedBerthIds = Review::distinct()->pluck('berth_id');

        foreach ($reviewedBerthIds as $berthId) {
            $berth = Berth::find($berthId);

            if (! $berth) {
                continue;
            }

            $reviews = Review::where('berth_id', $berthId)->get();
            $reviewCount = $reviews->count();
            $reviewAverage = round($reviews->avg('average_rating'), 2);

            $updateData = [
                'review_count' => $reviewCount,
                'review_average' => $reviewAverage,
            ];

            // Calcola blue_anchor_count se ci sono almeno 3 recensioni
            if ($reviewCount >= 3) {
                // Mappa la media delle recensioni (1-5) in ancore blu (1-5)
                $blueAnchors = max(1, min(5, round($reviewAverage)));
                $updateData['blue_anchor_count'] = $blueAnchors;

                // Se il berth aveva già un rating grey, ora passa a blue
                if ($berth->rating_level?->value === 'grey' || $berth->rating_level === null) {
                    $updateData['rating_level'] = 'blue';
                }
            }

            $berth->update($updateData);
        }
    }
}
