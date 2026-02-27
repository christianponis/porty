'use client';

import { useEffect, useState, useCallback } from 'react';
import { useParams, useRouter } from 'next/navigation';
import Link from 'next/link';
import { useUIStore } from '@/stores/ui';
import Button from '@/components/common/Button';
import Badge from '@/components/common/Badge';
import AnchorRating from '@/components/domain/AnchorRating';
import NodiBadge from '@/components/domain/NodiBadge';
import * as guestApi from '@/lib/api/guest';
import type { Booking } from '@/lib/api/types';
import {
  ArrowLeftIcon,
  CalendarDaysIcon,
  MapPinIcon,
  CurrencyEuroIcon,
  StarIcon,
  XCircleIcon,
} from '@heroicons/react/24/outline';

const statusMap: Record<string, { label: string; variant: 'success' | 'warning' | 'danger' | 'info' }> = {
  pending: { label: 'In attesa', variant: 'warning' },
  confirmed: { label: 'Confermata', variant: 'success' },
  cancelled: { label: 'Cancellata', variant: 'danger' },
  completed: { label: 'Completata', variant: 'info' },
};

export default function GuestBookingDetailPage() {
  const params = useParams();
  const router = useRouter();
  const bookingId = Number(params.id);
  const { addToast } = useUIStore();

  const [booking, setBooking] = useState<Booking | null>(null);
  const [loading, setLoading] = useState(true);
  const [cancelling, setCancelling] = useState(false);

  const fetchBooking = useCallback(async () => {
    try {
      setLoading(true);
      const data = await guestApi.getBooking(bookingId);
      setBooking(data);
    } catch {
      addToast({ type: 'error', message: 'Errore nel caricamento della prenotazione' });
    } finally {
      setLoading(false);
    }
  }, [bookingId, addToast]);

  useEffect(() => {
    fetchBooking();
  }, [fetchBooking]);

  const handleCancel = async () => {
    if (!confirm('Sei sicuro di voler cancellare questa prenotazione?')) return;
    try {
      setCancelling(true);
      await guestApi.cancelBooking(bookingId);
      addToast({ type: 'success', message: 'Prenotazione cancellata con successo' });
      fetchBooking();
    } catch {
      addToast({ type: 'error', message: 'Errore nella cancellazione della prenotazione' });
    } finally {
      setCancelling(false);
    }
  };

  if (loading) {
    return (
      <div className="space-y-6">
        <div className="h-8 w-64 animate-pulse rounded-lg bg-sky-50" />
        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
          <div className="h-80 animate-pulse rounded-xl bg-sky-50" />
          <div className="h-80 animate-pulse rounded-xl bg-sky-50" />
        </div>
      </div>
    );
  }

  if (!booking) {
    return (
      <div className="flex flex-col items-center justify-center py-16">
        <p className="text-slate-500">Prenotazione non trovata</p>
        <Link href="/guest/bookings" className="mt-4">
          <Button variant="secondary">Torna alle prenotazioni</Button>
        </Link>
      </div>
    );
  }

  const status = statusMap[booking.status];
  const canCancel = booking.status === 'pending' || booking.status === 'confirmed';
  const canReview = booking.status === 'completed';
  const nights = Math.ceil(
    (new Date(booking.check_out).getTime() - new Date(booking.check_in).getTime()) /
      (1000 * 60 * 60 * 24)
  );
  const nightlyPrice =
    typeof booking.berth?.price_per_night === 'number' && Number.isFinite(booking.berth.price_per_night)
      ? booking.berth.price_per_night
      : nights > 0
        ? booking.total_price / nights
        : 0;

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div className="flex items-center gap-4">
          <Link
            href="/guest/bookings"
            className="flex h-9 w-9 items-center justify-center rounded-lg border border-sky-200 bg-white text-slate-500 transition-colors hover:bg-sky-50 hover:text-sky-700"
          >
            <ArrowLeftIcon className="h-4 w-4" />
          </Link>
          <div>
            <h1 className="text-2xl font-bold text-sky-900">
              Prenotazione #{booking.id}
            </h1>
            <p className="text-sm text-slate-500">
              Creata il {new Date(booking.created_at).toLocaleDateString('it-IT')}
            </p>
          </div>
        </div>
        <Badge variant={status.variant} className="text-sm px-3 py-1">
          {status.label}
        </Badge>
      </div>

      <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {/* Booking info card */}
        <div className="rounded-xl border border-sky-100 bg-white p-6 shadow-sm space-y-5">
          <h2 className="text-lg font-semibold text-sky-900">Dettagli prenotazione</h2>

          <div className="space-y-4">
            <div className="flex items-center gap-3">
              <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                <CalendarDaysIcon className="h-5 w-5" />
              </div>
              <div>
                <p className="text-xs text-slate-400">Periodo</p>
                <p className="text-sm font-medium text-slate-700">
                  {new Date(booking.check_in).toLocaleDateString('it-IT')} -{' '}
                  {new Date(booking.check_out).toLocaleDateString('it-IT')}
                  <span className="ml-2 text-xs text-slate-400">({nights} notti)</span>
                </p>
              </div>
            </div>

            <div className="flex items-center gap-3">
              <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                <CurrencyEuroIcon className="h-5 w-5" />
              </div>
              <div>
                <p className="text-xs text-slate-400">Prezzo totale</p>
                <p className="text-sm font-medium text-slate-700">
                  &euro;{booking.total_price.toFixed(2)}
                </p>
              </div>
            </div>

            {booking.nodi_earned > 0 && (
              <div className="flex items-center gap-3">
                <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                  <svg className="h-5 w-5" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="2" />
                    <path d="M12 6C12 6 8 10 8 13C8 15.2 9.8 17 12 17C14.2 17 16 15.2 16 13C16 10 12 6 12 6Z" fill="currentColor" />
                  </svg>
                </div>
                <div>
                  <p className="text-xs text-slate-400">Nodi guadagnati</p>
                  <NodiBadge amount={booking.nodi_earned} />
                </div>
              </div>
            )}

            {/* Boat info */}
            <div className="rounded-lg bg-sky-50/50 p-4 space-y-2">
              <p className="text-xs font-semibold uppercase text-slate-400">Informazioni barca</p>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <p className="text-xs text-slate-400">Nome</p>
                  <p className="text-sm font-medium text-slate-700">{booking.boat_name}</p>
                </div>
                <div>
                  <p className="text-xs text-slate-400">Lunghezza</p>
                  <p className="text-sm font-medium text-slate-700">{booking.boat_length} m</p>
                </div>
              </div>
              {booking.sharing && (
                <Badge variant="info">Condivisione attiva</Badge>
              )}
              {booking.notes && (
                <div>
                  <p className="text-xs text-slate-400">Note</p>
                  <p className="text-sm text-slate-600">{booking.notes}</p>
                </div>
              )}
            </div>
          </div>
        </div>

        {/* Berth info card */}
        <div className="rounded-xl border border-sky-100 bg-white p-6 shadow-sm space-y-5">
          <h2 className="text-lg font-semibold text-sky-900">Posto barca</h2>

          <div className="space-y-4">
            <div>
              <h3 className="text-base font-semibold text-slate-800">{booking.berth.name}</h3>
              <div className="mt-1 flex items-center gap-1.5 text-sm text-slate-500">
                <MapPinIcon className="h-4 w-4" />
                {booking.berth.port.name}, {booking.berth.port.city}
              </div>
            </div>

            <AnchorRating
              count={booking.berth.anchor_rating}
              level={booking.berth.anchor_level}
            />

            <div className="grid grid-cols-2 gap-4">
              <div>
                <p className="text-xs font-medium uppercase text-slate-400">Lunghezza max</p>
                <p className="text-sm font-semibold text-slate-700">{booking.berth.max_length} m</p>
              </div>
              <div>
                <p className="text-xs font-medium uppercase text-slate-400">Larghezza max</p>
                <p className="text-sm font-semibold text-slate-700">{booking.berth.max_beam} m</p>
              </div>
              <div>
                <p className="text-xs font-medium uppercase text-slate-400">Pescaggio max</p>
                <p className="text-sm font-semibold text-slate-700">{booking.berth.max_draft} m</p>
              </div>
              <div>
                <p className="text-xs font-medium uppercase text-slate-400">Prezzo/notte</p>
                <p className="text-sm font-semibold text-slate-700">
                  &euro;{nightlyPrice.toFixed(2)}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Actions */}
      <div className="flex flex-wrap gap-3">
        {canCancel && (
          <Button variant="danger" onClick={handleCancel} loading={cancelling}>
            <XCircleIcon className="h-4 w-4" />
            Cancella prenotazione
          </Button>
        )}
        {canReview && (
          <Link href={`/guest/bookings/${booking.id}/review`}>
            <Button>
              <StarIcon className="h-4 w-4" />
              Lascia una recensione
            </Button>
          </Link>
        )}
      </div>
    </div>
  );
}
