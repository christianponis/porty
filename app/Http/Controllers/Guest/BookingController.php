<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Berth;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
        private PaymentService $paymentService,
    ) {}

    public function index()
    {
        $bookings = Auth::user()->bookings()
            ->with(['berth.port', 'berth.owner'])
            ->latest()
            ->paginate(15);

        return view('guest.bookings.index', compact('bookings'));
    }

    public function store(StoreBookingRequest $request)
    {
        $berth = Berth::findOrFail($request->berth_id);

        $booking = $this->bookingService->createBooking(
            $berth,
            Auth::user(),
            $request->only(['start_date', 'end_date', 'guest_notes'])
        );

        return redirect()->route('my-bookings.show', $booking)
            ->with('success', 'Prenotazione inviata! Il proprietario ricevera la tua richiesta.');
    }

    public function show(Booking $booking)
    {
        if ($booking->guest_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['berth.port', 'berth.owner', 'transactions']);

        return view('guest.bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        if ($booking->guest_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($booking->status->value, ['pending', 'confirmed'])) {
            return back()->with('error', 'Questa prenotazione non puo essere cancellata.');
        }

        $wasConfirmed = $booking->status->value === 'confirmed';

        $booking->update([
            'status' => 'cancelled',
            'cancelled_by' => 'guest',
            'cancelled_at' => now(),
        ]);

        // Se era confermata (pagamento processato), esegui rimborso
        if ($wasConfirmed) {
            $this->paymentService->processBookingRefund($booking);
            return back()->with('success', 'Prenotazione cancellata e rimborso processato.');
        }

        return back()->with('success', 'Prenotazione cancellata.');
    }
}
