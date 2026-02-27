"use client";

import { useEffect, useState, useCallback } from "react";
import { useUIStore } from "@/stores/ui";
import * as adminApi from "@/lib/api/admin";
import type { Port, PaginatedResponse } from "@/lib/api/types";
import Button from "@/components/common/Button";
import Input from "@/components/common/Input";
import Modal from "@/components/common/Modal";
import DataTable, { Column } from "@/components/common/DataTable";
import SearchInput from "@/components/common/SearchInput";
import Badge from "@/components/common/Badge";
import {
  PlusIcon,
  PencilSquareIcon,
  EyeIcon,
} from "@heroicons/react/24/outline";
import Link from "next/link";

interface PortFormData {
  name: string;
  city: string;
  province: string;
  region: string;
  country: string;
  latitude: string;
  longitude: string;
  description: string;
}

const emptyForm: PortFormData = {
  name: "",
  city: "",
  province: "",
  region: "",
  country: "Italia",
  latitude: "",
  longitude: "",
  description: "",
};

export default function PortsPage() {
  const { addToast } = useUIStore();
  const [ports, setPorts] = useState<Port[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [search, setSearch] = useState("");

  const [modalOpen, setModalOpen] = useState(false);
  const [editingPort, setEditingPort] = useState<Port | null>(null);
  const [form, setForm] = useState<PortFormData>(emptyForm);
  const [saving, setSaving] = useState(false);

  const fetchPorts = useCallback(async () => {
    try {
      setLoading(true);
      const res: PaginatedResponse<Port> = await adminApi.getPorts(page, search);
      setPorts(res.data);
      setTotalPages(res.last_page);
    } catch {
      addToast({ type: "error", message: "Errore nel caricamento dei porti" });
    } finally {
      setLoading(false);
    }
  }, [page, search, addToast]);

  useEffect(() => {
    fetchPorts();
  }, [fetchPorts]);

  const openCreate = () => {
    setEditingPort(null);
    setForm(emptyForm);
    setModalOpen(true);
  };

  const openEdit = (port: Port) => {
    setEditingPort(port);
    setForm({
      name: port.name,
      city: port.city,
      province: port.province || "",
      region: port.region,
      country: port.country,
      latitude: String(port.latitude || ""),
      longitude: String(port.longitude || ""),
      description: port.description || "",
    });
    setModalOpen(true);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      setSaving(true);
      const payload = {
        name: form.name,
        city: form.city,
        province: form.province,
        region: form.region,
        country: form.country,
        latitude: form.latitude ? parseFloat(form.latitude) : null,
        longitude: form.longitude ? parseFloat(form.longitude) : null,
        description: form.description,
      };

      if (editingPort) {
        await adminApi.updatePort(editingPort.id, payload);
        addToast({ type: "success", message: "Porto aggiornato" });
      } else {
        await adminApi.createPort(payload);
        addToast({ type: "success", message: "Porto creato" });
      }
      setModalOpen(false);
      fetchPorts();
    } catch {
      addToast({ type: "error", message: "Errore nel salvataggio" });
    } finally {
      setSaving(false);
    }
  };

  const updateField = (field: keyof PortFormData, value: string) => {
    setForm((prev) => ({ ...prev, [field]: value }));
  };

  const columns: Column<Port & Record<string, unknown>>[] = [
    {
      key: "name",
      header: "Nome",
      render: (p) => (
        <div>
          <p className="font-medium text-slate-800">{p.name}</p>
          <p className="text-xs text-slate-400">{p.city}</p>
        </div>
      ),
    },
    {
      key: "region",
      header: "Regione",
      render: (p) => <span className="text-slate-600">{p.region}</span>,
    },
    {
      key: "country",
      header: "Paese",
      render: (p) => <span className="text-slate-500">{p.country}</span>,
    },
    {
      key: "total_berths",
      header: "Posti barca",
      render: (p) => (
        <Badge variant="primary">{p.total_berths ?? 0}</Badge>
      ),
    },
    {
      key: "is_active",
      header: "Stato",
      render: (p) => (
        <Badge variant={p.is_active ? "success" : "default"}>
          {p.is_active ? "Attivo" : "Inattivo"}
        </Badge>
      ),
    },
    {
      key: "actions",
      header: "",
      className: "text-right",
      render: (p) => (
        <div className="flex items-center justify-end gap-1">
          <Link
            href={`/ports/${p.id}`}
            className="rounded-lg p-1.5 text-slate-400 transition hover:bg-sky-50 hover:text-sky-600"
          >
            <EyeIcon className="h-4 w-4" />
          </Link>
          <button
            onClick={() => openEdit(p as Port)}
            className="rounded-lg p-1.5 text-slate-400 transition hover:bg-sky-50 hover:text-sky-600"
          >
            <PencilSquareIcon className="h-4 w-4" />
          </button>
        </div>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-800">Gestione Porti</h1>
          <p className="mt-1 text-sm text-slate-500">
            {ports.length > 0
              ? `${ports.length} porti visualizzati`
              : "Nessun porto"}
          </p>
        </div>
        <Button onClick={openCreate}>
          <PlusIcon className="h-4 w-4" />
          Aggiungi porto
        </Button>
      </div>

      <SearchInput
        value={search}
        onChange={(v) => {
          setSearch(v);
          setPage(1);
        }}
        placeholder="Cerca per nome, citta o regione..."
        className="max-w-md"
      />

      <DataTable
        columns={columns}
        data={ports as (Port & Record<string, unknown>)[]}
        loading={loading}
        page={page}
        totalPages={totalPages}
        onPageChange={setPage}
        emptyMessage="Nessun porto trovato"
      />

      <Modal
        open={modalOpen}
        onClose={() => setModalOpen(false)}
        title={editingPort ? "Modifica porto" : "Nuovo porto"}
        size="lg"
        footer={
          <>
            <Button variant="secondary" onClick={() => setModalOpen(false)}>
              Annulla
            </Button>
            <Button onClick={handleSubmit} loading={saving}>
              {editingPort ? "Salva modifiche" : "Crea porto"}
            </Button>
          </>
        }
      >
        <form onSubmit={handleSubmit} className="space-y-4">
          <Input
            label="Nome porto"
            value={form.name}
            onChange={(e) => updateField("name", e.target.value)}
            required
            placeholder="es. Marina di Genova"
          />
          <div className="grid grid-cols-2 gap-4">
            <Input
              label="Citta"
              value={form.city}
              onChange={(e) => updateField("city", e.target.value)}
              required
            />
            <Input
              label="Provincia"
              value={form.province}
              onChange={(e) => updateField("province", e.target.value)}
            />
          </div>
          <div className="grid grid-cols-2 gap-4">
            <Input
              label="Regione"
              value={form.region}
              onChange={(e) => updateField("region", e.target.value)}
              required
            />
            <Input
              label="Paese"
              value={form.country}
              onChange={(e) => updateField("country", e.target.value)}
              required
            />
          </div>
          <div className="grid grid-cols-2 gap-4">
            <Input
              label="Latitudine"
              type="number"
              step="any"
              value={form.latitude}
              onChange={(e) => updateField("latitude", e.target.value)}
            />
            <Input
              label="Longitudine"
              type="number"
              step="any"
              value={form.longitude}
              onChange={(e) => updateField("longitude", e.target.value)}
            />
          </div>
          <div>
            <label className="mb-1.5 block text-sm font-medium text-slate-700">
              Descrizione
            </label>
            <textarea
              value={form.description}
              onChange={(e) => updateField("description", e.target.value)}
              rows={3}
              className="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 transition focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20"
            />
          </div>
        </form>
      </Modal>
    </div>
  );
}
