<?php

namespace App\Services;

use App\Enums\AssessmentCategory;
use App\Enums\AssessmentStatus;
use App\Enums\RatingLevel;
use App\Models\Berth;
use App\Models\SelfAssessment;

class RatingService
{
    public function calculateSelfAssessmentScore(SelfAssessment $assessment): float
    {
        $assessment->load('answers.question');

        $categoryScores = [];

        foreach (AssessmentCategory::cases() as $category) {
            $answers = $assessment->answers->filter(
                fn($a) => $a->question && $a->question->category === $category
            );

            if ($answers->isEmpty()) {
                $categoryScores[$category->value] = 0;
                continue;
            }

            $totalWeight = 0;
            $weightedScore = 0;

            foreach ($answers as $answer) {
                $question = $answer->question;
                $weight = (float) $question->weight;
                $totalWeight += $weight;

                $normalizedScore = match ($question->question_type) {
                    'boolean' => $answer->answer_value ? 100 : 0,
                    'scale_1_5' => ($answer->answer_value - 1) * 25,
                    'scale_1_10' => ($answer->answer_value - 1) * (100 / 9),
                    default => 0,
                };

                $weightedScore += $normalizedScore * $weight;
            }

            $categoryScores[$category->value] = $totalWeight > 0
                ? $weightedScore / $totalWeight
                : 0;
        }

        $totalScore = 0;
        foreach (AssessmentCategory::cases() as $category) {
            $totalScore += ($categoryScores[$category->value] ?? 0) * $category->weight();
        }

        return round($totalScore, 2);
    }

    public function scoreToAnchors(float $score): int
    {
        $ranges = config('porty.rating.score_to_anchors');

        foreach ($ranges as $anchors => $range) {
            if ($score >= $range[0] && $score <= $range[1]) {
                return $anchors;
            }
        }

        return 1;
    }

    public function submitSelfAssessment(SelfAssessment $assessment): SelfAssessment
    {
        $score = $this->calculateSelfAssessmentScore($assessment);
        $anchors = $this->scoreToAnchors($score);

        $assessment->update([
            'status' => AssessmentStatus::Submitted,
            'total_score' => $score,
            'anchor_count' => $anchors,
            'submitted_at' => now(),
        ]);

        $assessment->berth->update([
            'grey_anchor_count' => $anchors,
        ]);

        $this->updateBerthRatingLevel($assessment->berth);

        return $assessment;
    }

    public function recalculateBlueRating(Berth $berth): void
    {
        $reviews = $berth->reviews;
        $count = $reviews->count();
        $average = $count > 0 ? round($reviews->avg('average_rating'), 2) : null;

        $minReviews = config('porty.rating.min_reviews_for_blue', 3);
        $blueAnchors = null;

        if ($count >= $minReviews && $average !== null) {
            $blueAnchors = $this->scoreToAnchors($average * 20);
        }

        $berth->update([
            'review_count' => $count,
            'review_average' => $average,
            'blue_anchor_count' => $blueAnchors,
        ]);

        $this->updateBerthRatingLevel($berth);
    }

    public function updateBerthRatingLevel(Berth $berth): void
    {
        $berth->refresh();

        $level = null;

        if ($berth->gold_anchor_count && $berth->latestCertification?->isValid()) {
            $level = RatingLevel::Gold;
        } elseif ($berth->blue_anchor_count) {
            $level = RatingLevel::Blue;
        } elseif ($berth->grey_anchor_count) {
            $level = RatingLevel::Grey;
        }

        $berth->update(['rating_level' => $level]);
    }
}
