"use client";

import { useEffect, useState, useCallback } from "react";
import { useUIStore } from "@/stores/ui";
import * as adminApi from "@/lib/api/admin";
import type { Convention, Port, PaginatedResponse, CategoryOption } from "@/lib/api/types";
import DataTable, { Column } from "@/components/common/DataTable";
import SearchInput from "@/components/common/SearchInput";
import Select from "@/components/common/Select";
import Badge from "@/components/common/Badge";
import Button from "@/components/common/Button";
import Modal from "@/components/common/Modal";
import Input from "@/components/common/Input";
import ConfirmDialog from "@/components/common/ConfirmDialog";
import { formatDate } from "@/lib/utils/formatters";
import {
  conventionCategoryColors,
  discountTypeLabels,
} from "@/lib/utils/constants";
import {
  PlusIcon,
  PencilSquareIcon,
  TrashIcon,
} from "@heroicons/react/24/outline";

interface ConventionFormData {
  port_id: string;
  name: string;
  description: string;
  category: string;
  address: string;
  phone: string;
  email: string;
  website: string;
  discount_type: string;
  discount_value: string;
  discount_description: string;
  latitude: string;
  longitude: string;
  is_active: boolean;
  valid_from: string;
  valid_until: string;
  sort_order: string;
}

const emptyForm: ConventionFormData = {
  port_id: "",
  name: "",
  description: "",
  category: "commercial",
  address: "",
  phone: "",
  email: "",
  website: "",
  discount_type: "percentage",
  discount_value: "",
  discount_description: "",
  latitude: "",
  longitude: "",
  is_active: true,
  valid_from: "",
  valid_until: "",
  sort_order: "0",
};

export default function ConventionsPage() {
  const { addToast } = useUIStore();
  const [conventions, setConventions] = useState<Convention[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [search, setSearch] = useState("");
  const [categoryFilter, setCategoryFilter] = useState("");

  const [categories, setCategories] = useState<CategoryOption[]>([]);
  const [ports, setPorts] = useState<Port[]>([]);

  const [modalOpen, setModalOpen] = useState(false);
  const [editingConvention, setEditingConvention] = useState<Convention | null>(null);
  const [form, setForm] = useState<ConventionFormData>(emptyForm);
  const [saving, setSaving] = useState(false);

  const [deleteTarget, setDeleteTarget] = useState<Convention | null>(null);
  const [deleting, setDeleting] = useState(false);

  // Load categories and ports on mount
  useEffect(() => {
    adminApi.getConventionCategories().then(setCategories).catch(() => {});
    adminApi.getPorts(1, "").then((res) => {
      // Load all ports for the selector
      const allPorts: Port[] = res.data || [];
      setPorts(allPorts);
    }).catch(() => {});
  }, []);

  const fetchConventions = useCallback(async () => {
    try {
      setLoading(true);
      const res: PaginatedResponse<Convention> = await adminApi.getConventions(page, {
        search: search || undefined,
        category: categoryFilter || undefined,
      });
      setConventions(res.data);
      setTotalPages(res.last_page);
    } catch {
      addToast({ type: "error", message: "Errore nel caricamento convenzioni" });
    } finally {
      setLoading(false);
    }
  }, [page, search, categoryFilter, addToast]);

  useEffect(() => {
    fetchConventions();
  }, [fetchConventions]);

  const openCreate = () => {
    setEditingConvention(null);
    setForm(emptyForm);
    setModalOpen(true);
  };

  const openEdit = (conv: Convention) => {
    setEditingConvention(conv);
    setForm({
      port_id: String(conv.port_id),
      name: conv.name,
      description: conv.description || "",
      category: conv.category,
      address: conv.address || "",
      phone: conv.phone || "",
      email: conv.email || "",
      website: conv.website || "",
      discount_type: conv.discount_type,
      discount_value: conv.discount_value ? String(conv.discount_value) : "",
      discount_description: conv.discount_description || "",
      latitude: conv.latitude ? String(conv.latitude) : "",
      longitude: conv.longitude ? String(conv.longitude) : "",
      is_active: conv.is_active,
      valid_from: conv.valid_from || "",
      valid_until: conv.valid_until || "",
      sort_order: String(conv.sort_order),
    });
    setModalOpen(true);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      setSaving(true);
      const payload = {
        port_id: parseInt(form.port_id),
        name: form.name,
        description: form.description || null,
        category: form.category,
        address: form.address || null,
        phone: form.phone || null,
        email: form.email || null,
        website: form.website || null,
        discount_type: form.discount_type,
        discount_value: form.discount_value ? parseFloat(form.discount_value) : null,
        discount_description: form.discount_description || null,
        latitude: form.latitude ? parseFloat(form.latitude) : null,
        longitude: form.longitude ? parseFloat(form.longitude) : null,
        is_active: form.is_active,
        valid_from: form.valid_from || null,
        valid_until: form.valid_until || null,
        sort_order: parseInt(form.sort_order) || 0,
      };

      if (editingConvention) {
        await adminApi.updateConvention(editingConvention.id, payload);
        addToast({ type: "success", message: "Convenzione aggiornata" });
      } else {
        await adminApi.createConvention(payload);
        addToast({ type: "success", message: "Convenzione creata" });
      }
      setModalOpen(false);
      fetchConventions();
    } catch {
      addToast({ type: "error", message: "Errore nel salvataggio" });
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async () => {
    if (!deleteTarget) return;
    try {
      setDeleting(true);
      await adminApi.deleteConvention(deleteTarget.id);
      addToast({ type: "success", message: "Convenzione eliminata" });
      setDeleteTarget(null);
      fetchConventions();
    } catch {
      addToast({ type: "error", message: "Errore nell'eliminazione" });
    } finally {
      setDeleting(false);
    }
  };

  const updateField = (field: keyof ConventionFormData, value: string | boolean) => {
    setForm((prev) => ({ ...prev, [field]: value }));
  };

  const formatDiscount = (conv: Convention) => {
    if (conv.discount_type === "free") return "Gratuito";
    if (conv.discount_type === "percentage") return `${conv.discount_value}%`;
    return `${conv.discount_value} EUR`;
  };

  const columns: Column<Convention & Record<string, unknown>>[] = [
    {
      key: "name",
      header: "Nome",
      render: (c) => (
        <div>
          <p className="font-medium text-slate-800">{c.name}</p>
          {c.address && <p className="text-xs text-slate-400">{c.address}</p>}
        </div>
      ),
    },
    {
      key: "port",
      header: "Porto",
      render: (c) => (
        <span className="text-slate-600">{(c.port as { name?: string })?.name || "N/D"}</span>
      ),
    },
    {
      key: "category",
      header: "Categoria",
      render: (c) => (
        <Badge variant={conventionCategoryColors[c.category] || "default"}>
          {c.category_label}
        </Badge>
      ),
    },
    {
      key: "discount",
      header: "Sconto",
      render: (c) => (
        <span className="font-medium text-emerald-600">
          {formatDiscount(c as Convention)}
        </span>
      ),
    },
    {
      key: "valid_until",
      header: "Validita",
      render: (c) => (
        <span className="text-xs text-slate-500">
          {c.valid_until ? `Fino al ${formatDate(c.valid_until)}` : "Illimitata"}
        </span>
      ),
    },
    {
      key: "is_active",
      header: "Stato",
      render: (c) => (
        <Badge variant={c.is_active ? "success" : "default"}>
          {c.is_active ? "Attiva" : "Inattiva"}
        </Badge>
      ),
    },
    {
      key: "actions",
      header: "",
      className: "text-right",
      render: (c) => (
        <div className="flex items-center justify-end gap-1">
          <button
            onClick={() => openEdit(c as Convention)}
            className="rounded-lg p-1.5 text-slate-400 transition hover:bg-sky-50 hover:text-sky-600"
          >
            <PencilSquareIcon className="h-4 w-4" />
          </button>
          <button
            onClick={() => setDeleteTarget(c as Convention)}
            className="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-500"
          >
            <TrashIcon className="h-4 w-4" />
          </button>
        </div>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-800">Convenzioni Porto</h1>
          <p className="mt-1 text-sm text-slate-500">
            Gestisci le convenzioni con servizi e attivita locali
          </p>
        </div>
        <Button onClick={openCreate}>
          <PlusIcon className="h-4 w-4" />
          Nuova Convenzione
        </Button>
      </div>

      <div className="flex flex-wrap items-end gap-4">
        <SearchInput
          value={search}
          onChange={(v) => {
            setSearch(v);
            setPage(1);
          }}
          placeholder="Cerca per nome..."
          className="w-64"
        />
        <Select
          options={categories.map((c) => ({ value: c.value, label: c.label }))}
          value={categoryFilter}
          onChange={(e) => {
            setCategoryFilter(e.target.value);
            setPage(1);
          }}
          placeholder="Tutte le categorie"
          className="w-44"
        />
      </div>

      <DataTable
        columns={columns}
        data={conventions as (Convention & Record<string, unknown>)[]}
        loading={loading}
        page={page}
        totalPages={totalPages}
        onPageChange={setPage}
        emptyMessage="Nessuna convenzione trovata"
      />

      {/* Create/Edit Modal */}
      <Modal
        open={modalOpen}
        onClose={() => setModalOpen(false)}
        title={editingConvention ? "Modifica Convenzione" : "Nuova Convenzione"}
        size="xl"
        footer={
          <>
            <Button variant="secondary" onClick={() => setModalOpen(false)}>
              Annulla
            </Button>
            <Button onClick={handleSubmit} loading={saving}>
              {editingConvention ? "Salva modifiche" : "Crea convenzione"}
            </Button>
          </>
        }
      >
        <form onSubmit={handleSubmit} className="space-y-5">
          {/* Porto + Nome */}
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <Select
              label="Porto"
              options={ports.map((p) => ({ value: String(p.id), label: `${p.name} - ${p.city}` }))}
              value={form.port_id}
              onChange={(e) => updateField("port_id", e.target.value)}
              placeholder="Seleziona porto"
            />
            <Input
              label="Nome attivita"
              value={form.name}
              onChange={(e) => updateField("name", e.target.value)}
              required
              placeholder="es. Ristorante Il Faro"
            />
          </div>

          {/* Categoria + Tipo sconto */}
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <Select
              label="Categoria"
              options={categories.map((c) => ({ value: c.value, label: c.label }))}
              value={form.category}
              onChange={(e) => updateField("category", e.target.value)}
            />
            <Select
              label="Tipo sconto"
              options={[
                { value: "percentage", label: "Percentuale (%)" },
                { value: "fixed", label: "Fisso (EUR)" },
                { value: "free", label: "Gratuito" },
              ]}
              value={form.discount_type}
              onChange={(e) => updateField("discount_type", e.target.value)}
            />
            {form.discount_type !== "free" && (
              <Input
                label={form.discount_type === "percentage" ? "Valore (%)" : "Valore (EUR)"}
                type="number"
                step="0.01"
                value={form.discount_value}
                onChange={(e) => updateField("discount_value", e.target.value)}
                placeholder={form.discount_type === "percentage" ? "es. 10" : "es. 5.00"}
              />
            )}
          </div>

          <Input
            label="Descrizione sconto"
            value={form.discount_description}
            onChange={(e) => updateField("discount_description", e.target.value)}
            placeholder="es. 10% su tutti i prodotti con carta Porty"
          />

          {/* Descrizione */}
          <div>
            <label className="mb-1.5 block text-sm font-medium text-slate-700">
              Descrizione
            </label>
            <textarea
              value={form.description}
              onChange={(e) => updateField("description", e.target.value)}
              rows={3}
              className="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 transition focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20"
              placeholder="Descrizione dell'attivita o servizio..."
            />
          </div>

          {/* Contatti */}
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <Input
              label="Indirizzo"
              value={form.address}
              onChange={(e) => updateField("address", e.target.value)}
              placeholder="Via Roma 1, 16100 Genova"
            />
            <Input
              label="Telefono"
              value={form.phone}
              onChange={(e) => updateField("phone", e.target.value)}
              placeholder="+39 010 123456"
            />
            <Input
              label="Email"
              type="email"
              value={form.email}
              onChange={(e) => updateField("email", e.target.value)}
              placeholder="info@attivita.it"
            />
            <Input
              label="Sito web"
              value={form.website}
              onChange={(e) => updateField("website", e.target.value)}
              placeholder="https://www.attivita.it"
            />
          </div>

          {/* Coordinate + Validita */}
          <div className="grid grid-cols-2 gap-4 sm:grid-cols-4">
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
            <Input
              label="Valida dal"
              type="date"
              value={form.valid_from}
              onChange={(e) => updateField("valid_from", e.target.value)}
            />
            <Input
              label="Valida fino al"
              type="date"
              value={form.valid_until}
              onChange={(e) => updateField("valid_until", e.target.value)}
            />
          </div>

          {/* Ordine + Attiva */}
          <div className="flex items-center gap-6">
            <Input
              label="Ordine"
              type="number"
              value={form.sort_order}
              onChange={(e) => updateField("sort_order", e.target.value)}
              className="w-24"
            />
            <label className="flex items-center gap-2 text-sm text-slate-700">
              <input
                type="checkbox"
                checked={form.is_active}
                onChange={(e) => updateField("is_active", e.target.checked)}
                className="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
              />
              Attiva
            </label>
          </div>
        </form>
      </Modal>

      {/* Delete Confirmation */}
      <ConfirmDialog
        open={!!deleteTarget}
        onClose={() => setDeleteTarget(null)}
        onConfirm={handleDelete}
        title="Elimina convenzione"
        message={`Sei sicuro di voler eliminare la convenzione "${deleteTarget?.name}"? Questa azione non puo essere annullata.`}
        confirmLabel="Elimina"
        loading={deleting}
      />
    </div>
  );
}
