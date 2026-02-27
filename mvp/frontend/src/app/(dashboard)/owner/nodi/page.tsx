'use client';

import { useEffect, useState, useCallback } from 'react';
import { useUIStore } from '@/stores/ui';
import Badge from '@/components/common/Badge';
import NodiBadge from '@/components/domain/NodiBadge';
import DataTable, { Column } from '@/components/common/DataTable';
import * as walletApi from '@/lib/api/wallet';
import type { Wallet, Transaction } from '@/lib/api/types';
import {
  WalletIcon,
  ArrowTrendingUpIcon,
  ArrowTrendingDownIcon,
} from '@heroicons/react/24/outline';

const typeMap: Record<string, { label: string; variant: 'success' | 'warning' | 'danger' | 'info' }> = {
  earn: { label: 'Guadagno', variant: 'success' },
  spend: { label: 'Spesa', variant: 'warning' },
  refund: { label: 'Rimborso', variant: 'info' },
};

export default function OwnerNodiPage() {
  const { addToast } = useUIStore();
  const [wallet, setWallet] = useState<Wallet | null>(null);
  const [transactions, setTransactions] = useState<Transaction[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

  const fetchData = useCallback(async () => {
    try {
      setLoading(true);
      const [walletData, txData] = await Promise.all([
        walletApi.getWallet(),
        walletApi.getTransactions(page),
      ]);
      setWallet(walletData);
      setTransactions(txData.results);
      setTotalPages(Math.ceil(txData.count / 10));
    } catch {
      addToast({ type: 'error', message: 'Errore nel caricamento del portafoglio' });
    } finally {
      setLoading(false);
    }
  }, [page, addToast]);

  useEffect(() => {
    fetchData();
  }, [fetchData]);

  const columns: Column<Record<string, unknown>>[] = [
    {
      key: 'created_at',
      header: 'Data',
      render: (item) => (
        <span className="text-slate-500">
          {new Date(item.created_at as string).toLocaleDateString('it-IT', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
          })}
        </span>
      ),
    },
    {
      key: 'type',
      header: 'Tipo',
      render: (item) => {
        const t = typeMap[item.type as string] || { label: item.type, variant: 'info' as const };
        return <Badge variant={t.variant}>{t.label}</Badge>;
      },
    },
    {
      key: 'description',
      header: 'Descrizione',
      render: (item) => (
        <span className="text-slate-700">{item.description as string}</span>
      ),
    },
    {
      key: 'amount',
      header: 'Importo',
      render: (item) => {
        const amount = item.amount as number;
        const isPositive = amount > 0;
        return (
          <span className={`font-semibold ${isPositive ? 'text-emerald-600' : 'text-red-600'}`}>
            {isPositive ? '+' : ''}{amount} Nodi
          </span>
        );
      },
    },
  ];

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl font-bold text-sky-900">Portafoglio Nodi</h1>
        <p className="mt-1 text-sm text-slate-500">
          Gestisci il tuo saldo Nodi e consulta lo storico delle transazioni
        </p>
      </div>

      {/* Balance card */}
      {loading && !wallet ? (
        <div className="h-44 animate-pulse rounded-2xl bg-emerald-50" />
      ) : wallet ? (
        <div className="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-400 p-6 text-white shadow-xl">
          {/* Background decorations */}
          <div className="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-white/10" />
          <div className="absolute -bottom-4 -left-4 h-24 w-24 rounded-full bg-white/10" />

          <div className="relative">
            <div className="flex items-center gap-3 mb-6">
              <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                <WalletIcon className="h-5 w-5" />
              </div>
              <p className="text-sm font-medium text-emerald-100">Saldo attuale</p>
            </div>

            <p className="text-4xl font-bold tracking-tight">
              {wallet.nodi_balance.toLocaleString('it-IT')} <span className="text-xl font-normal text-emerald-200">Nodi</span>
            </p>

            <div className="mt-6 grid grid-cols-2 gap-4">
              <div className="rounded-xl bg-white/10 backdrop-blur-sm p-3">
                <div className="flex items-center gap-2 mb-1">
                  <ArrowTrendingUpIcon className="h-4 w-4 text-emerald-200" />
                  <p className="text-xs font-medium text-emerald-200">Totale guadagnati</p>
                </div>
                <p className="text-lg font-bold">{wallet.total_earned.toLocaleString('it-IT')}</p>
              </div>
              <div className="rounded-xl bg-white/10 backdrop-blur-sm p-3">
                <div className="flex items-center gap-2 mb-1">
                  <ArrowTrendingDownIcon className="h-4 w-4 text-emerald-200" />
                  <p className="text-xs font-medium text-emerald-200">Totale spesi</p>
                </div>
                <p className="text-lg font-bold">{wallet.total_spent.toLocaleString('it-IT')}</p>
              </div>
            </div>
          </div>
        </div>
      ) : null}

      {/* Transactions */}
      <div>
        <h2 className="mb-4 text-lg font-semibold text-sky-900">Storico transazioni</h2>
        <DataTable
          columns={columns}
          data={transactions as unknown as Record<string, unknown>[]}
          loading={loading}
          page={page}
          totalPages={totalPages}
          onPageChange={setPage}
          emptyMessage="Nessuna transazione ancora"
        />
      </div>
    </div>
  );
}
