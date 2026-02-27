"use client";

import { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import { useUIStore } from "@/stores/ui";
import * as adminApi from "@/lib/api/admin";
import type { Convention, Port, CategoryOption } from "@/lib/api/types";
import Input from "@/components/common/Input";
import Select from "@/components/common/Select";
import Button from "@/components/common/Button";
import Badge from "@/components/common/Badge";
import ConfirmDialog from "@/components/common/ConfirmDialog";
import { formatDate } from "@/lib/utils/formatters";
import { conventionCategoryColors } from "@/lib/utils/constants";
import { ArrowLeftIcon, TrashIcon } from "@heroicons/react/24/outline";
import Link from "next/link";

export default function ConventionDetailPage() {
  const params = useParams();
  const router = useRouter();
  const convId = Number(params.id);
  const { addToast } = useUIStore();

  const [convention, setConvention] = useState<Convention | null>(null);
  const [categories, setCategories] = useState<CategoryOption[]>([]);
  const [ports, setPorts] = useState<Port[]>([]);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [deleteOpen, setDeleteOpen] = useState(false);
  const [deleting, setDeleting] = useState(false);

  const [form, setForm] = useState({
    port_id: "",
    name: "",
    description: "",
    category: "",
    address: "",
    phone: "",
    email: "",
    website: "",
    discount_type: "",
    discount_value: "",
    discount_description: "",
    latitude: "",
    longitude: "",
    is_active: true,
    valid_from: "",
    valid_until: "",
    sort_order: "0",
  });

  useEffect(() => {
    const load = async () => {
      try {
        const [convRes, cats, portsRes] = await Promise.all([
          adminApi.getConvention(convId),
          adminApi.getConventionCategories(),
          adminApi.getPorts(1, ""),
        ]);
        const c = (convRes as { data?: Convention }).data || (convRes as Convention);
        setConvention(c);
        setCategories(cats);
        setPorts(portsRes.data || []);
        setForm({
          port_id: String(c.port_id),
          name: c.name,
          description: c.description || "",
          category: c.category,
          address: c.address || "",
          phone: c.phone || "",
          email: c.email || "",
          website: c.website || "",
          discount_type: c.discount_type,
          discount_value: c.discount_value ? String(c.discount_value) : "",
          discount_description: c.discount_description || "",
          latitude: c.latitude ? String(c.latitude) : "",
          longitude: c.longitude ? String(c.longitude) : "",
          is_active: c.is_active,
          valid_from: c.valid_from || "",
          valid_until: c.valid_until || "",
          sort_order: String(c.sort_order),
        });
      } catch {
        addToast({ type: "error", message: "Errore nel caricamento" });
      } finally {
        setLoading(false);
      }
    };
    if (convId) load();
  }, [convId, addToast]);

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      setSaving(true);
      await adminApi.updateConvention(convId, {
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
      } as Partial<Convention>);
      addToast({ type: "success", message: "Convenzione aggiornata" });
    } catch {
      addToast({ type: "error", message: "Errore nel salvataggio" });
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async () => {
    try {
      setDeleting(true);
      await adminApi.deleteConvention(convId);
      addToast({ type: "success", message: "Convenzione eliminata" });
      router.push("/conventions");
    } catch {
      addToast({ type: "error", message: "Errore nell'eliminazione" });
    } finally {
      setDeleting(false);
    }
  };

  const update = (field: string, value: string | boolean) => {
    setForm((prev) => ({ ...prev, [field]: value }));
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center py-20">
        <div className="h-8 w-8 animate-spin rounded-full border-4 border-sky-200 border-t-sky-600" />
      </div>
    );
  }

  if (!convention) return <p className="py-10 text-center text-slate-400">Convenzione non trovata</p>;

  return (
    <div className="mx-auto max-w-3xl space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-3">
          <Link
            href="/conventions"
            className="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
          >
            <ArrowLeftIcon className="h-5 w-5" />
          </Link>
          <div>
            <h1 className="text-xl font-bold text-slate-800">{convention.name}</h1>
            <div className="mt-0.5 flex items-center gap-2">
              <Badge variant={conventionCategoryColors[convention.category] || "default"}>
                {convention.category_label}
              </Badge>
              <Badge variant={convention.is_active ? "success" : "default"}>
                {convention.is_active ? "Attiva" : "Inattiva"}
              </Badge>
            </div>
          </div>
        </div>
        <Button variant="danger" size="sm" onClick={() => setDeleteOpen(true)}>
          <TrashIcon className="h-4 w-4" />
          Elimina
        </Button>
      </div>

      {/* Edit Form */}
      <form onSubmit={handleSave} className="space-y-6 rounded-xl border border-slate-200 bg-white p-6">
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <Select
            label="Porto"
            options={ports.map((p) => ({ value: String(p.id), label: `${p.name} - ${p.city}` }))}
            value={form.port_id}
            onChange={(e) => update("port_id", e.target.value)}
          />
          <Input
            label="Nome attivita"
            value={form.name}
            onChange={(e) => update("name", e.target.value)}
            required
          />
        </div>

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <Select
            label="Categoria"
            options={categories.map((c) => ({ value: c.value, label: c.label }))}
            value={form.category}
            onChange={(e) => update("category", e.target.value)}
          />
          <Select
            label="Tipo sconto"
            options={[
              { value: "percentage", label: "Percentuale (%)" },
              { value: "fixed", label: "Fisso (EUR)" },
              { value: "free", label: "Gratuito" },
            ]}
            value={form.discount_type}
            onChange={(e) => update("discount_type", e.target.value)}
          />
          {form.discount_type !== "free" && (
            <Input
              label={form.discount_type === "percentage" ? "Valore (%)" : "Valore (EUR)"}
              type="number"
              step="0.01"
              value={form.discount_value}
              onChange={(e) => update("discount_value", e.target.value)}
            />
          )}
        </div>

        <Input
          label="Descrizione sconto"
          value={form.discount_description}
          onChange={(e) => update("discount_description", e.target.value)}
        />

        <div>
          <label className="mb-1.5 block text-sm font-medium text-slate-700">Descrizione</label>
          <textarea
            value={form.description}
            onChange={(e) => update("description", e.target.value)}
            rows={3}
            className="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 placeholder-slate-400 transition focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20"
          />
        </div>

        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <Input label="Indirizzo" value={form.address} onChange={(e) => update("address", e.target.value)} />
          <Input label="Telefono" value={form.phone} onChange={(e) => update("phone", e.target.value)} />
          <Input label="Email" type="email" value={form.email} onChange={(e) => update("email", e.target.value)} />
          <Input label="Sito web" value={form.website} onChange={(e) => update("website", e.target.value)} />
        </div>

        <div className="grid grid-cols-2 gap-4 sm:grid-cols-4">
          <Input label="Latitudine" type="number" step="any" value={form.latitude} onChange={(e) => update("latitude", e.target.value)} />
          <Input label="Longitudine" type="number" step="any" value={form.longitude} onChange={(e) => update("longitude", e.target.value)} />
          <Input label="Valida dal" type="date" value={form.valid_from} onChange={(e) => update("valid_from", e.target.value)} />
          <Input label="Valida fino al" type="date" value={form.valid_until} onChange={(e) => update("valid_until", e.target.value)} />
        </div>

        <div className="flex items-center justify-between border-t border-slate-100 pt-5">
          <div className="flex items-center gap-4">
            <Input
              label="Ordine"
              type="number"
              value={form.sort_order}
              onChange={(e) => update("sort_order", e.target.value)}
              className="w-20"
            />
            <label className="flex items-center gap-2 text-sm text-slate-700">
              <input
                type="checkbox"
                checked={form.is_active}
                onChange={(e) => update("is_active", e.target.checked)}
                className="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
              />
              Attiva
            </label>
          </div>
          <Button type="submit" loading={saving}>
            Salva modifiche
          </Button>
        </div>
      </form>

      <ConfirmDialog
        open={deleteOpen}
        onClose={() => setDeleteOpen(false)}
        onConfirm={handleDelete}
        title="Elimina convenzione"
        message={`Sei sicuro di voler eliminare "${convention.name}"? Questa azione non puo essere annullata.`}
        confirmLabel="Elimina"
        loading={deleting}
      />
    </div>
  );
}
