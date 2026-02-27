<?php

namespace App\Http\Controllers\Api\Rating;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Booking;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService,
    ) {}

    /**
     * Create a review for a completed booking (for guests).
     */
    public function store(StoreReviewRequest $request, Booking $booking): JsonResponse
    {
        $user = auth('api')->user();

        if (! $this->reviewService->canReview($booking, $user)) {
            return response()->json([
                'message' => 'Non è possibile recensire questa prenotazione. Verifica che sia completata e che non sia già stata recensita.',
            ], 422);
        }

        $review = $this->reviewService->createReview($booking, $user, $request->validated());
        $review->load('guest');

        return response()->json([
            'message' => 'Recensione creata con successo.',
            'data' => new ReviewResource($review),
        ], 201);
    }
}
