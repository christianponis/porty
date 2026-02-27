"use client";

import { useEffect, useState, useCallback } from "react";
import { useUIStore } from "@/stores/ui";
import * as adminApi from "@/lib/api/admin";
import type { Booking, PaginatedResponse } from "@/lib/api/types";
import DataTable, { Column } from "@/components/common/DataTable";
import SearchInput from "@/components/common/SearchInput";
import Badge from "@/components/common/Badge";
import { formatEur, formatDate } from "@/lib/utils/formatters";
import { statusLabels, statusColors } from "@/lib/utils/constants";

const statusFilters = [
  { value: "", label: "Tutte" },
  { value: "pending", label: "In attesa" },
  { value: "confirmed", label: "Confermate" },
  { value: "completed", label: "Completate" },
  { value: "cancelled", label: "Cancellate" },
];

export default function BookingsPage() {
  const { addToast } = useUIStore();
  const [bookings, setBookings] = useState<Booking[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [search, setSearch] = useState("");
  const [statusFilter, setStatusFilter] = useState("");

  const fetchBookings = useCallback(async () => {
    try {
      setLoading(true);
      const res: PaginatedResponse<Booking> = await adminApi.getBookings(page, {
        status: statusFilter || undefined,
        search: search || undefined,
      });
      setBookings(res.data);
      setTotalPages(res.last_page);
    } catch {
      addToast({ type: "error", message: "Errore nel caricamento prenotazioni" });
    } finally {
      setLoading(false);
    }
  }, [page, search, statusFilter, addToast]);

  useEffect(() => {
    fetchBookings();
  }, [fetchBookings]);

  const columns: Column<Booking & Record<string, unknown>>[] = [
    {
      key: "id",
      header: "#",
      render: (b) => (
        <span className="text-xs font-mono text-slate-400">#{b.id}</span>
      ),
    },
    {
      key: "berth",
      header: "Posto Barca",
      render: (b) => (
        <div>
          <p className="font-medium text-slate-800">
            {(b.berth as { title?: string })?.title || `#${(b.berth as { id?: number })?.id}`}
          </p>
          <p className="text-xs text-slate-400">
            {(b.berth as { port?: { name?: string } })?.port?.name || ""}
          </p>
        </div>
      ),
    },
    {
      key: "guest",
      header: "Ospite",
      render: (b) => (
        <div>
          <p className="text-sm text-slate-700">
            {(b.guest as { name?: string })?.name || "N/D"}
          </p>
          <p className="text-xs text-slate-400">
            {(b.guest as { email?: string })?.email || ""}
          </p>
        </div>
      ),
    },
    {
      key: "dates",
      header: "Date",
      render: (b) => (
        <div className="text-xs text-slate-600">
          <p>{formatDate(b.start_date)}</p>
          <p className="text-slate-400">{formatDate(b.end_date)}</p>
        </div>
      ),
    },
    {
      key: "total_price",
      header: "Totale",
      render: (b) => (
        <span className="font-medium text-slate-700">{formatEur(b.total_price)}</span>
      ),
    },
    {
      key: "mode",
      header: "Modo",
      render: (b) => (
        <Badge variant={b.booking_mode === "sharing" ? "cyan" : "default"}>
          {b.booking_mode === "sharing" ? "Sharing" : "Rental"}
        </Badge>
      ),
    },
    {
      key: "status",
      header: "Stato",
      render: (b) => (
        <Badge variant={statusColors[b.status] || "default"}>
          {statusLabels[b.status] || b.status}
        </Badge>
      ),
    },
    {
      key: "created_at",
      header: "Creata",
      render: (b) => (
        <span className="text-xs text-slate-400">{formatDate(b.created_at)}</span>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-slate-800">Gestione Prenotazioni</h1>
        <p className="mt-1 text-sm text-slate-500">
          Tutte le prenotazioni della piattaforma
        </p>
      </div>

      <div className="flex flex-wrap items-end gap-4">
        <SearchInput
          value={search}
          onChange={(v) => {
            setSearch(v);
            setPage(1);
          }}
          placeholder="Cerca per ospite..."
          className="w-64"
        />
        <div className="flex gap-1">
          {statusFilters.map((f) => (
            <button
              key={f.value}
              onClick={() => {
                setStatusFilter(f.value);
                setPage(1);
              }}
              className={`rounded-lg px-3 py-1.5 text-xs font-medium transition ${
                statusFilter === f.value
                  ? "bg-sky-600 text-white"
                  : "bg-white text-slate-600 border border-slate-200 hover:bg-slate-50"
              }`}
            >
              {f.label}
            </button>
          ))}
        </div>
      </div>

      <DataTable
        columns={columns}
        data={bookings as (Booking & Record<string, unknown>)[]}
        loading={loading}
        page={page}
        totalPages={totalPages}
        onPageChange={setPage}
        emptyMessage="Nessuna prenotazione trovata"
      />
    </div>
  );
}
