<?php

namespace App\Http\Controllers\Api\Booking;

use App\Enums\BookingMode;
use App\Enums\BookingStatus;
use App\Enums\NodoTransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Berth;
use App\Models\Booking;
use App\Models\User;
use App\Services\BookingService;
use App\Services\NodiService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private NodiService $nodiService,
    ) {}

    /**
     * Guest bookings list (paginated).
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $perPage = $request->integer('per_page', 10);

        $bookings = Booking::query()
            ->with(['berth.port'])
            ->where('guest_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $guests = User::whereIn('id', $bookings->pluck('guest_id')->unique()->values())
            ->get()
            ->keyBy('id');

        $results = $bookings->map(fn (Booking $booking) => $this->transformBooking($booking, $guests));

        return response()->json([
            'count' => $bookings->total(),
            'results' => $results->values(),
        ]);
    }

    /**
     * Create a new booking (for guests).
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        $user = auth('api')->user();
        $berth = Berth::findOrFail($request->berth_id);

        $bookingData = $request->validated();
        $bookingData['guest_notes'] = json_encode([
            'boat_name' => $request->boat_name,
            'boat_length' => $request->boat_length,
            'boat_width' => $request->boat_width,
            'boat_draft' => $request->boat_draft,
        ]);

        $booking = $this->bookingService->createBooking($berth, $user, $bookingData);
        $booking->load('berth.port');

        return response()->json([
            'message' => 'Prenotazione creata con successo.',
            'data' => new BookingResource($booking),
        ], 201);
    }

    /**
     * Guest dashboard payload.
     */
    public function guestDashboard(): JsonResponse
    {
        $user = auth('api')->user();
        $today = Carbon::today();

        $allBookings = Booking::query()
            ->with(['berth.port'])
            ->where('guest_id', $user->id)
            ->orderByDesc('start_date')
            ->get();

        $guests = User::whereIn('id', $allBookings->pluck('guest_id')->unique()->values())
            ->get()
            ->keyBy('id');

        $upcoming = $allBookings
            ->filter(fn (Booking $booking) => $booking->end_date && $booking->end_date->gte($today))
            ->values()
            ->map(fn (Booking $booking) => $this->transformBooking($booking, $guests));

        $past = $allBookings
            ->filter(fn (Booking $booking) => $booking->end_date && $booking->end_date->lt($today))
            ->values()
            ->map(fn (Booking $booking) => $this->transformBooking($booking, $guests));

        $wallet = $this->nodiService->getOrCreateWallet($user);

        return response()->json([
            'upcoming_bookings' => $upcoming,
            'past_bookings' => $past,
            'nodi_balance' => (float) $wallet->balance,
            'total_bookings' => $allBookings->count(),
        ]);
    }

    /**
     * Owner dashboard payload.
     */
    public function ownerDashboard(): JsonResponse
    {
        $user = auth('api')->user();

        $berths = Berth::query()
            ->where('owner_id', $user->id)
            ->pluck('id');

        $bookings = Booking::query()
            ->whereIn('berth_id', $berths)
            ->get();

        $wallet = $this->nodiService->getOrCreateWallet($user);

        return response()->json([
            'total_berths' => $berths->count(),
            'total_bookings' => $bookings->count(),
            'pending_bookings' => $bookings->where('status', BookingStatus::Pending)->count(),
            'confirmed_bookings' => $bookings->where('status', BookingStatus::Confirmed)->count(),
            'nodi_balance' => (float) $wallet->balance,
        ]);
    }

    /**
     * Owner bookings list for a specific berth.
     */
    public function ownerBerthBookings(Request $request, Berth $berth): JsonResponse
    {
        $user = auth('api')->user();
        if ($berth->owner_id !== $user->id) {
            return response()->json(['message' => 'Non autorizzato.'], 403);
        }

        $perPage = $request->integer('per_page', 10);
        $bookings = Booking::query()
            ->with(['berth.port'])
            ->where('berth_id', $berth->id)
            ->orderByDesc('created_at')
            ->paginate($perPage);

        $guests = User::whereIn('id', $bookings->pluck('guest_id')->unique()->values())
            ->get()
            ->keyBy('id');

        $results = $bookings->map(fn (Booking $booking) => $this->transformBooking($booking, $guests));

        return response()->json([
            'count' => $bookings->total(),
            'results' => $results->values(),
        ]);
    }

    /**
     * Confirm a pending booking (owner only).
     */
    public function confirm(Booking $booking): JsonResponse
    {
        $user = auth('api')->user();
        if ($booking->berth?->owner_id !== $user->id) {
            return response()->json(['message' => 'Non autorizzato.'], 403);
        }

        if ($booking->status !== BookingStatus::Pending) {
            return response()->json(['message' => 'La prenotazione non e in attesa.'], 422);
        }

        $booking->update(['status' => BookingStatus::Confirmed]);
        $booking->load(['berth.port']);
        $guests = User::where('id', $booking->guest_id)->get()->keyBy('id');

        return response()->json($this->transformBooking($booking, $guests));
    }

    /**
     * Reject a pending booking (owner only).
     */
    public function reject(Request $request, Booking $booking): JsonResponse
    {
        $user = auth('api')->user();
        if ($booking->berth?->owner_id !== $user->id) {
            return response()->json(['message' => 'Non autorizzato.'], 403);
        }

        if ($booking->status !== BookingStatus::Pending) {
            return response()->json(['message' => 'La prenotazione non e in attesa.'], 422);
        }

        $booking->update([
            'status' => BookingStatus::Cancelled,
            'owner_notes' => $request->input('reason'),
            'cancelled_by' => $user->id,
            'cancelled_at' => now(),
        ]);

        $booking->load(['berth.port']);
        $guests = User::where('id', $booking->guest_id)->get()->keyBy('id');

        return response()->json($this->transformBooking($booking, $guests));
    }

    /**
     * Show a single booking (for the guest who owns it).
     */
    public function show(Booking $booking): JsonResponse
    {
        $user = auth('api')->user();

        if ($booking->guest_id !== $user->id && $booking->berth?->owner_id !== $user->id && ! $user->isAdmin()) {
            return response()->json([
                'message' => 'Non autorizzato.',
            ], 403);
        }

        $booking->load('berth.port');
        $guests = User::where('id', $booking->guest_id)->get()->keyBy('id');
        return response()->json($this->transformBooking($booking, $guests));
    }

    /**
     * Cancel a booking (if status is pending or confirmed).
     */
    public function cancel(Booking $booking): JsonResponse
    {
        $user = auth('api')->user();

        if ($booking->guest_id !== $user->id) {
            return response()->json([
                'message' => 'Non autorizzato.',
            ], 403);
        }

        if (! in_array($booking->status, [BookingStatus::Pending, BookingStatus::Confirmed])) {
            return response()->json([
                'message' => 'La prenotazione non può essere cancellata.',
            ], 422);
        }

        // Refund nodi for sharing modes
        if (
            in_array($booking->booking_mode, [BookingMode::Sharing, BookingMode::SharingCompensation])
            && ($booking->nodi_amount ?? 0) > 0
        ) {
            $guestWallet = $this->nodiService->getOrCreateWallet($user);
            $this->nodiService->creditNodi(
                $guestWallet,
                $booking->nodi_amount,
                NodoTransactionType::Adjustment,
                $booking,
                "Rimborso cancellazione prenotazione #{$booking->id}"
            );

            // Debit from owner
            $owner = $booking->berth->owner;
            if ($owner) {
                $ownerWallet = $this->nodiService->getOrCreateWallet($owner);
                if ($ownerWallet->balance >= $booking->nodi_amount) {
                    $this->nodiService->debitNodi(
                        $ownerWallet,
                        $booking->nodi_amount,
                        NodoTransactionType::Adjustment,
                        $booking,
                        "Storno cancellazione prenotazione #{$booking->id}"
                    );
                }
            }
        }

        $booking->update([
            'status' => BookingStatus::Cancelled,
            'cancelled_by' => $user->id,
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'message' => 'Prenotazione cancellata con successo.',
            'data' => new BookingResource($booking->fresh('berth.port')),
        ]);
    }

    private function transformBooking(Booking $booking, $guests): array
    {
        $guest = $guests->get($booking->guest_id);
        $nameParts = explode(' ', $guest?->name ?? '', 2);

        return [
            'id' => $booking->id,
            'berth' => [
                'id' => $booking->berth?->id,
                'name' => $booking->berth?->title,
                'port' => [
                    'id' => $booking->berth?->port?->id,
                    'name' => $booking->berth?->port?->name,
                    'city' => $booking->berth?->port?->city,
                ],
                'max_length' => (float) ($booking->berth?->length_m ?? 0),
                'max_beam' => (float) ($booking->berth?->width_m ?? 0),
                'max_draft' => (float) ($booking->berth?->max_draft_m ?? 0),
                'price_per_night' => (float) ($booking->berth?->price_per_day ?? 0),
                'anchor_rating' => (int) (
                    $booking->berth?->gold_anchor_count
                    ?? $booking->berth?->blue_anchor_count
                    ?? $booking->berth?->grey_anchor_count
                    ?? 0
                ),
                'anchor_level' => $booking->berth?->rating_level?->value ?? 'grey',
            ],
            'guest' => [
                'id' => $guest?->id,
                'first_name' => $nameParts[0] ?? '',
                'last_name' => $nameParts[1] ?? '',
                'email' => $guest?->email,
            ],
            'check_in' => $booking->start_date?->toDateString(),
            'check_out' => $booking->end_date?->toDateString(),
            'total_price' => (float) $booking->total_price,
            'nodi_earned' => (float) ($booking->nodi_amount ?? 0),
            'status' => $booking->status?->value,
            'sharing' => in_array($booking->booking_mode, [BookingMode::Sharing, BookingMode::SharingCompensation], true),
            'boat_length' => (float) ($booking->boat_length ?? 0),
            'boat_name' => (string) ($booking->boat_name ?? ''),
            'notes' => $booking->guest_notes,
            'created_at' => $booking->created_at?->toISOString(),
        ];
    }
}
