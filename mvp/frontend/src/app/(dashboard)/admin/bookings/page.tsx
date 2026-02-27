'use client';

import { useEffect, useState, useCallback } from 'react';
import { useUIStore } from '@/stores/ui';
import Badge from '@/components/common/Badge';
import DataTable, { Column } from '@/components/common/DataTable';
import * as adminApi from '@/lib/api/admin';
import type { Booking, PaginatedResponse } from '@/lib/api/types';
import { FunnelIcon } from '@heroicons/react/24/outline';

const statusMap: Record<string, { label: string; variant: 'success' | 'warning' | 'danger' | 'info' }> = {
  pending: { label: 'In attesa', variant: 'warning' },
  confirmed: { label: 'Confermata', variant: 'success' },
  cancelled: { label: 'Cancellata', variant: 'danger' },
  completed: { label: 'Completata', variant: 'info' },
};

const modeLabel: Record<string, string> = {
  exclusive: 'Esclusivo',
  sharing: 'Condiviso',
};

const statusFilters = [
  { value: '', label: 'Tutti' },
  { value: 'pending', label: 'In attesa' },
  { value: 'confirmed', label: 'Confermata' },
  { value: 'completed', label: 'Completata' },
  { value: 'cancelled', label: 'Cancellata' },
];

const PAGE_SIZE = 15;

export default function AdminBookingsPage() {
  const { addToast } = useUIStore();
  const [bookings, setBookings] = useState<Booking[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [statusFilter, setStatusFilter] = useState('');

  const fetchBookings = useCallback(async () => {
    try {
      setLoading(true);
      const res: PaginatedResponse<Booking> = await adminApi.getBookings(page);
      setBookings(res.results);
      setTotalPages(Math.ceil(res.count / PAGE_SIZE));
    } catch {
      addToast({ type: 'error', message: 'Errore nel caricamento delle prenotazioni' });
    } finally {
      setLoading(false);
    }
  }, [page, addToast]);

  useEffect(() => {
    fetchBookings();
  }, [fetchBookings]);

  const filteredBookings = statusFilter
    ? bookings.filter((b) => b.status === statusFilter)
    : bookings;

  const columns: Column<Booking & Record<string, unknown>>[] = [
    {
      key: 'berth',
      header: 'Posto barca',
      render: (b) => (
        <div>
          <span className="font-medium text-slate-800">{b.berth?.name ?? '-'}</span>
          {b.berth?.port && (
            <p className="text-xs text-slate-400">{b.berth.port.name}</p>
          )}
        </div>
      ),
    },
    {
      key: 'guest',
      header: 'Ospite',
      render: (b) => (
        <span className="text-slate-600">
          {b.guest?.first_name} {b.guest?.last_name}
        </span>
      ),
    },
    {
      key: 'dates',
      header: 'Date',
      render: (b) => (
        <span className="text-slate-500">
          {new Date(b.check_in).toLocaleDateString('it-IT')} -{' '}
          {new Date(b.check_out).toLocaleDateString('it-IT')}
        </span>
      ),
    },
    {
      key: 'total_price',
      header: 'Prezzo',
      render: (b) => (
        <span className="font-medium text-slate-700">
          &euro;{b.total_price.toLocaleString('it-IT', { minimumFractionDigits: 2 })}
        </span>
      ),
    },
    {
      key: 'status',
      header: 'Stato',
      render: (b) => {
        const s = statusMap[b.status] ?? { label: b.status, variant: 'info' as const };
        return <Badge variant={s.variant}>{s.label}</Badge>;
      },
    },
    {
      key: 'sharing',
      header: 'Modalita',
      render: (b) => (
        <span className="text-xs text-slate-500">
          {b.sharing ? modeLabel.sharing : modeLabel.exclusive}
        </span>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl font-bold text-sky-900">Gestione Prenotazioni</h1>
        <p className="mt-1 text-sm text-slate-500">
          Visualizza tutte le prenotazioni della piattaforma.
        </p>
      </div>

      {/* Status filter */}
      <div className="flex items-center gap-3">
        <FunnelIcon className="h-4 w-4 text-slate-400" />
        <div className="flex flex-wrap gap-2">
          {statusFilters.map((sf) => (
            <button
              key={sf.value}
              onClick={() => {
                setStatusFilter(sf.value);
                setPage(1);
              }}
              className={`rounded-full px-3.5 py-1.5 text-xs font-medium transition-all ${
                statusFilter === sf.value
                  ? 'bg-sky-600 text-white shadow-sm shadow-sky-600/20'
                  : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:bg-sky-50 hover:text-sky-700'
              }`}
            >
              {sf.label}
            </button>
          ))}
        </div>
      </div>

      {/* Table */}
      <DataTable
        columns={columns}
        data={filteredBookings as (Booking & Record<string, unknown>)[]}
        loading={loading}
        page={page}
        totalPages={totalPages}
        onPageChange={setPage}
        emptyMessage="Nessuna prenotazione trovata"
      />
    </div>
  );
}
