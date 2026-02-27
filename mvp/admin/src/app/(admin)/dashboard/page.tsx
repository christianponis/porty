"use client";

import { useEffect, useState } from "react";
import { useUIStore } from "@/stores/ui";
import * as adminApi from "@/lib/api/admin";
import type { DashboardStats } from "@/lib/api/types";
import StatsCard from "@/components/common/StatsCard";
import Badge from "@/components/common/Badge";
import {
  UsersIcon,
  BuildingOfficeIcon,
  Squares2X2Icon,
  CalendarDaysIcon,
  CurrencyEuroIcon,
  SparklesIcon,
} from "@heroicons/react/24/outline";

function formatEur(v: number) {
  return new Intl.NumberFormat("it-IT", {
    style: "currency",
    currency: "EUR",
    minimumFractionDigits: 0,
  }).format(v);
}

function formatDate(d: string) {
  return new Date(d).toLocaleDateString("it-IT", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
}

const statusColors: Record<string, "success" | "warning" | "danger" | "info" | "default"> = {
  pending: "warning",
  confirmed: "info",
  completed: "success",
  cancelled: "danger",
};

const statusLabels: Record<string, string> = {
  pending: "In attesa",
  confirmed: "Confermata",
  completed: "Completata",
  cancelled: "Cancellata",
};

const roleLabels: Record<string, string> = {
  admin: "Admin",
  owner: "Proprietario",
  guest: "Ospite",
};

const roleColors: Record<string, "danger" | "primary" | "success" | "default"> = {
  admin: "danger",
  owner: "primary",
  guest: "success",
};

export default function DashboardPage() {
  const { addToast } = useUIStore();
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const load = async () => {
      try {
        const data = await adminApi.getDashboard();
        setStats(data);
      } catch {
        addToast({ type: "error", message: "Errore nel caricamento della dashboard" });
      } finally {
        setLoading(false);
      }
    };
    load();
  }, [addToast]);

  if (loading) {
    return (
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl font-bold text-slate-800">Dashboard</h1>
          <p className="mt-1 text-sm text-slate-500">Panoramica generale della piattaforma</p>
        </div>
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {Array.from({ length: 6 }).map((_, i) => (
            <div key={i} className="h-28 animate-pulse rounded-xl border border-slate-200 bg-slate-100" />
          ))}
        </div>
      </div>
    );
  }

  if (!stats) return null;

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl font-bold text-slate-800">Dashboard</h1>
        <p className="mt-1 text-sm text-slate-500">
          Panoramica generale della piattaforma
        </p>
      </div>

      {/* KPI Cards */}
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <StatsCard
          title="Utenti"
          value={stats.total_users.toLocaleString("it-IT")}
          icon={UsersIcon}
          color="sky"
        />
        <StatsCard
          title="Porti"
          value={stats.total_ports.toLocaleString("it-IT")}
          icon={BuildingOfficeIcon}
          color="cyan"
        />
        <StatsCard
          title="Posti Barca"
          value={stats.total_berths.toLocaleString("it-IT")}
          subtitle={`${stats.active_berths} attivi`}
          icon={Squares2X2Icon}
          color="emerald"
        />
        <StatsCard
          title="Prenotazioni"
          value={stats.total_bookings.toLocaleString("it-IT")}
          subtitle={`${stats.pending_bookings} in attesa`}
          icon={CalendarDaysIcon}
          color="amber"
        />
        <StatsCard
          title="Revenue"
          value={formatEur(stats.total_revenue)}
          icon={CurrencyEuroIcon}
          color="purple"
        />
        <StatsCard
          title="Nodi in Circolo"
          value={stats.total_nodi.toLocaleString("it-IT")}
          icon={SparklesIcon}
          color="cyan"
        />
      </div>

      {/* Tables row */}
      <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {/* Recent Users */}
        <div className="rounded-xl border border-slate-200 bg-white shadow-sm">
          <div className="border-b border-slate-100 px-5 py-4">
            <h2 className="text-sm font-semibold text-slate-700">
              Ultimi utenti registrati
            </h2>
          </div>
          <div className="divide-y divide-slate-100">
            {stats.recent_users.length === 0 ? (
              <p className="px-5 py-8 text-center text-sm text-slate-400">
                Nessun utente recente
              </p>
            ) : (
              stats.recent_users.map((u) => (
                <div key={u.id} className="flex items-center gap-3 px-5 py-3">
                  <div className="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                    {(u.first_name?.[0] || "").toUpperCase()}
                    {(u.last_name?.[0] || "").toUpperCase()}
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="truncate text-sm font-medium text-slate-700">
                      {u.name}
                    </p>
                    <p className="truncate text-xs text-slate-400">{u.email}</p>
                  </div>
                  <Badge variant={roleColors[u.role] || "default"}>
                    {roleLabels[u.role] || u.role}
                  </Badge>
                </div>
              ))
            )}
          </div>
        </div>

        {/* Recent Bookings */}
        <div className="rounded-xl border border-slate-200 bg-white shadow-sm">
          <div className="border-b border-slate-100 px-5 py-4">
            <h2 className="text-sm font-semibold text-slate-700">
              Ultime prenotazioni
            </h2>
          </div>
          <div className="divide-y divide-slate-100">
            {stats.recent_bookings.length === 0 ? (
              <p className="px-5 py-8 text-center text-sm text-slate-400">
                Nessuna prenotazione recente
              </p>
            ) : (
              stats.recent_bookings.map((b) => (
                <div key={b.id} className="flex items-center gap-3 px-5 py-3">
                  <div className="flex-1 min-w-0">
                    <p className="truncate text-sm font-medium text-slate-700">
                      {b.berth?.title || `Berth #${b.berth?.id}`}
                    </p>
                    <p className="truncate text-xs text-slate-400">
                      {b.guest?.name} &middot; {formatDate(b.start_date)} - {formatDate(b.end_date)}
                    </p>
                  </div>
                  <div className="text-right">
                    <p className="text-sm font-semibold text-slate-700">
                      {formatEur(b.total_price)}
                    </p>
                    <Badge variant={statusColors[b.status] || "default"}>
                      {statusLabels[b.status] || b.status}
                    </Badge>
                  </div>
                </div>
              ))
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
