"use client";

import { useEffect, useState } from "react";
import { useParams } from "next/navigation";
import { useUIStore } from "@/stores/ui";
import * as adminApi from "@/lib/api/admin";
import type { Port, Convention, Berth } from "@/lib/api/types";
import Badge from "@/components/common/Badge";
import Button from "@/components/common/Button";
import {
  MapPinIcon,
  Squares2X2Icon,
  TicketIcon,
} from "@heroicons/react/24/outline";
import Link from "next/link";

export default function PortDetailPage() {
  const params = useParams();
  const portId = Number(params.id);
  const { addToast } = useUIStore();

  const [port, setPort] = useState<Port | null>(null);
  const [conventions, setConventions] = useState<Convention[]>([]);
  const [loading, setLoading] = useState(true);
  const [tab, setTab] = useState<"info" | "conventions">("info");

  useEffect(() => {
    const load = async () => {
      try {
        const [portRes, convRes] = await Promise.all([
          adminApi.getPort(portId),
          adminApi.getPortConventions(portId),
        ]);
        setPort((portRes as { data?: Port }).data || portRes as Port);
        setConventions(convRes);
      } catch {
        addToast({ type: "error", message: "Errore nel caricamento del porto" });
      } finally {
        setLoading(false);
      }
    };
    if (portId) load();
  }, [portId, addToast]);

  if (loading) {
    return (
      <div className="flex items-center justify-center py-20">
        <div className="h-8 w-8 animate-spin rounded-full border-4 border-sky-200 border-t-sky-600" />
      </div>
    );
  }

  if (!port) return <p className="py-10 text-center text-slate-400">Porto non trovato</p>;

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-start justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-800">{port.name}</h1>
          <p className="mt-1 flex items-center gap-1.5 text-sm text-slate-500">
            <MapPinIcon className="h-4 w-4" />
            {port.city}, {port.region}, {port.country}
          </p>
        </div>
        <Badge variant={port.is_active ? "success" : "default"} className="text-sm">
          {port.is_active ? "Attivo" : "Inattivo"}
        </Badge>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div className="rounded-xl border border-slate-200 bg-white p-4 text-center">
          <p className="text-2xl font-bold text-sky-600">{(port as Record<string, unknown>).berths_count as number ?? 0}</p>
          <p className="text-xs text-slate-500">Posti Barca</p>
        </div>
        <div className="rounded-xl border border-slate-200 bg-white p-4 text-center">
          <p className="text-2xl font-bold text-emerald-600">{(port as Record<string, unknown>).active_berths_count as number ?? 0}</p>
          <p className="text-xs text-slate-500">Attivi</p>
        </div>
        <div className="rounded-xl border border-slate-200 bg-white p-4 text-center">
          <p className="text-2xl font-bold text-purple-600">{conventions.length}</p>
          <p className="text-xs text-slate-500">Convenzioni</p>
        </div>
        <div className="rounded-xl border border-slate-200 bg-white p-4 text-center">
          <p className="text-2xl font-bold text-slate-600">
            {port.latitude ? `${port.latitude.toFixed(4)}` : "N/D"}
          </p>
          <p className="text-xs text-slate-500">Coordinate</p>
        </div>
      </div>

      {/* Description */}
      {port.description && (
        <div className="rounded-xl border border-slate-200 bg-white p-5">
          <h3 className="mb-2 text-sm font-semibold text-slate-700">Descrizione</h3>
          <p className="text-sm text-slate-600">{port.description}</p>
        </div>
      )}

      {/* Tabs */}
      <div className="flex gap-1 border-b border-slate-200">
        <button
          onClick={() => setTab("info")}
          className={`flex items-center gap-2 border-b-2 px-4 py-2.5 text-sm font-medium transition ${
            tab === "info"
              ? "border-sky-600 text-sky-600"
              : "border-transparent text-slate-500 hover:text-slate-700"
          }`}
        >
          <Squares2X2Icon className="h-4 w-4" />
          Informazioni
        </button>
        <button
          onClick={() => setTab("conventions")}
          className={`flex items-center gap-2 border-b-2 px-4 py-2.5 text-sm font-medium transition ${
            tab === "conventions"
              ? "border-sky-600 text-sky-600"
              : "border-transparent text-slate-500 hover:text-slate-700"
          }`}
        >
          <TicketIcon className="h-4 w-4" />
          Convenzioni ({conventions.length})
        </button>
      </div>

      {tab === "info" && (
        <div className="rounded-xl border border-slate-200 bg-white p-5">
          <dl className="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <dt className="text-xs font-medium uppercase text-slate-400">Nome</dt>
              <dd className="mt-1 text-sm text-slate-700">{port.name}</dd>
            </div>
            <div>
              <dt className="text-xs font-medium uppercase text-slate-400">Citta</dt>
              <dd className="mt-1 text-sm text-slate-700">{port.city}</dd>
            </div>
            <div>
              <dt className="text-xs font-medium uppercase text-slate-400">Regione</dt>
              <dd className="mt-1 text-sm text-slate-700">{port.region}</dd>
            </div>
            <div>
              <dt className="text-xs font-medium uppercase text-slate-400">Paese</dt>
              <dd className="mt-1 text-sm text-slate-700">{port.country}</dd>
            </div>
            <div>
              <dt className="text-xs font-medium uppercase text-slate-400">Latitudine</dt>
              <dd className="mt-1 text-sm text-slate-700">{port.latitude ?? "N/D"}</dd>
            </div>
            <div>
              <dt className="text-xs font-medium uppercase text-slate-400">Longitudine</dt>
              <dd className="mt-1 text-sm text-slate-700">{port.longitude ?? "N/D"}</dd>
            </div>
          </dl>
        </div>
      )}

      {tab === "conventions" && (
        <div className="space-y-3">
          <div className="flex justify-end">
            <Link href="/conventions">
              <Button size="sm">
                <TicketIcon className="h-4 w-4" />
                Gestisci Convenzioni
              </Button>
            </Link>
          </div>
          {conventions.length === 0 ? (
            <div className="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center">
              <TicketIcon className="mx-auto h-10 w-10 text-slate-300" />
              <p className="mt-3 text-sm text-slate-500">
                Nessuna convenzione associata a questo porto
              </p>
            </div>
          ) : (
            conventions.map((c) => (
              <div
                key={c.id}
                className="flex items-center gap-4 rounded-xl border border-slate-200 bg-white p-4 transition hover:shadow-sm"
              >
                <div className="flex-1">
                  <p className="font-medium text-slate-800">{c.name}</p>
                  <p className="text-xs text-slate-400">
                    {c.category_label} &middot;{" "}
                    {c.discount_type === "free"
                      ? "Gratuito"
                      : c.discount_type === "percentage"
                      ? `${c.discount_value}% di sconto`
                      : `${c.discount_value} EUR di sconto`}
                  </p>
                </div>
                <Badge variant={c.is_active ? "success" : "default"}>
                  {c.is_active ? "Attiva" : "Inattiva"}
                </Badge>
              </div>
            ))
          )}
        </div>
      )}
    </div>
  );
}
