<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Review;
use App\Models\ReviewVerification;
use App\Models\User;

class ReviewService
{
    public function __construct(
        private RatingService $ratingService,
    ) {}

    public function canReview(Booking $booking, User $guest): bool
    {
        return $booking->status === BookingStatus::Completed
            && $booking->guest_id === $guest->id
            && ! $booking->review()->exists();
    }

    public function createReview(Booking $booking, User $guest, array $data): Review
    {
        $average = round(
            ($data['rating_ormeggio'] + $data['rating_servizi'] + $data['rating_posizione']
                + $data['rating_qualita_prezzo'] + $data['rating_accoglienza']) / 5,
            2
        );

        $review = Review::create([
            'booking_id' => $booking->id,
            'berth_id' => $booking->berth_id,
            'guest_id' => $guest->id,
            'rating_ormeggio' => $data['rating_ormeggio'],
            'rating_servizi' => $data['rating_servizi'],
            'rating_posizione' => $data['rating_posizione'],
            'rating_qualita_prezzo' => $data['rating_qualita_prezzo'],
            'rating_accoglienza' => $data['rating_accoglienza'],
            'average_rating' => $average,
            'comment' => $data['comment'] ?? null,
        ]);

        if (! empty($data['verifications'])) {
            foreach ($data['verifications'] as $verification) {
                ReviewVerification::create([
                    'review_id' => $review->id,
                    'question_key' => $verification['question_key'],
                    'answer' => $verification['answer'],
                ]);
            }

            $review->update(['is_verified' => $this->crossVerify($review)]);
        }

        $this->ratingService->recalculateBlueRating($booking->berth);

        return $review;
    }

    public function crossVerify(Review $review): bool
    {
        $berth = $review->berth;
        $assessment = $berth->selfAssessment;

        if (! $assessment || $assessment->answers->isEmpty()) {
            return false;
        }

        $verifications = $review->verifications;
        if ($verifications->isEmpty()) {
            return false;
        }

        $assessment->load('answers.question');

        $booleanAnswers = $assessment->answers
            ->filter(fn($a) => $a->question && $a->question->question_type === 'boolean')
            ->keyBy(fn($a) => $a->question->category->value . '_' . $a->question->sort_order);

        $matches = 0;
        $total = 0;

        foreach ($verifications as $verification) {
            $assessmentAnswer = $booleanAnswers->get($verification->question_key);
            if ($assessmentAnswer) {
                $total++;
                if ((bool) $assessmentAnswer->answer_value === $verification->answer) {
                    $matches++;
                }
            }
        }

        return $total > 0 && ($matches / $total) >= 0.7;
    }
}
