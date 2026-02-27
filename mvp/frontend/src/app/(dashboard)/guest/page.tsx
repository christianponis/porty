'use client';

import { useEffect, useState, useCallback } from 'react';
import Link from 'next/link';
import { useAuthStore } from '@/stores/auth';
import { useUIStore } from '@/stores/ui';
import StatsCard from '@/components/common/StatsCard';
import Badge from '@/components/common/Badge';
import Button from '@/components/common/Button';
import NodiBadge from '@/components/domain/NodiBadge';
import * as guestApi from '@/lib/api/guest';
import * as walletApi from '@/lib/api/wallet';
import type { Booking, Wallet, GuestDashboard } from '@/lib/api/types';
import {
  CalendarDaysIcon,
  PaperAirplaneIcon,
  WalletIcon,
  StarIcon,
} from '@heroicons/react/24/outline';

const statusMap: Record<string, { label: string; variant: 'success' | 'warning' | 'danger' | 'info' }> = {
  pending: { label: 'In attesa', variant: 'warning' },
  confirmed: { label: 'Confermata', variant: 'success' },
  cancelled: { label: 'Cancellata', variant: 'danger' },
  completed: { label: 'Completata', variant: 'info' },
};

export default function GuestDashboardPage() {
  const { user } = useAuthStore();
  const { addToast } = useUIStore();
  const [dashboard, setDashboard] = useState<GuestDashboard | null>(null);
  const [wallet, setWallet] = useState<Wallet | null>(null);
  const [loading, setLoading] = useState(true);

  const fetchData = useCallback(async () => {
    try {
      setLoading(true);
      const [dashData, walletData] = await Promise.all([
        guestApi.getDashboard(),
        walletApi.getWallet(),
      ]);
      setDashboard(dashData);
      setWallet(walletData);
    } catch {
      addToast({ type: 'error', message: 'Errore nel caricamento dei dati' });
    } finally {
      setLoading(false);
    }
  }, [addToast]);

  useEffect(() => {
    fetchData();
  }, [fetchData]);

  const upcomingBookings = (dashboard?.upcoming_bookings || [])
    .filter((b) => b.status === 'confirmed')
    .sort((a, b) => new Date(a.check_in).getTime() - new Date(b.check_in).getTime());

  const completedBookings = (dashboard?.past_bookings || [])
    .filter((b) => b.status === 'completed');

  if (loading) {
    return (
      <div className="space-y-6">
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {[...Array(3)].map((_, i) => (
            <div key={i} className="h-24 animate-pulse rounded-xl bg-sky-50" />
          ))}
        </div>
        <div className="h-64 animate-pulse rounded-xl bg-sky-50" />
      </div>
    );
  }

  return (
    <div className="space-y-8">
      {/* Header */}
      <div>
        <h1 className="text-2xl font-bold text-sky-900">
          Benvenuto, {user?.first_name}!
        </h1>
        <p className="mt-1 text-sm text-slate-500">
          Ecco un riepilogo delle tue prenotazioni e del tuo saldo Nodi
        </p>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <StatsCard
          title="Prenotazioni totali"
          value={dashboard?.total_bookings ?? 0}
          icon={<CalendarDaysIcon className="h-5 w-5" />}
          color="sky"
        />
        <StatsCard
          title="Prossime partenze"
          value={upcomingBookings.length}
          icon={<PaperAirplaneIcon className="h-5 w-5" />}
          color="cyan"
        />
        <StatsCard
          title="Saldo Nodi"
          value={wallet?.nodi_balance ?? 0}
          icon={<WalletIcon className="h-5 w-5" />}
          color="emerald"
        />
      </div>

      {/* Upcoming bookings */}
      <div className="rounded-xl border border-sky-100 bg-white shadow-sm">
        <div className="flex items-center justify-between border-b border-sky-100 px-6 py-4">
          <h2 className="text-lg font-semibold text-sky-900">Prossime partenze</h2>
          <Link href="/guest/bookings">
            <Button variant="ghost" size="sm">Vedi tutte</Button>
          </Link>
        </div>
        {upcomingBookings.length === 0 ? (
          <div className="px-6 py-12 text-center text-slate-400">
            <PaperAirplaneIcon className="mx-auto h-10 w-10 text-slate-300 mb-3" />
            <p>Nessuna partenza in programma</p>
            <Link href="/search" className="mt-3 inline-block">
              <Button variant="secondary" size="sm">Cerca un posto barca</Button>
            </Link>
          </div>
        ) : (
          <div className="divide-y divide-sky-50">
            {upcomingBookings.slice(0, 5).map((booking) => (
              <Link
                key={booking.id}
                href={`/guest/bookings/${booking.id}`}
                className="flex items-center justify-between px-6 py-4 transition-colors hover:bg-sky-50/30"
              >
                <div className="flex items-center gap-4">
                  <div className="flex h-12 w-12 flex-col items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                    <span className="text-xs font-medium">
                      {new Date(booking.check_in).toLocaleDateString('it-IT', { month: 'short' })}
                    </span>
                    <span className="text-lg font-bold leading-tight">
                      {new Date(booking.check_in).getDate()}
                    </span>
                  </div>
                  <div>
                    <p className="text-sm font-semibold text-slate-800">{booking.berth.name}</p>
                    <p className="text-xs text-slate-500">
                      {booking.berth.port.name} &middot;{' '}
                      {new Date(booking.check_in).toLocaleDateString('it-IT')} -{' '}
                      {new Date(booking.check_out).toLocaleDateString('it-IT')}
                    </p>
                  </div>
                </div>
                <div className="text-right">
                  <p className="text-sm font-semibold text-slate-700">
                    &euro;{booking.total_price.toFixed(2)}
                  </p>
                  <Badge variant="success">Confermata</Badge>
                </div>
              </Link>
            ))}
          </div>
        )}
      </div>

      {/* Recent completed - review prompt */}
      <div className="rounded-xl border border-sky-100 bg-white shadow-sm">
        <div className="border-b border-sky-100 px-6 py-4">
          <h2 className="text-lg font-semibold text-sky-900">Prenotazioni completate</h2>
        </div>
        {completedBookings.length === 0 ? (
          <div className="px-6 py-12 text-center text-slate-400">
            <StarIcon className="mx-auto h-10 w-10 text-slate-300 mb-3" />
            <p>Nessuna prenotazione completata</p>
          </div>
        ) : (
          <div className="divide-y divide-sky-50">
            {completedBookings.slice(0, 5).map((booking) => (
              <div
                key={booking.id}
                className="flex items-center justify-between px-6 py-4"
              >
                <div>
                  <p className="text-sm font-semibold text-slate-800">{booking.berth.name}</p>
                  <p className="text-xs text-slate-500">
                    {booking.berth.port.name} &middot;{' '}
                    {new Date(booking.check_in).toLocaleDateString('it-IT')} -{' '}
                    {new Date(booking.check_out).toLocaleDateString('it-IT')}
                  </p>
                </div>
                <div className="flex items-center gap-3">
                  <Badge variant="info">Completata</Badge>
                  <Link href={`/guest/bookings/${booking.id}/review`}>
                    <Button variant="secondary" size="sm">
                      <StarIcon className="h-3.5 w-3.5" />
                      Recensisci
                    </Button>
                  </Link>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
