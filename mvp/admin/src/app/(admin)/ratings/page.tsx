"use client";

import { useEffect, useState, useCallback } from "react";
import { useUIStore } from "@/stores/ui";
import * as adminApi from "@/lib/api/admin";
import type { Berth, PaginatedResponse } from "@/lib/api/types";
import DataTable, { Column } from "@/components/common/DataTable";
import Badge from "@/components/common/Badge";
import { ratingLevelLabels, ratingLevelColors } from "@/lib/utils/constants";

export default function RatingsPage() {
  const { addToast } = useUIStore();
  const [berths, setBerths] = useState<Berth[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [tab, setTab] = useState<"ratings" | "reviews">("ratings");

  const fetchRatings = useCallback(async () => {
    try {
      setLoading(true);
      const res: PaginatedResponse<Berth> = await adminApi.getRatings(page);
      setBerths(res.data);
      setTotalPages(res.last_page);
    } catch {
      addToast({ type: "error", message: "Errore nel caricamento ratings" });
    } finally {
      setLoading(false);
    }
  }, [page, addToast]);

  useEffect(() => {
    fetchRatings();
  }, [fetchRatings]);

  const ratingColumns: Column<Berth & Record<string, unknown>>[] = [
    {
      key: "title",
      header: "Posto Barca",
      render: (b) => (
        <div>
          <p className="font-medium text-slate-800">{(b as Record<string, unknown>).title as string}</p>
          <p className="text-xs text-slate-400">
            {((b as Record<string, unknown>).port as { name?: string })?.name || ""}
          </p>
        </div>
      ),
    },
    {
      key: "rating_level",
      header: "Livello",
      render: (b) => {
        const level = (b as Record<string, unknown>).rating_level as string;
        return level ? (
          <Badge variant={ratingLevelColors[level] || "default"}>
            {ratingLevelLabels[level] || level}
          </Badge>
        ) : (
          <span className="text-xs text-slate-400">N/D</span>
        );
      },
    },
    {
      key: "grey",
      header: "Grigie",
      render: (b) => (
        <span className="text-sm text-slate-500">
          {(b as Record<string, unknown>).grey_anchor_count as number ?? 0}
        </span>
      ),
    },
    {
      key: "blue",
      header: "Blu",
      render: (b) => (
        <span className="text-sm text-sky-600">
          {(b as Record<string, unknown>).blue_anchor_count as number ?? 0}
        </span>
      ),
    },
    {
      key: "gold",
      header: "Oro",
      render: (b) => (
        <span className="text-sm text-amber-600">
          {(b as Record<string, unknown>).gold_anchor_count as number ?? 0}
        </span>
      ),
    },
    {
      key: "reviews",
      header: "Recensioni",
      render: (b) => (
        <span className="text-sm text-slate-600">
          {(b as Record<string, unknown>).review_count as number ?? 0}
          {(b as Record<string, unknown>).review_average
            ? ` (${((b as Record<string, unknown>).review_average as number).toFixed(1)})`
            : ""}
        </span>
      ),
    },
    {
      key: "assessment",
      header: "Self-Assessment",
      render: (b) => {
        const has = (b as Record<string, unknown>).has_self_assessment;
        const status = (b as Record<string, unknown>).self_assessment_status as string;
        return has ? (
          <Badge variant={status === "approved" ? "success" : status === "submitted" ? "info" : "warning"}>
            {status === "approved" ? "Approvato" : status === "submitted" ? "Inviato" : "Bozza"}
          </Badge>
        ) : (
          <span className="text-xs text-slate-400">Non compilato</span>
        );
      },
    },
    {
      key: "certification",
      header: "Certificazione",
      render: (b) => {
        const valid = (b as Record<string, unknown>).certification_valid;
        const has = (b as Record<string, unknown>).has_certification;
        return has ? (
          <Badge variant={valid ? "success" : "danger"}>
            {valid ? "Valida" : "Scaduta"}
          </Badge>
        ) : (
          <span className="text-xs text-slate-400">Nessuna</span>
        );
      },
    },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-slate-800">Rating & Recensioni</h1>
        <p className="mt-1 text-sm text-slate-500">
          Panoramica dei rating, ancore e certificazioni
        </p>
      </div>

      {/* Tabs */}
      <div className="flex gap-1 border-b border-slate-200">
        <button
          onClick={() => setTab("ratings")}
          className={`border-b-2 px-4 py-2.5 text-sm font-medium transition ${
            tab === "ratings"
              ? "border-sky-600 text-sky-600"
              : "border-transparent text-slate-500 hover:text-slate-700"
          }`}
        >
          Rating Posti Barca
        </button>
        <button
          onClick={() => setTab("reviews")}
          className={`border-b-2 px-4 py-2.5 text-sm font-medium transition ${
            tab === "reviews"
              ? "border-sky-600 text-sky-600"
              : "border-transparent text-slate-500 hover:text-slate-700"
          }`}
        >
          Recensioni
        </button>
      </div>

      {tab === "ratings" && (
        <DataTable
          columns={ratingColumns}
          data={berths as (Berth & Record<string, unknown>)[]}
          loading={loading}
          page={page}
          totalPages={totalPages}
          onPageChange={setPage}
          emptyMessage="Nessun rating trovato"
        />
      )}

      {tab === "reviews" && (
        <div className="rounded-xl border border-dashed border-slate-300 bg-white p-12 text-center">
          <p className="text-slate-400">
            La moderazione delle recensioni sara disponibile a breve.
          </p>
        </div>
      )}
    </div>
  );
}
