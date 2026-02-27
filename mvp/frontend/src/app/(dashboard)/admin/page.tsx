'use client';

import { useEffect, useState, useCallback } from 'react';
import { useAuthStore } from '@/stores/auth';
import { useUIStore } from '@/stores/ui';
import StatsCard from '@/components/common/StatsCard';
import Badge from '@/components/common/Badge';
import * as adminApi from '@/lib/api/admin';
import type { AdminDashboard, User, Booking } from '@/lib/api/types';
import {
  UsersIcon,
  BuildingOffice2Icon,
  Squares2X2Icon,
  CalendarDaysIcon,
  StarIcon,
  BanknotesIcon,
} from '@heroicons/react/24/outline';

const statusMap: Record<string, { label: string; variant: 'success' | 'warning' | 'danger' | 'info' }> = {
  pending: { label: 'In attesa', variant: 'warning' },
  confirmed: { label: 'Confermata', variant: 'success' },
  cancelled: { label: 'Cancellata', variant: 'danger' },
  completed: { label: 'Completata', variant: 'info' },
};

const roleMap: Record<string, { label: string; variant: 'success' | 'warning' | 'danger' | 'info' }> = {
  admin: { label: 'Admin', variant: 'info' },
  owner: { label: 'Armatore', variant: 'warning' },
  guest: { label: 'Ospite', variant: 'success' },
};

export default function AdminDashboardPage() {
  const { user } = useAuthStore();
  const { addToast } = useUIStore();
  const [data, setData] = useState<AdminDashboard | null>(null);
  const [loading, setLoading] = useState(true);

  const fetchData = useCallback(async () => {
    try {
      setLoading(true);
      const dashboard = await adminApi.getDashboard();
      setData(dashboard);
    } catch {
      addToast({ type: 'error', message: 'Errore nel caricamento della dashboard' });
    } finally {
      setLoading(false);
    }
  }, [addToast]);

  useEffect(() => {
    fetchData();
  }, [fetchData]);

  if (loading) {
    return (
      <div className="space-y-6">
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {[...Array(6)].map((_, i) => (
            <div key={i} className="h-24 animate-pulse rounded-xl bg-sky-50" />
          ))}
        </div>
        <div className="h-64 animate-pulse rounded-xl bg-sky-50" />
        <div className="h-64 animate-pulse rounded-xl bg-sky-50" />
      </div>
    );
  }

  const stats = data?.stats;

  return (
    <div className="space-y-8">
      {/* Header */}
      <div>
        <h1 className="text-2xl font-bold text-sky-900">
          Pannello di Amministrazione
        </h1>
        <p className="mt-1 text-sm text-slate-500">
          Benvenuto, {user?.first_name}. Ecco la panoramica della piattaforma.
        </p>
      </div>

      {/* Stats Grid - 2x3 */}
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <StatsCard
          title="Utenti totali"
          value={stats?.total_users ?? 0}
          icon={<UsersIcon className="h-5 w-5" />}
          color="sky"
        />
        <StatsCard
          title="Porti"
          value={stats?.total_ports ?? 0}
          icon={<BuildingOffice2Icon className="h-5 w-5" />}
          color="cyan"
        />
        <StatsCard
          title="Posti barca attivi"
          value={stats?.total_berths ?? 0}
          icon={<Squares2X2Icon className="h-5 w-5" />}
          color="emerald"
        />
        <StatsCard
          title="Prenotazioni"
          value={stats?.total_bookings ?? 0}
          icon={<CalendarDaysIcon className="h-5 w-5" />}
          color="amber"
        />
        <StatsCard
          title="Recensioni"
          value={stats?.total_revenue ?? 0}
          icon={<StarIcon className="h-5 w-5" />}
          color="sky"
        />
        <StatsCard
          title="Transazioni"
          value={stats?.total_nodi_issued ?? 0}
          icon={<BanknotesIcon className="h-5 w-5" />}
          color="cyan"
        />
      </div>

      {/* Recent Users */}
      <div className="rounded-xl border border-sky-100 bg-white shadow-sm">
        <div className="border-b border-sky-100 px-6 py-4">
          <h2 className="text-lg font-semibold text-sky-900">Utenti recenti</h2>
        </div>
        {!data?.recent_users?.length ? (
          <div className="px-6 py-12 text-center text-slate-400">
            <UsersIcon className="mx-auto mb-3 h-10 w-10 text-slate-300" />
            <p>Nessun utente registrato</p>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-sky-50/50 text-xs uppercase text-slate-500">
                <tr>
                  <th className="px-4 py-3 text-left font-medium">Nome</th>
                  <th className="px-4 py-3 text-left font-medium">Email</th>
                  <th className="px-4 py-3 text-left font-medium">Ruolo</th>
                  <th className="px-4 py-3 text-left font-medium">Registrato</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-sky-50">
                {data.recent_users.slice(0, 5).map((u: User) => {
                  const role = roleMap[u.role] ?? { label: u.role, variant: 'info' as const };
                  return (
                    <tr key={u.id} className="transition-colors hover:bg-sky-50/30">
                      <td className="px-4 py-3 font-medium text-slate-700">
                        {u.first_name} {u.last_name}
                      </td>
                      <td className="px-4 py-3 text-slate-500">{u.email}</td>
                      <td className="px-4 py-3">
                        <Badge variant={role.variant}>{role.label}</Badge>
                      </td>
                      <td className="px-4 py-3 text-slate-500">
                        {new Date(u.created_at).toLocaleDateString('it-IT')}
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {/* Recent Bookings */}
      <div className="rounded-xl border border-sky-100 bg-white shadow-sm">
        <div className="border-b border-sky-100 px-6 py-4">
          <h2 className="text-lg font-semibold text-sky-900">Prenotazioni recenti</h2>
        </div>
        {!data?.recent_bookings?.length ? (
          <div className="px-6 py-12 text-center text-slate-400">
            <CalendarDaysIcon className="mx-auto mb-3 h-10 w-10 text-slate-300" />
            <p>Nessuna prenotazione</p>
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-sky-50/50 text-xs uppercase text-slate-500">
                <tr>
                  <th className="px-4 py-3 text-left font-medium">Posto barca</th>
                  <th className="px-4 py-3 text-left font-medium">Ospite</th>
                  <th className="px-4 py-3 text-left font-medium">Date</th>
                  <th className="px-4 py-3 text-left font-medium">Stato</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-sky-50">
                {data.recent_bookings.slice(0, 5).map((b: Booking) => {
                  const status = statusMap[b.status] ?? { label: b.status, variant: 'info' as const };
                  return (
                    <tr key={b.id} className="transition-colors hover:bg-sky-50/30">
                      <td className="px-4 py-3 font-medium text-slate-700">
                        {b.berth?.name ?? `#${b.id}`}
                      </td>
                      <td className="px-4 py-3 text-slate-500">
                        {b.guest?.first_name} {b.guest?.last_name}
                      </td>
                      <td className="px-4 py-3 text-slate-500">
                        {new Date(b.check_in).toLocaleDateString('it-IT')} -{' '}
                        {new Date(b.check_out).toLocaleDateString('it-IT')}
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
