'use client';

import { useEffect, useState, useCallback } from 'react';
import Link from 'next/link';
import { useAuthStore } from '@/stores/auth';
import { useUIStore } from '@/stores/ui';
import StatsCard from '@/components/common/StatsCard';
import Badge from '@/components/common/Badge';
import Button from '@/components/common/Button';
import NodiBadge from '@/components/domain/NodiBadge';
import * as ownerApi from '@/lib/api/owner';
import * as walletApi from '@/lib/api/wallet';
import type { Berth, Booking, Wallet } from '@/lib/api/types';
import {
  Squares2X2Icon,
  CalendarDaysIcon,
  BanknotesIcon,
  CurrencyEuroIcon,
} from '@heroicons/react/24/outline';

const statusMap: Record<string, { label: string; variant: 'success' | 'warning' | 'danger' | 'info' }> = {
  pending: { label: 'In attesa', variant: 'warning' },
  confirmed: { label: 'Confermata', variant: 'success' },
  cancelled: { label: 'Cancellata', variant: 'danger' },
  completed: { label: 'Completata', variant: 'info' },
};

export default function OwnerDashboard() {
  const { user } = useAuthStore();
  const { addToast } = useUIStore();
  const [berths, setBerths] = useState<Berth[]>([]);
  const [bookings, setBookings] = useState<Booking[]>([]);
  const [wallet, setWallet] = useState<Wallet | null>(null);
  const [loading, setLoading] = useState(true);

  const fetchData = useCallback(async () => {
    try {
      setLoading(true);
      const [berthsData, walletData] = await Promise.all([
        ownerApi.getMyBerths(),
        walletApi.getWallet(),
      ]);
      setBerths(berthsData);
      setWallet(walletData);

      // Fetch bookings from first berth (simplified - in production aggregate all)
      if (berthsData.length > 0) {
        const allBookings: Booking[] = [];
        for (const berth of berthsData.slice(0, 5)) {
          try {
            const res = await ownerApi.getBerthBookings(berth.id);
            allBookings.push(...res.results);
          } catch {
            // skip
          }
        }
        allBookings.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime());
        setBookings(allBookings.slice(0, 5));
      }
    } catch {
      addToast({ type: 'error', message: 'Errore nel caricamento dei dati' });
    } finally {
      setLoading(false);
    }
  }, [addToast]);

  useEffect(() => {
    fetchData();
  }, [fetchData]);

  const activeBookings = bookings.filter((b) => b.status === 'confirmed' || b.status === 'pending');
  const totalRevenue = bookings
    .filter((b) => b.status === 'completed' || b.status === 'confirmed')
    .reduce((sum, b) => sum + b.total_price, 0);

  if (loading) {
    return (
      <div className="space-y-6">
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
          {[...Array(4)].map((_, i) => (
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
          Ecco un riepilogo della tua attivita
        </p>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <StatsCard
          title="Posti barca totali"
          value={berths.length}
          icon={<Squares2X2Icon className="h-5 w-5" />}
          color="sky"
        />
        <StatsCard
          title="Prenotazioni attive"
          value={activeBookings.length}
          icon={<CalendarDaysIcon className="h-5 w-5" />}
          color="cyan"
        />
        <StatsCard
          title="Guadagni totali (EUR)"
          value={`\u20AC${totalRevenue.toLocaleString('it-IT', { minimumFractionDigits: 2 })}`}
          icon={<CurrencyEuroIcon className="h-5 w-5" />}
          color="emerald"
        />
        <StatsCard
          title="Saldo Nodi"
          value={wallet?.nodi_balance ?? 0}
          icon={<BanknotesIcon className="h-5 w-5" />}
          color="amber"
        />
      </div>

      {/* Quick actions */}
      <div className="flex flex-wrap gap-3">
        <Link href="/owner/berths/create">
          <Button>Aggiungi posto barca</Button>
        </Link>
        <Link href="/owner/berths">
          <Button variant="secondary">Vedi prenotazioni</Button>
        </Link>
      </div>

      {/* Recent bookings */}
      <div className="rounded-xl border border-sky-100 bg-white shadow-sm">
        <div className="border-b border-sky-100 px-6 py-4">
          <h2 className="text-lg font-semibold text-sky-900">Prenotazioni recenti</h2>
        </div>
        {bookings.length === 0 ? (
          <div className="px-6 py-12 text-center text-slate-400">
            <CalendarDaysIcon className="mx-auto h-10 w-10 text-slate-300 mb-3" />
            <p>Nessuna prenotazione ancora</p>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-sky-50/50 text-xs uppercase text-slate-500">
                <tr>
                  <th className="px-4 py-3 text-left font-medium">Ospite</th>
                  <th className="px-4 py-3 text-left font-medium">Posto barca</th>
                  <th className="px-4 py-3 text-left font-medium">Date</th>
                  <th className="px-4 py-3 text-left font-medium">Prezzo</th>
                  <th className="px-4 py-3 text-left font-medium">Stato</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-sky-50">
                {bookings.map((booking) => {
                  const status = statusMap[booking.status];
                  return (
                    <tr key={booking.id} className="hover:bg-sky-50/30 transition-colors">
                      <td className="px-4 py-3 text-slate-700">
                        {booking.guest.first_name} {booking.guest.last_name}
                      </td>
                      <td className="px-4 py-3 text-slate-700">{booking.berth.name}</td>
                      <td className="px-4 py-3 text-slate-500">
                        {new Date(booking.check_in).toLocaleDateString('it-IT')} -{' '}
                        {new Date(booking.check_out).toLocaleDateString('it-IT')}
                      </td>
                      <td className="px-4 py-3 font-medium text-slate-700">
                        &euro;{booking.total_price.toFixed(2)}
                      </td>
                      <td className="px-4 py-3">
                        <Badge variant={status.variant}>{status.label}</Badge>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}
