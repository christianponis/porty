"use client";

import { useEffect, useState, useCallback } from "react";
import { useUIStore } from "@/stores/ui";
import * as adminApi from "@/lib/api/admin";
import type { Transaction, PaginatedResponse } from "@/lib/api/types";
import DataTable, { Column } from "@/components/common/DataTable";
import StatsCard from "@/components/common/StatsCard";
import Badge from "@/components/common/Badge";
import Select from "@/components/common/Select";
import { formatEur, formatDate } from "@/lib/utils/formatters";
import {
  CurrencyEuroIcon,
  BanknotesIcon,
  SparklesIcon,
  ArrowTrendingDownIcon,
} from "@heroicons/react/24/outline";

const typeLabels: Record<string, string> = {
  payment: "Pagamento",
  refund: "Rimborso",
  commission: "Commissione",
  payout: "Payout",
};

const typeColors: Record<string, "success" | "danger" | "warning" | "info" | "default"> = {
  payment: "success",
  refund: "danger",
  commission: "warning",
  payout: "info",
};

const transStatusLabels: Record<string, string> = {
  pending: "In attesa",
  completed: "Completata",
  failed: "Fallita",
};

const transStatusColors: Record<string, "warning" | "success" | "danger" | "default"> = {
  pending: "warning",
  completed: "success",
  failed: "danger",
};

export default function TransactionsPage() {
  const { addToast } = useUIStore();
  const [transactions, setTransactions] = useState<Transaction[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [typeFilter, setTypeFilter] = useState("");

  const [overview, setOverview] = useState({
    total_revenue: 0,
    total_commissions: 0,
    total_nodi_issued: 0,
    total_nodi_spent: 0,
  });

  useEffect(() => {
    adminApi.getFinancialOverview().then(setOverview).catch(() => {});
  }, []);

  const fetchTransactions = useCallback(async () => {
    try {
      setLoading(true);
      const res: PaginatedResponse<Transaction> = await adminApi.getTransactions(page, {
        type: typeFilter || undefined,
      });
      setTransactions(res.data);
      setTotalPages(res.last_page);
    } catch {
      addToast({ type: "error", message: "Errore nel caricamento transazioni" });
    } finally {
      setLoading(false);
    }
  }, [page, typeFilter, addToast]);

  useEffect(() => {
    fetchTransactions();
  }, [fetchTransactions]);

  const columns: Column<Transaction & Record<string, unknown>>[] = [
    {
      key: "id",
      header: "#",
      render: (t) => <span className="text-xs font-mono text-slate-400">#{t.id}</span>,
    },
    {
      key: "type",
      header: "Tipo",
      render: (t) => (
        <Badge variant={typeColors[t.type] || "default"}>
          {typeLabels[t.type] || t.type}
        </Badge>
      ),
    },
    {
      key: "amount",
      header: "Importo",
      render: (t) => (
        <span className="font-semibold text-slate-800">{formatEur(t.amount)}</span>
      ),
    },
    {
      key: "commission",
      header: "Commissione",
      render: (t) =>
        t.commission_amount ? (
          <span className="text-sm text-amber-600">{formatEur(t.commission_amount)}</span>
        ) : (
          <span className="text-xs text-slate-400">-</span>
        ),
    },
    {
      key: "status",
      header: "Stato",
      render: (t) => (
        <Badge variant={transStatusColors[t.status] || "default"}>
          {transStatusLabels[t.status] || t.status}
        </Badge>
      ),
    },
    {
      key: "booking_id",
      header: "Booking",
      render: (t) =>
        t.booking_id ? (
          <span className="text-xs text-slate-500">#{t.booking_id}</span>
        ) : (
          <span className="text-xs text-slate-400">-</span>
        ),
    },
    {
      key: "created_at",
      header: "Data",
      render: (t) => (
        <span className="text-xs text-slate-400">{formatDate(t.created_at)}</span>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-slate-800">Finanza</h1>
        <p className="mt-1 text-sm text-slate-500">
          Panoramica finanziaria e transazioni
        </p>
      </div>

      {/* KPI Cards */}
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <StatsCard
          title="Revenue Totale"
          value={formatEur(overview.total_revenue)}
          icon={CurrencyEuroIcon}
          color="emerald"
        />
        <StatsCard
          title="Commissioni"
          value={formatEur(overview.total_commissions)}
          icon={BanknotesIcon}
          color="amber"
        />
        <StatsCard
          title="Nodi Emessi"
          value={overview.total_nodi_issued.toLocaleString("it-IT")}
          icon={SparklesIcon}
          color="cyan"
        />
        <StatsCard
          title="Nodi Spesi"
          value={overview.total_nodi_spent.toLocaleString("it-IT")}
          icon={ArrowTrendingDownIcon}
          color="purple"
        />
      </div>

      {/* Filters */}
      <div className="flex items-end gap-4">
        <Select
          options={[
            { value: "payment", label: "Pagamenti" },
            { value: "refund", label: "Rimborsi" },
            { value: "commission", label: "Commissioni" },
            { value: "payout", label: "Payout" },
          ]}
          value={typeFilter}
          onChange={(e) => {
            setTypeFilter(e.target.value);
            setPage(1);
          }}
          placeholder="Tutti i tipi"
          className="w-44"
        />
      </div>

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
