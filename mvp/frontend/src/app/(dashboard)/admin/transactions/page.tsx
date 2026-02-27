'use client';

import { useEffect, useState, useCallback } from 'react';
import Link from 'next/link';
import { useUIStore } from '@/stores/ui';
import Badge from '@/components/common/Badge';
import DataTable, { Column } from '@/components/common/DataTable';
import * as adminApi from '@/lib/api/admin';
import type { Transaction, PaginatedResponse } from '@/lib/api/types';

const typeMap: Record<string, { label: string; variant: 'success' | 'warning' | 'danger' | 'info' }> = {
  earn: { label: 'Guadagno', variant: 'success' },
  spend: { label: 'Spesa', variant: 'warning' },
  refund: { label: 'Rimborso', variant: 'info' },
};

const PAGE_SIZE = 15;

export default function AdminTransactionsPage() {
  const { addToast } = useUIStore();
  const [transactions, setTransactions] = useState<Transaction[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

  const fetchTransactions = useCallback(async () => {
    try {
      setLoading(true);
      const res: PaginatedResponse<Transaction> = await adminApi.getTransactions(page);
      setTransactions(res.results);
      setTotalPages(Math.ceil(res.count / PAGE_SIZE));
    } catch {
      addToast({ type: 'error', message: 'Errore nel caricamento delle transazioni' });
    } finally {
      setLoading(false);
    }
  }, [page, addToast]);

  useEffect(() => {
    fetchTransactions();
  }, [fetchTransactions]);

  const columns: Column<Transaction & Record<string, unknown>>[] = [
    {
      key: 'created_at',
      header: 'Data',
      render: (t) => (
        <span className="text-slate-500">
          {new Date(t.created_at).toLocaleDateString('it-IT', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
          })}
          <span className="ml-1 text-xs text-slate-400">
            {new Date(t.created_at).toLocaleTimeString('it-IT', {
              hour: '2-digit',
              minute: '2-digit',
            })}
          </span>
        </span>
      ),
    },
    {
      key: 'type',
      header: 'Tipo',
      render: (t) => {
        const tm = typeMap[t.type] ?? { label: t.type, variant: 'info' as const };
        return <Badge variant={tm.variant}>{tm.label}</Badge>;
      },
    },
    {
      key: 'amount',
      header: 'Importo',
      render: (t) => {
        const isPositive = t.amount > 0;
        return (
          <span
            className={`font-semibold ${
              isPositive ? 'text-emerald-600' : 'text-red-500'
            }`}
          >
            {isPositive ? '+' : ''}
            {t.amount.toLocaleString('it-IT', { minimumFractionDigits: 2 })} Nodi
          </span>
        );
      },
    },
    {
      key: 'description',
      header: 'Descrizione',
      render: (t) => (
        <span className="text-slate-600">{t.description || '-'}</span>
      ),
    },
    {
      key: 'booking_id',
      header: 'Prenotazione',
      render: (t) =>
        t.booking_id ? (
          <Link
            href={`/admin/bookings`}
            className="font-medium text-sky-600 transition-colors hover:text-sky-800 hover:underline"
          >
            #{t.booking_id}
          </Link>
        ) : (
          <span className="text-slate-400">-</span>
        ),
    },
  ];

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl font-bold text-sky-900">Transazioni</h1>
        <p className="mt-1 text-sm text-slate-500">
          Storico di tutte le transazioni Nodi sulla piattaforma.
        </p>
      </div>

      {/* Summary cards */}
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div className="rounded-xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-white p-4 shadow-sm">
          <p className="text-xs font-medium uppercase tracking-wider text-emerald-600">Totale guadagni</p>
          <p className="mt-1 text-xl font-bold text-emerald-700">
            +{transactions
              .filter((t) => t.type === 'earn')
              .reduce((sum, t) => sum + t.amount, 0)
              .toLocaleString('it-IT', { minimumFractionDigits: 2 })}{' '}
            Nodi
          </p>
        </div>
        <div className="rounded-xl border border-amber-100 bg-gradient-to-br from-amber-50 to-white p-4 shadow-sm">
          <p className="text-xs font-medium uppercase tracking-wider text-amber-600">Totale spese</p>
          <p className="mt-1 text-xl font-bold text-amber-700">
            {transactions
              .filter((t) => t.type === 'spend')
              .reduce((sum, t) => sum + t.amount, 0)
              .toLocaleString('it-IT', { minimumFractionDigits: 2 })}{' '}
            Nodi
          </p>
        </div>
        <div className="rounded-xl border border-sky-100 bg-gradient-to-br from-sky-50 to-white p-4 shadow-sm">
          <p className="text-xs font-medium uppercase tracking-wider text-sky-600">Rimborsi</p>
          <p className="mt-1 text-xl font-bold text-sky-700">
            {transactions
              .filter((t) => t.type === 'refund')
              .reduce((sum, t) => sum + Math.abs(t.amount), 0)
              .toLocaleString('it-IT', { minimumFractionDigits: 2 })}{' '}
            Nodi
          </p>
        </div>
      </div>

      {/* Table */}
      <DataTable
        columns={columns}
        data={transactions as (Transaction & Record<string, unknown>)[]}
        loading={loading}
        page={page}
        totalPages={totalPages}
        onPageChange={setPage}
        emptyMessage="Nessuna transazione trovata"
      />
    </div>
  );
}
