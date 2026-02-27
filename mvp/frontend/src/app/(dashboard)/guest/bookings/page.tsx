'use client';

import { useEffect, useState, useCallback } from 'react';
import Link from 'next/link';
import { useUIStore } from '@/stores/ui';
import Badge from '@/components/common/Badge';
import DataTable, { Column } from '@/components/common/DataTable';
import * as guestApi from '@/lib/api/guest';
import type { Booking } from '@/lib/api/types';
import { EyeIcon } from '@heroicons/react/24/outline';

const statusMap: Record<string, { label: string; variant: 'success' | 'warning' | 'danger' | 'info' }> = {
  pending: { label: 'In attesa', variant: 'warning' },
  confirmed: { label: 'Confermata', variant: 'success' },
  cancelled: { label: 'Cancellata', variant: 'danger' },
  completed: { label: 'Completata', variant: 'info' },
};

const statusOptions = [
  { value: '', label: 'Tutti gli stati' },
  { value: 'pending', label: 'In attesa' },
  { value: 'confirmed', label: 'Confermata' },
  { value: 'cancelled', label: 'Cancellata' },
  { value: 'completed', label: 'Completata' },
];

export default function GuestBookingsPage() {
  const { addToast } = useUIStore();
  const [bookings, setBookings] = useState<Booking[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [statusFilter, setStatusFilter] = useState('');

  const fetchBookings = useCallback(async () => {
    try {
      setLoading(true);
      const res = await guestApi.getMyBookings(page);
      setBookings(res.results);
      setTotalPages(Math.ceil(res.count / 10));
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

  const columns: Column<Record<string, unknown>>[] = [
    {
      key: 'berth_name',
      header: 'Posto barca',
      render: (item) => {
        const booking = item as unknown as Booking;
        return (
          <span className="font-medium text-slate-800">{booking.berth.name}</span>
        );
      },
    },
    {
      key: 'port',
      header: 'Porto',
      render: (item) => {
        const booking = item as unknown as Booking;
        return (
          <span className="text-slate-500">
            {booking.berth.port.name}, {booking.berth.port.city}
          </span>
        );
      },
    },
    {
      key: 'check_in',
      header: 'Data inizio',
      render: (item) => (
        <span className="text-slate-500">
          {new Date(item.check_in as string).toLocaleDateString('it-IT')}
        </span>
      ),
    },
    {
      key: 'check_out',
      header: 'Data fine',
      render: (item) => (
        <span className="text-slate-500">
          {new Date(item.check_out as string).toLocaleDateString('it-IT')}
        </span>
      ),
    },
    {
      key: 'total_price',
      header: 'Prezzo',
      render: (item) => (
        <span className="font-medium text-slate-700">
          &euro;{(item.total_price as number).toFixed(2)}
        </span>
      ),
    },
    {
      key: 'status',
      header: 'Stato',
      render: (item) => {
        const s = statusMap[item.status as string] || { label: item.status, variant: 'info' as const };
        return <Badge variant={s.variant}>{s.label}</Badge>;
      },
    },
    {
      key: 'actions',
      header: 'Azioni',
      render: (item) => (
        <Link href={`/guest/bookings/${item.id}`}>
          <button className="flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-sky-700 transition-colors hover:bg-sky-50">
            <EyeIcon className="h-3.5 w-3.5" />
            Dettaglio
          </button>
        </Link>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 className="text-2xl font-bold text-sky-900">Le mie prenotazioni</h1>
          <p className="mt-1 text-sm text-slate-500">
            Gestisci le tue prenotazioni e visualizza lo storico
          </p>
        </div>

        {/* Status filter */}
        <select
          value={statusFilter}
          onChange={(e) => setStatusFilter(e.target.value)}
          className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200"
        >
          {statusOptions.map((opt) => (
            <option key={opt.value} value={opt.value}>
              {opt.label}
            </option>
          ))}
        </select>
      </div>

      {/* Table */}
      <DataTable
        columns={columns}
        data={filteredBookings as unknown as Record<string, unknown>[]}
        loading={loading}
        page={page}
        totalPages={totalPages}
        onPageChange={setPage}
        emptyMessage="Nessuna prenotazione trovata"
      />
    </div>
  );
}
