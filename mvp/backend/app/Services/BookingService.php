<?php

namespace App\Services;

use App\Enums\BookingMode;
use App\Enums\BookingStatus;
use App\Enums\NodoTransactionType;
use App\Models\Berth;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        private NodiService $nodiService,
    ) {}

    public function calculatePrice(Berth $berth, Carbon $startDate, Carbon $endDate): array
    {
        $totalDays = $startDate->diffInDays($endDate);

        if ($totalDays < config('porty.booking.min_days')) {
            throw ValidationException::withMessages([
                'start_date' => "La prenotazione deve essere di almeno " . config('porty.booking.min_days') . " giorno/i.",
            ]);
        }

        if ($totalDays > config('porty.booking.max_days')) {
            throw ValidationException::withMessages([
                'start_date' => "La prenotazione non puo superare " . config('porty.booking.max_days') . " giorni.",
            ]);
        }

        // Calcola il prezzo migliore per il guest
        $totalPrice = $this->calculateBestPrice($berth, $totalDays);

        return [
            'total_days' => $totalDays,
            'total_price' => round($totalPrice, 2),
            'price_per_day' => round($totalPrice / $totalDays, 2),
        ];
    }

    private function calculateBestPrice(Berth $berth, int $totalDays): float
    {
        $priceByDay = $berth->price_per_day * $totalDays;

        // Se c'e prezzo settimanale e il periodo >= 7 giorni
        if ($berth->price_per_week && $totalDays >= 7) {
            $weeks = intdiv($totalDays, 7);
            $remainingDays = $totalDays % 7;
            $priceByWeek = ($weeks * $berth->price_per_week) + ($remainingDays * $berth->price_per_day);
            $priceByDay = min($priceByDay, $priceByWeek);
        }

        // Se c'e prezzo mensile e il periodo >= 30 giorni
        if ($berth->price_per_month && $totalDays >= 30) {
            $months = intdiv($totalDays, 30);
            $remainingDays = $totalDays % 30;
            $priceByMonth = ($months * $berth->price_per_month) + ($remainingDays * $berth->price_per_day);
            $priceByDay = min($priceByDay, $priceByMonth);
        }

        return $priceByDay;
    }

    public function checkAvailability(Berth $berth, Carbon $startDate, Carbon $endDate): bool
    {
        // Verifica che il posto sia attivo e disponibile
        if (! $berth->is_active || $berth->status->value !== 'available') {
            return false;
        }

        // Verifica che il periodo rientri in almeno un periodo di disponibilita
        $hasAvailability = $berth->availabilities()
            ->where('is_available', true)
            ->where('start_date', '<=', $startDate)
            ->where('end_date', '>=', $endDate)
            ->exists();

        if (! $hasAvailability) {
            return false;
        }

        // Verifica che non ci siano prenotazioni sovrapposte (pending o confirmed)
        $hasConflict = $berth->bookings()
            ->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where(function ($q2) use ($startDate, $endDate) {
                    $q2->where('start_date', '<', $endDate)
                        ->where('end_date', '>', $startDate);
                });
            })
            ->exists();

        return ! $hasConflict;
    }

    public function createBooking(Berth $berth, User $guest, array $data): Booking
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $bookingMode = BookingMode::tryFrom($data['booking_mode'] ?? 'rental') ?? BookingMode::Rental;

        // Non puoi prenotare il tuo posto
        if ($berth->owner_id === $guest->id) {
            throw ValidationException::withMessages([
                'berth_id' => 'Non puoi prenotare il tuo stesso posto barca.',
            ]);
        }

        // Check disponibilita
        if (! $this->checkAvailability($berth, $startDate, $endDate)) {
            throw ValidationException::withMessages([
                'start_date' => 'Il posto barca non e disponibile per le date selezionate.',
            ]);
        }

        // Calcola prezzo EUR
        $pricing = $this->calculatePrice($berth, $startDate, $endDate);

        $bookingData = [
            'berth_id' => $berth->id,
            'guest_id' => $guest->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $pricing['total_days'],
            'total_price' => $pricing['total_price'],
            'status' => BookingStatus::Pending,
            'guest_notes' => $data['guest_notes'] ?? null,
            'booking_mode' => $bookingMode,
        ];

        // Gestione Nodi per modalita sharing
        if ($bookingMode === BookingMode::Sharing) {
            if (! $berth->sharing_enabled) {
                throw ValidationException::withMessages([
                    'booking_mode' => 'Questo posto barca non accetta lo sharing.',
                ]);
            }

            $nodiAmount = $this->nodiService->calculateNodiForBooking($berth, $pricing['total_days']);
            $guestWallet = $this->nodiService->getOrCreateWallet($guest);

            if ($guestWallet->balance < $nodiAmount) {
                throw ValidationException::withMessages([
                    'booking_mode' => 'Saldo Nodi insufficiente. Servono ' . $nodiAmount . ' Nodi.',
                ]);
            }

            $bookingData['nodi_amount'] = $nodiAmount;
            $bookingData['total_price'] = 0;
        } elseif ($bookingMode === BookingMode::SharingCompensation) {
            if (! $berth->sharing_enabled) {
                throw ValidationException::withMessages([
                    'booking_mode' => 'Questo posto barca non accetta lo sharing.',
                ]);
            }

            $nodiAmount = $this->nodiService->calculateNodiForBooking($berth, $pricing['total_days']);
            $guestWallet = $this->nodiService->getOrCreateWallet($guest);

            $nodiAvailable = min($guestWallet->balance, $nodiAmount);
            $eurCompensation = ($nodiAmount - $nodiAvailable) > 0
                ? round($pricing['total_price'] * (($nodiAmount - $nodiAvailable) / $nodiAmount), 2)
                : 0;

            $bookingData['nodi_amount'] = $nodiAvailable;
            $bookingData['eur_compensation'] = $eurCompensation;
            $bookingData['total_price'] = $eurCompensation;
        }

        $booking = Booking::create($bookingData);

        // Processa transazioni Nodi
        if (in_array($bookingMode, [BookingMode::Sharing, BookingMode::SharingCompensation]) && ($booking->nodi_amount ?? 0) > 0) {
            $guestWallet = $this->nodiService->getOrCreateWallet($guest);
            $this->nodiService->debitNodi(
                $guestWallet,
                $booking->nodi_amount,
                NodoTransactionType::Spent,
                $booking,
                "Prenotazione #{$booking->id} - {$berth->title}"
            );

            $ownerWallet = $this->nodiService->getOrCreateWallet($berth->owner);
            $this->nodiService->creditNodi(
                $ownerWallet,
                $booking->nodi_amount,
                NodoTransactionType::Earned,
                $booking,
                "Prenotazione #{$booking->id} - {$berth->title}"
            );
        }

        return $booking;
    }
}
