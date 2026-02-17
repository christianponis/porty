<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService,
    ) {}

    public function create(Booking $booking)
    {
        if (! $this->reviewService->canReview($booking, Auth::user())) {
            return redirect()->route('my-bookings.show', $booking)
                ->with('error', 'Non puoi lasciare una recensione per questa prenotazione.');
        }

        $booking->load(['berth.port', 'berth.selfAssessment']);

        return view('guest.reviews.create', compact('booking'));
    }

    public function store(Request $request, Booking $booking)
    {
        if (! $this->reviewService->canReview($booking, Auth::user())) {
            return redirect()->route('my-bookings.show', $booking)
                ->with('error', 'Non puoi lasciare una recensione per questa prenotazione.');
        }

        $validated = $request->validate([
            'rating_ormeggio' => 'required|integer|min:1|max:5',
            'rating_servizi' => 'required|integer|min:1|max:5',
            'rating_posizione' => 'required|integer|min:1|max:5',
            'rating_qualita_prezzo' => 'required|integer|min:1|max:5',
            'rating_accoglienza' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
            'verifications' => 'nullable|array',
            'verifications.*.question_key' => 'required_with:verifications|string|max:50',
            'verifications.*.answer' => 'required_with:verifications|boolean',
        ]);

        $this->reviewService->createReview($booking, Auth::user(), $validated);

        return redirect()->route('my-bookings.show', $booking)
            ->with('success', 'Recensione pubblicata con successo!');
    }
}
