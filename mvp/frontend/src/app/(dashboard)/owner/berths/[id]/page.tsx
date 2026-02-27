'use client';

import { useEffect, useState, useCallback } from 'react';
import { useParams } from 'next/navigation';
import Link from 'next/link';
import { useUIStore } from '@/stores/ui';
import Button from '@/components/common/Button';
import Badge from '@/components/common/Badge';
import AnchorRating from '@/components/domain/AnchorRating';
import ReviewStars from '@/components/domain/ReviewStars';
import * as ownerApi from '@/lib/api/owner';
import type { BerthDetail, Booking, Review } from '@/lib/api/types';
import {
  ArrowLeftIcon,
  PencilSquareIcon,
  CheckIcon,
  XMarkIcon,
  InformationCircleIcon,
  CalendarDaysIcon,
  StarIcon,
} from '@heroicons/react/24/outline';

const statusMap: Record<string, { label: string; variant: 'success' | 'warning' | 'danger' | 'info' }> = {
  pending: { label: 'In attesa', variant: 'warning' },
  confirmed: { label: 'Confermata', variant: 'success' },
  cancelled: { label: 'Cancellata', variant: 'danger' },
  completed: { label: 'Completata', variant: 'info' },
};

type Tab = 'info' | 'prenotazioni' | 'recensioni';

export default function OwnerBerthDetailPage() {
  const params = useParams();
  const berthId = Number(params.id);
  const { addToast } = useUIStore();

  const [berth, setBerth] = useState<BerthDetail | null>(null);
  const [bookings, setBookings] = useState<Booking[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<Tab>('info');
  const [actionLoading, setActionLoading] = useState<number | null>(null);
  const [bookingsPage, setBookingsPage] = useState(1);
  const [bookingsTotalPages, setBookingsTotalPages] = useState(1);

  const fetchBerth = useCallback(async () => {
    try {
      setLoading(true);
      const data = await ownerApi.getBerth(berthId);
      setBerth(data);
    } catch {
      addToast({ type: 'error', message: 'Errore nel caricamento del posto barca' });
    } finally {
      setLoading(false);
    }
  }, [berthId, addToast]);

  const fetchBookings = useCallback(async (page: number) => {
    try {
      const res = await ownerApi.getBerthBookings(berthId, page);
      setBookings(res.results);
      setBookingsTotalPages(Math.ceil(res.count / 10));
    } catch {
      addToast({ type: 'error', message: 'Errore nel caricamento delle prenotazioni' });
    }
  }, [berthId, addToast]);

  useEffect(() => {
    fetchBerth();
  }, [fetchBerth]);

  useEffect(() => {
    if (activeTab === 'prenotazioni') {
      fetchBookings(bookingsPage);
    }
  }, [activeTab, bookingsPage, fetchBookings]);

  const handleConfirm = async (bookingId: number) => {
    try {
      setActionLoading(bookingId);
      await ownerApi.confirmBooking(bookingId);
      addToast({ type: 'success', message: 'Prenotazione confermata' });
      fetchBookings(bookingsPage);
    } catch {
      addToast({ type: 'error', message: 'Errore nella conferma' });
    } finally {
      setActionLoading(null);
    }
  };

  const handleReject = async (bookingId: number) => {
    try {
      setActionLoading(bookingId);
      await ownerApi.rejectBooking(bookingId);
      addToast({ type: 'success', message: 'Prenotazione rifiutata' });
      fetchBookings(bookingsPage);
    } catch {
      addToast({ type: 'error', message: 'Errore nel rifiuto' });
    } finally {
      setActionLoading(null);
    }
  };

  const tabs: { key: Tab; label: string; icon: React.ComponentType<{ className?: string }> }[] = [
    { key: 'info', label: 'Info', icon: InformationCircleIcon },
    { key: 'prenotazioni', label: 'Prenotazioni', icon: CalendarDaysIcon },
    { key: 'recensioni', label: 'Recensioni', icon: StarIcon },
  ];

  if (loading) {
    return (
      <div className="space-y-6">
        <div className="h-8 w-64 animate-pulse rounded-lg bg-sky-50" />
        <div className="h-96 animate-pulse rounded-xl bg-sky-50" />
      </div>
    );
  }

  if (!berth) {
    return (
      <div className="flex flex-col items-center justify-center py-16">
        <p className="text-slate-500">Posto barca non trovato</p>
        <Link href="/owner/berths" className="mt-4">
          <Button variant="secondary">Torna ai posti barca</Button>
        </Link>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div className="flex items-center gap-4">
          <Link
            href="/owner/berths"
            className="flex h-9 w-9 items-center justify-center rounded-lg border border-sky-200 bg-white text-slate-500 transition-colors hover:bg-sky-50 hover:text-sky-700"
          >
            <ArrowLeftIcon className="h-4 w-4" />
          </Link>
          <div>
            <h1 className="text-2xl font-bold text-sky-900">{berth.name}</h1>
            <p className="text-sm text-slate-500">{berth.port.name}, {berth.port.city}</p>
          </div>
        </div>
        <Link href={`/owner/berths/${berthId}/edit`}>
          <Button variant="secondary">
            <PencilSquareIcon className="h-4 w-4" />
            Modifica
          </Button>
        </Link>
      </div>

      {/* Tabs */}
      <div className="flex gap-1 rounded-xl border border-sky-100 bg-sky-50/50 p-1">
        {tabs.map((tab) => (
          <button
            key={tab.key}
            onClick={() => setActiveTab(tab.key)}
            className={`flex flex-1 items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium transition-all ${
              activeTab === tab.key
                ? 'bg-white text-sky-800 shadow-sm'
                : 'text-slate-500 hover:text-sky-700'
            }`}
          >
            <tab.icon className="h-4 w-4" />
            {tab.label}
          </button>
        ))}
      </div>

      {/* Tab Content */}
      {activeTab === 'info' && (
        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
          {/* Details card */}
          <div className="rounded-xl border border-sky-100 bg-white p-6 shadow-sm space-y-4">
            <h2 className="text-lg font-semibold text-sky-900">Dettagli</h2>

            <div className="flex items-center gap-3">
              <Badge variant={berth.is_available ? 'success' : 'danger'}>
                {berth.is_available ? 'Disponibile' : 'Non disponibile'}
              </Badge>
              {berth.sharing_available && <Badge variant="info">Condivisione attiva</Badge>}
            </div>

            {berth.description && (
              <p className="text-sm text-slate-600 leading-relaxed">{berth.description}</p>
            )}

            <div className="grid grid-cols-2 gap-4">
              <div>
                <p className="text-xs font-medium uppercase text-slate-400">Lunghezza max</p>
                <p className="text-sm font-semibold text-slate-700">{berth.max_length} m</p>
              </div>
              <div>
                <p className="text-xs font-medium uppercase text-slate-400">Larghezza max</p>
                <p className="text-sm font-semibold text-slate-700">{berth.max_beam} m</p>
              </div>
              <div>
                <p className="text-xs font-medium uppercase text-slate-400">Pescaggio max</p>
                <p className="text-sm font-semibold text-slate-700">{berth.max_draft} m</p>
              </div>
              <div>
                <p className="text-xs font-medium uppercase text-slate-400">Prezzo/notte</p>
                <p className="text-sm font-semibold text-slate-700">&euro;{berth.price_per_night.toFixed(2)}</p>
              </div>
            </div>
          </div>

          {/* Rating card */}
          <div className="rounded-xl border border-sky-100 bg-white p-6 shadow-sm space-y-4">
            <h2 className="text-lg font-semibold text-sky-900">Valutazione</h2>

            <div className="flex items-center gap-4">
              <AnchorRating count={berth.anchor_rating} level={berth.anchor_level} size="lg" />
              <div>
                <p className="text-2xl font-bold text-sky-900">{berth.anchor_rating}/5</p>
                <p className="text-xs text-slate-400 capitalize">Livello {berth.anchor_level}</p>
              </div>
            </div>

            <div className="rounded-lg bg-sky-50/50 p-4">
              <div className="flex items-center justify-between">
                <p className="text-sm text-slate-600">Media recensioni</p>
                <div className="flex items-center gap-2">
                  <ReviewStars value={Math.round(berth.average_rating)} readonly size="sm" />
                  <span className="text-sm font-medium text-slate-700">
                    {berth.average_rating.toFixed(1)}
                  </span>
                </div>
              </div>
              <p className="mt-1 text-xs text-slate-400">{berth.review_count} recensioni totali</p>
            </div>

            <Link href={`/owner/berths/${berthId}/assessment`}>
              <Button variant="secondary" className="w-full">
                Autovalutazione
              </Button>
            </Link>
          </div>
        </div>
      )}

      {activeTab === 'prenotazioni' && (
        <div className="rounded-xl border border-sky-100 bg-white shadow-sm">
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="border-b border-sky-100 bg-sky-50/50">
                <tr>
                  <th className="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Ospite</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Date</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Barca</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Prezzo</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Stato</th>
                  <th className="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Azioni</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-sky-50">
                {bookings.length === 0 ? (
                  <tr>
                    <td colSpan={6} className="px-4 py-8 text-center text-slate-400">
                      Nessuna prenotazione
                    </td>
                  </tr>
                ) : (
                  bookings.map((booking) => {
                    const status = statusMap[booking.status];
                    return (
                      <tr key={booking.id} className="hover:bg-sky-50/30 transition-colors">
                        <td className="px-4 py-3 text-slate-700">
                          {booking.guest.first_name} {booking.guest.last_name}
                        </td>
                        <td className="px-4 py-3 text-slate-500">
                          {new Date(booking.check_in).toLocaleDateString('it-IT')} -{' '}
                          {new Date(booking.check_out).toLocaleDateString('it-IT')}
                        </td>
                        <td className="px-4 py-3 text-slate-500">
                          {booking.boat_name} ({booking.boat_length}m)
                        </td>
                        <td className="px-4 py-3 font-medium text-slate-700">
                          &euro;{booking.total_price.toFixed(2)}
                        </td>
                        <td className="px-4 py-3">
                          <Badge variant={status.variant}>{status.label}</Badge>
                        </td>
                        <td className="px-4 py-3">
                          {booking.status === 'pending' && (
                            <div className="flex items-center gap-1">
                              <button
                                onClick={() => handleConfirm(booking.id)}
                                disabled={actionLoading === booking.id}
                                className="flex h-7 w-7 items-center justify-center rounded-md bg-emerald-50 text-emerald-600 transition-colors hover:bg-emerald-100 disabled:opacity-50"
                                title="Conferma"
                              >
                                <CheckIcon className="h-4 w-4" />
                              </button>
                              <button
                                onClick={() => handleReject(booking.id)}
                                disabled={actionLoading === booking.id}
                                className="flex h-7 w-7 items-center justify-center rounded-md bg-red-50 text-red-600 transition-colors hover:bg-red-100 disabled:opacity-50"
                                title="Rifiuta"
                              >
                                <XMarkIcon className="h-4 w-4" />
                              </button>
                            </div>
                          )}
                        </td>
                      </tr>
                    );
                  })
                )}
              </tbody>
            </table>
          </div>

          {/* Pagination */}
          {bookingsTotalPages > 1 && (
            <div className="flex items-center justify-between border-t border-sky-100 px-4 py-3">
              <p className="text-xs text-slate-500">
                Pagina {bookingsPage} di {bookingsTotalPages}
              </p>
              <div className="flex gap-1">
                <Button
                  variant="ghost"
                  size="sm"
                  disabled={bookingsPage <= 1}
                  onClick={() => setBookingsPage((p) => p - 1)}
                >
                  Precedente
                </Button>
                <Button
                  variant="ghost"
                  size="sm"
                  disabled={bookingsPage >= bookingsTotalPages}
                  onClick={() => setBookingsPage((p) => p + 1)}
                >
                  Successiva
                </Button>
              </div>
            </div>
          )}
        </div>
      )}

      {activeTab === 'recensioni' && (
        <div className="space-y-4">
          {berth.reviews.length === 0 ? (
            <div className="flex flex-col items-center justify-center rounded-xl border border-sky-100 bg-white py-12 shadow-sm">
              <StarIcon className="h-10 w-10 text-slate-300 mb-3" />
              <p className="text-slate-500">Nessuna recensione ancora</p>
            </div>
          ) : (
            berth.reviews.map((review: Review) => (
              <div
                key={review.id}
                className="rounded-xl border border-sky-100 bg-white p-5 shadow-sm"
              >
                <div className="flex items-start justify-between">
                  <div className="flex items-center gap-3">
                    <div className="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-cyan-500 text-white text-sm font-bold">
                      {(review.guest.first_name?.charAt(0) ?? '').toUpperCase()}
                      {(review.guest.last_name?.charAt(0) ?? '').toUpperCase()}
                    </div>
                    <div>
                      <p className="text-sm font-semibold text-slate-800">
                        {review.guest.first_name} {review.guest.last_name}
                      </p>
                      <p className="text-xs text-slate-400">
                        {new Date(review.created_at).toLocaleDateString('it-IT')}
                      </p>
                    </div>
                  </div>
                  <ReviewStars value={review.rating} readonly size="sm" />
                </div>
                {review.comment && (
                  <p className="mt-3 text-sm text-slate-600 leading-relaxed">{review.comment}</p>
                )}
              </div>
            ))
          )}
        </div>
      )}
    </div>
  );
}
