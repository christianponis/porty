"use client";

import { useEffect, useState } from "react";
import { useParams } from "next/navigation";
import { useUIStore } from "@/stores/ui";
import * as adminApi from "@/lib/api/admin";
import type { Berth } from "@/lib/api/types";
import Badge from "@/components/common/Badge";
import Button from "@/components/common/Button";
import { formatEur } from "@/lib/utils/formatters";
import { ratingLevelLabels, ratingLevelColors } from "@/lib/utils/constants";
import {
  MapPinIcon,
  UserIcon,
  CurrencyEuroIcon,
  StarIcon,
} from "@heroicons/react/24/outline";

export default function BerthDetailPage() {
  const params = useParams();
  const berthId = Number(params.id);
  const { addToast } = useUIStore();
  const [berth, setBerth] = useState<Berth | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const load = async () => {
      try {
        const res = await adminApi.getBerth(berthId);
        setBerth((res as { data?: Berth }).data || res as Berth);
      } catch {
        addToast({ type: "error", message: "Errore nel caricamento" });
      } finally {
        setLoading(false);
      }
    };
    if (berthId) load();
  }, [berthId, addToast]);

  const handleToggle = async () => {
    if (!berth) return;
    try {
      await adminApi.toggleBerthActive(berth.id);
      setBerth({ ...berth, is_active: !berth.is_active });
      addToast({ type: "success", message: berth.is_active ? "Disattivato" : "Attivato" });
    } catch {
      addToast({ type: "error", message: "Errore" });
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center py-20">
        <div className="h-8 w-8 animate-spin rounded-full border-4 border-sky-200 border-t-sky-600" />
      </div>
    );
  }

  if (!berth) return <p className="py-10 text-center text-slate-400">Posto barca non trovato</p>;

  const ext = berth as Record<string, unknown>;

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-start justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-800">{berth.title}</h1>
          <p className="mt-1 flex items-center gap-2 text-sm text-slate-500">
            <span className="font-mono">{berth.code}</span>
            {berth.port && (
              <>
                <span>&middot;</span>
                <MapPinIcon className="h-4 w-4" />
                {(berth.port as { name?: string }).name}
              </>
            )}
          </p>
        </div>
        <div className="flex items-center gap-2">
          <Badge variant={berth.is_active ? "success" : "default"} className="text-sm">
            {berth.is_active ? "Attivo" : "Inattivo"}
          </Badge>
          <Button
            variant={berth.is_active ? "danger" : "primary"}
            size="sm"
            onClick={handleToggle}
          >
            {berth.is_active ? "Disattiva" : "Attiva"}
          </Button>
        </div>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div className="rounded-xl border border-slate-200 bg-white p-4">
          <p className="text-xs uppercase text-slate-400">Prezzo/giorno</p>
          <p className="mt-1 text-xl font-bold text-slate-800">{formatEur(berth.price_per_day)}</p>
        </div>
        <div className="rounded-xl border border-slate-200 bg-white p-4">
          <p className="text-xs uppercase text-slate-400">Dimensioni</p>
          <p className="mt-1 text-xl font-bold text-slate-800">
            {berth.length_m} x {berth.width_m}m
          </p>
        </div>
        <div className="rounded-xl border border-slate-200 bg-white p-4">
          <p className="text-xs uppercase text-slate-400">Prenotazioni</p>
          <p className="mt-1 text-xl font-bold text-sky-600">{ext.total_bookings as number ?? 0}</p>
        </div>
        <div className="rounded-xl border border-slate-200 bg-white p-4">
          <p className="text-xs uppercase text-slate-400">Revenue</p>
          <p className="mt-1 text-xl font-bold text-emerald-600">
            {formatEur((ext.total_revenue as number) ?? 0)}
          </p>
        </div>
      </div>

      {/* Details */}
      <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {/* Info */}
        <div className="rounded-xl border border-slate-200 bg-white p-5">
          <h3 className="mb-4 text-sm font-semibold text-slate-700">Dettagli</h3>
          <dl className="space-y-3">
            <div className="flex justify-between text-sm">
              <dt className="text-slate-500">Pescaggio max</dt>
              <dd className="font-medium text-slate-700">{berth.max_draft_m}m</dd>
            </div>
            <div className="flex justify-between text-sm">
              <dt className="text-slate-500">Status</dt>
              <dd className="font-medium text-slate-700">{berth.status}</dd>
            </div>
            <div className="flex justify-between text-sm">
              <dt className="text-slate-500">Sharing</dt>
              <dd>
                <Badge variant={berth.sharing_enabled ? "success" : "default"}>
                  {berth.sharing_enabled ? "Abilitato" : "Disabilitato"}
                </Badge>
              </dd>
            </div>
            {berth.rating_level && (
              <div className="flex justify-between text-sm">
                <dt className="text-slate-500">Livello Rating</dt>
                <dd>
                  <Badge variant={ratingLevelColors[berth.rating_level] || "default"}>
                    {ratingLevelLabels[berth.rating_level] || berth.rating_level}
                  </Badge>
                </dd>
              </div>
            )}
            <div className="flex justify-between text-sm">
              <dt className="text-slate-500">Recensioni</dt>
              <dd className="font-medium text-slate-700">
                {berth.review_count}
                {berth.review_average ? ` (media: ${berth.review_average.toFixed(1)})` : ""}
              </dd>
            </div>
            {berth.nodi_value_per_day && (
              <div className="flex justify-between text-sm">
                <dt className="text-slate-500">Nodi/giorno</dt>
                <dd className="font-medium text-emerald-600">{berth.nodi_value_per_day}</dd>
              </div>
            )}
          </dl>
        </div>

        {/* Owner */}
        <div className="rounded-xl border border-slate-200 bg-white p-5">
          <h3 className="mb-4 text-sm font-semibold text-slate-700">Proprietario</h3>
          {berth.owner ? (
            <div className="flex items-center gap-3">
              <div className="flex h-10 w-10 items-center justify-center rounded-full bg-sky-100">
                <UserIcon className="h-5 w-5 text-sky-600" />
              </div>
              <div>
                <p className="font-medium text-slate-800">{(berth.owner as { name?: string }).name}</p>
                <p className="text-sm text-slate-400">{(berth.owner as { email?: string }).email}</p>
              </div>
            </div>
          ) : (
            <p className="text-sm text-slate-400">Proprietario non trovato</p>
          )}

          {berth.description && (
            <div className="mt-6">
              <h4 className="mb-2 text-sm font-semibold text-slate-700">Descrizione</h4>
              <p className="text-sm text-slate-600">{berth.description}</p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
