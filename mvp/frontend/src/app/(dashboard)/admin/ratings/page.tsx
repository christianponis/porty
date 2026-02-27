'use client';

import { useEffect, useState, useCallback } from 'react';
import { useUIStore } from '@/stores/ui';
import DataTable, { Column } from '@/components/common/DataTable';
import AnchorRating from '@/components/domain/AnchorRating';
import * as adminApi from '@/lib/api/admin';
import type { PaginatedResponse, Berth } from '@/lib/api/types';

const levelColors: Record<string, string> = {
  grey: 'text-slate-500',
  blue: 'text-sky-600',
  gold: 'text-amber-500',
};

const levelLabel: Record<string, string> = {
  grey: 'Grigio',
  blue: 'Blu',
  gold: 'Oro',
};

const PAGE_SIZE = 15;

export default function AdminRatingsPage() {
  const { addToast } = useUIStore();
  const [berths, setBerths] = useState<Berth[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

  const fetchRatings = useCallback(async () => {
    try {
      setLoading(true);
      const res: PaginatedResponse<Berth> = await adminApi.getRatings(page);
      setBerths(res.results);
      setTotalPages(Math.ceil(res.count / PAGE_SIZE));
    } catch {
      addToast({ type: 'error', message: 'Errore nel caricamento delle valutazioni' });
    } finally {
      setLoading(false);
    }
  }, [page, addToast]);

  useEffect(() => {
    fetchRatings();
  }, [fetchRatings]);

  const columns: Column<Berth & Record<string, unknown>>[] = [
    {
      key: 'name',
      header: 'Posto barca',
      render: (b) => <span className="font-medium text-slate-800">{b.name}</span>,
    },
    {
      key: 'port',
      header: 'Porto',
      render: (b) => (
        <span className="text-slate-500">{b.port?.name ?? '-'}</span>
      ),
    },
    {
      key: 'anchor_level',
      header: 'Livello Rating',
      render: (b) => (
        <span className={`font-semibold ${levelColors[b.anchor_level] ?? 'text-slate-500'}`}>
          {levelLabel[b.anchor_level] ?? b.anchor_level}
        </span>
      ),
    },
    {
      key: 'grey_anchors',
      header: 'Ancore Grigie',
      render: (b) => (
        <AnchorRating
          count={b.anchor_level === 'grey' ? b.anchor_rating : 0}
          level="grey"
          size="sm"
        />
      ),
    },
    {
      key: 'blue_anchors',
      header: 'Ancore Blu',
      render: (b) => (
        <AnchorRating
          count={b.anchor_level === 'blue' ? b.anchor_rating : 0}
          level="blue"
          size="sm"
        />
      ),
    },
    {
      key: 'gold_anchors',
      header: 'Ancore Dorate',
      render: (b) => (
        <AnchorRating
          count={b.anchor_level === 'gold' ? b.anchor_rating : 0}
          level="gold"
          size="sm"
        />
      ),
    },
    {
      key: 'review_count',
      header: 'N. Recensioni',
      render: (b) => (
        <span className="font-medium text-slate-700">{b.review_count}</span>
      ),
    },
    {
      key: 'average_rating',
      header: 'Media',
      render: (b) => (
        <div className="flex items-center gap-2">
          <AnchorRating
            count={Math.round(b.average_rating)}
            level={b.anchor_level}
            size="sm"
          />
          <span className="text-xs font-medium text-slate-500">
            {b.average_rating.toFixed(1)}
          </span>
        </div>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl font-bold text-sky-900">Valutazioni Ancore</h1>
        <p className="mt-1 text-sm text-slate-500">
          Panoramica dei rating e delle recensioni di tutti i posti barca.
        </p>
      </div>

      {/* Legend */}
      <div className="flex flex-wrap items-center gap-6 rounded-xl border border-sky-100 bg-white px-5 py-3 shadow-sm">
        <span className="text-xs font-semibold uppercase tracking-wider text-slate-400">Legenda</span>
        <div className="flex items-center gap-1.5">
          <AnchorRating count={1} level="grey" size="sm" />
          <span className="text-xs text-slate-500">Grigio</span>
        </div>
        <div className="flex items-center gap-1.5">
          <AnchorRating count={1} level="blue" size="sm" />
          <span className="text-xs text-slate-500">Blu</span>
        </div>
        <div className="flex items-center gap-1.5">
          <AnchorRating count={1} level="gold" size="sm" />
          <span className="text-xs text-slate-500">Oro</span>
        </div>
      </div>

      {/* Table */}
      <DataTable
        columns={columns}
        data={berths as (Berth & Record<string, unknown>)[]}
        loading={loading}
        page={page}
        totalPages={totalPages}
        onPageChange={setPage}
        emptyMessage="Nessuna valutazione disponibile"
      />
    </div>
  );
}
