<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function index(Request $request)
    {
        $berthIds = Auth::user()->berths()->pluck('id');

        $bookings = Booking::with(['berth.port', 'guest'])
            ->whereIn('berth_id', $berthIds)
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15);

        return view('owner.bookings.index', compact('bookings'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $berthIds = Auth::user()->berths()->pluck('id');

        if (!$berthIds->contains($booking->berth_id)) {
            abort(403, 'Non autorizzato.');
        }

        $request->validate([
            'status' => 'required|in:confirmed,cancelled',
            'owner_notes' => 'nullable|string|max:500',
        ]);

        $data = ['status' => $request->status];

        if ($request->owner_notes) {
            $data['owner_notes'] = $request->owner_notes;
        }

        if ($request->status === 'confirmed') {
            // Conferma: processa il pagamento
            $booking->update($data);
            $payment = $this->paymentService->processBookingPayment($booking);

            if ($payment->status->value === 'failed') {
                $booking->update(['status' => 'pending']);
                return back()->with('error', 'Errore nel processamento del pagamento. Riprova.');
            }

            return back()->with('success', 'Prenotazione confermata e pagamento processato.');
        }

        // Cancellazione da parte dell'owner
        $data['cancelled_by'] = 'owner';
        $data['cancelled_at'] = now();

        // Se era confermata, processa rimborso
        if ($booking->status->value === 'confirmed') {
            $booking->update($data);
            $this->paymentService->processBookingRefund($booking);
            return back()->with('success', 'Prenotazione rifiutata e rimborso processato.');
        }

        $booking->update($data);
        return back()->with('success', 'Prenotazione rifiutata.');
    }
}
