"use client";

import { useEffect, useState, useCallback } from "react";
import { useUIStore } from "@/stores/ui";
import * as adminApi from "@/lib/api/admin";
import type { Berth, PaginatedResponse } from "@/lib/api/types";
import DataTable, { Column } from "@/components/common/DataTable";
import SearchInput from "@/components/common/SearchInput";
import Select from "@/components/common/Select";
import Badge from "@/components/common/Badge";
import { formatEur } from "@/lib/utils/formatters";
import { ratingLevelLabels, ratingLevelColors } from "@/lib/utils/constants";
import Link from "next/link";
import { EyeIcon } from "@heroicons/react/24/outline";

export default function BerthsPage() {
  const { addToast } = useUIStore();
  const [berths, setBerths] = useState<Berth[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [search, setSearch] = useState("");
  const [statusFilter, setStatusFilter] = useState("");
  const [ratingFilter, setRatingFilter] = useState("");

  const fetchBerths = useCallback(async () => {
    try {
      setLoading(true);
      const res = await adminApi.getBerths(page, {
        search,
        status: statusFilter || undefined,
        rating_level: ratingFilter || undefined,
      });
      setBerths(res.data);
      setTotalPages(res.last_page);
    } catch {
      addToast({ type: "error", message: "Errore nel caricamento" });
    } finally {
      setLoading(false);
    }
  }, [page, search, statusFilter, ratingFilter, addToast]);

  useEffect(() => {
    fetchBerths();
  }, [fetchBerths]);

  const handleToggleActive = async (berth: Berth) => {
    try {
      await adminApi.toggleBerthActive(berth.id);
      addToast({
        type: "success",
        message: berth.is_active ? "Posto barca disattivato" : "Posto barca attivato",
      });
      fetchBerths();
    } catch {
      addToast({ type: "error", message: "Errore nell'aggiornamento" });
    }
  };

  const columns: Column<Berth & Record<string, unknown>>[] = [
    {
      key: "title",
      header: "Posto Barca",
      render: (b) => (
        <div>
          <p className="font-medium text-slate-800">{b.title}</p>
          <p className="text-xs text-slate-400">{b.code}</p>
        </div>
      ),
    },
    {
      key: "port",
      header: "Porto",
      render: (b) => (
        <span className="text-slate-600">{(b.port as { name?: string })?.name || "N/D"}</span>
      ),
    },
    {
      key: "owner",
      header: "Proprietario",
      render: (b) => (
        <span className="text-slate-600">{(b.owner as { name?: string })?.name || "N/D"}</span>
      ),
    },
    {
      key: "size",
      header: "Dim.",
      render: (b) => (
        <span className="text-xs text-slate-500">
          {b.length_m}x{b.width_m}m
        </span>
      ),
    },
    {
      key: "price",
      header: "Prezzo/g",
      render: (b) => (
        <span className="font-medium text-slate-700">{formatEur(b.price_per_day)}</span>
      ),
    },
    {
      key: "rating",
      header: "Rating",
      render: (b) =>
        b.rating_level ? (
          <Badge variant={ratingLevelColors[b.rating_level] || "default"}>
            {ratingLevelLabels[b.rating_level] || b.rating_level}
          </Badge>
        ) : (
          <span className="text-xs text-slate-400">-</span>
        ),
    },
    {
      key: "reviews",
      header: "Rec.",
      render: (b) => (
        <span className="text-sm text-slate-600">
          {b.review_count}
          {b.review_average ? ` (${b.review_average.toFixed(1)})` : ""}
        </span>
      ),
    },
    {
      key: "is_active",
      header: "Stato",
      render: (b) => (
        <button
          onClick={() => handleToggleActive(b as Berth)}
          className="cursor-pointer"
        >
          <Badge variant={b.is_active ? "success" : "default"}>
            {b.is_active ? "Attivo" : "Inattivo"}
          </Badge>
        </button>
      ),
    },
    {
      key: "actions",
      header: "",
      className: "text-right",
      render: (b) => (
        <Link
          href={`/berths/${b.id}`}
          className="rounded-lg p-1.5 text-slate-400 transition hover:bg-sky-50 hover:text-sky-600 inline-flex"
        >
          <EyeIcon className="h-4 w-4" />
        </Link>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-slate-800">Gestione Posti Barca</h1>
        <p className="mt-1 text-sm text-slate-500">
          Tutti i posti barca della piattaforma
        </p>
      </div>

      <div className="flex flex-wrap items-end gap-4">
        <SearchInput
          value={search}
          onChange={(v) => {
            setSearch(v);
            setPage(1);
          }}
          placeholder="Cerca per titolo o codice..."
          className="w-64"
        />
        <Select
          options={[
            { value: "available", label: "Disponibile" },
            { value: "occupied", label: "Occupato" },
            { value: "maintenance", label: "Manutenzione" },
          ]}
          value={statusFilter}
          onChange={(e) => {
            setStatusFilter(e.target.value);
            setPage(1);
          }}
          placeholder="Tutti gli stati"
          className="w-44"
        />
        <Select
          options={[
            { value: "grey", label: "Ancora Grigia" },
            { value: "blue", label: "Ancora Blu" },
            { value: "gold", label: "Ancora Oro" },
          ]}
          value={ratingFilter}
          onChange={(e) => {
            setRatingFilter(e.target.value);
            setPage(1);
          }}
          placeholder="Tutti i rating"
          className="w-44"
        />
      </div>

      <DataTable
        columns={columns}
        data={berths as (Berth & Record<string, unknown>)[]}
        loading={loading}
        page={page}
        totalPages={totalPages}
        onPageChange={setPage}
        emptyMessage="Nessun posto barca trovato"
      />
    </div>
  );
}
