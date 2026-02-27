'use client';

import { useEffect, useState, useCallback } from 'react';
import Link from 'next/link';
import { useUIStore } from '@/stores/ui';
import Button from '@/components/common/Button';
import Badge from '@/components/common/Badge';
import AnchorRating from '@/components/domain/AnchorRating';
import * as ownerApi from '@/lib/api/owner';
import type { Berth } from '@/lib/api/types';
import {
  PlusIcon,
  PencilSquareIcon,
  EyeIcon,
  ClipboardDocumentCheckIcon,
  Squares2X2Icon,
} from '@heroicons/react/24/outline';

export default function OwnerBerthsPage() {
  const { addToast } = useUIStore();
  const [berths, setBerths] = useState<Berth[]>([]);
  const [loading, setLoading] = useState(true);

  const fetchBerths = useCallback(async () => {
    try {
      setLoading(true);
      const data = await ownerApi.getMyBerths();
      setBerths(data);
    } catch {
      addToast({ type: 'error', message: 'Errore nel caricamento dei posti barca' });
    } finally {
      setLoading(false);
    }
  }, [addToast]);

  useEffect(() => {
    fetchBerths();
  }, [fetchBerths]);

  if (loading) {
    return (
      <div className="space-y-6">
        <div className="flex items-center justify-between">
          <div className="h-8 w-48 animate-pulse rounded-lg bg-sky-50" />
          <div className="h-10 w-44 animate-pulse rounded-lg bg-sky-50" />
        </div>
        <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {[...Array(6)].map((_, i) => (
            <div key={i} className="h-64 animate-pulse rounded-xl bg-sky-50" />
          ))}
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 className="text-2xl font-bold text-sky-900">I miei posti barca</h1>
          <p className="mt-1 text-sm text-slate-500">
            Gestisci i tuoi posti barca e le relative prenotazioni
          </p>
        </div>
        <Link href="/owner/berths/create">
          <Button>
            <PlusIcon className="h-4 w-4" />
            Aggiungi posto barca
          </Button>
        </Link>
      </div>

      {/* Grid */}
      {berths.length === 0 ? (
        <div className="flex flex-col items-center justify-center rounded-xl border border-sky-100 bg-white py-16 shadow-sm">
          <Squares2X2Icon className="h-12 w-12 text-slate-300 mb-4" />
          <h3 className="text-lg font-semibold text-slate-700">Nessun posto barca</h3>
          <p className="mt-1 text-sm text-slate-400">
            Aggiungi il tuo primo posto barca per iniziare a ricevere prenotazioni
          </p>
          <Link href="/owner/berths/create" className="mt-6">
            <Button>
              <PlusIcon className="h-4 w-4" />
              Aggiungi posto barca
            </Button>
          </Link>
        </div>
      ) : (
        <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {berths.map((berth) => (
            <div
              key={berth.id}
              className="overflow-hidden rounded-xl border border-sky-100 bg-white shadow-sm transition-shadow hover:shadow-md"
            >
              {/* Image */}
              <div className="relative h-40 bg-gradient-to-br from-sky-100 to-cyan-50">
                {berth.main_image ? (
                  <img
                    src={berth.main_image}
                    alt={berth.name}
                    className="h-full w-full object-cover"
                  />
                ) : (
                  <div className="flex h-full items-center justify-center">
                    <Squares2X2Icon className="h-12 w-12 text-sky-200" />
                  </div>
                )}
                <div className="absolute top-3 right-3">
                  <Badge variant={berth.is_available ? 'success' : 'danger'}>
                    {berth.is_available ? 'Disponibile' : 'Non disponibile'}
                  </Badge>
                </div>
              </div>

              {/* Content */}
              <div className="p-4 space-y-3">
                <div>
                  <h3 className="text-lg font-semibold text-sky-900">{berth.name}</h3>
                  <p className="text-sm text-slate-500">{berth.port.name}, {berth.port.city}</p>
                </div>

                <div className="flex items-center justify-between">
                  <AnchorRating count={berth.anchor_rating} level={berth.anchor_level} size="sm" />
                  <span className="text-lg font-bold text-sky-800">
                    &euro;{berth.price_per_night.toFixed(2)}
                    <span className="text-xs font-normal text-slate-400">/notte</span>
                  </span>
                </div>

                {/* Actions */}
                <div className="flex items-center gap-2 pt-2 border-t border-sky-50">
                  <Link href={`/owner/berths/${berth.id}`} className="flex-1">
                    <Button variant="secondary" size="sm" className="w-full">
                      <EyeIcon className="h-3.5 w-3.5" />
                      Dettaglio
                    </Button>
                  </Link>
                  <Link href={`/owner/berths/${berth.id}/edit`} className="flex-1">
                    <Button variant="ghost" size="sm" className="w-full">
                      <PencilSquareIcon className="h-3.5 w-3.5" />
                      Modifica
                    </Button>
                  </Link>
                  <Link href={`/owner/berths/${berth.id}/assessment`}>
                    <Button variant="ghost" size="sm" title="Autovalutazione">
                      <ClipboardDocumentCheckIcon className="h-3.5 w-3.5" />
                    </Button>
                  </Link>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
