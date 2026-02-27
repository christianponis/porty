'use client';

import { useEffect, useState, useCallback } from 'react';
import { useUIStore } from '@/stores/ui';
import Button from '@/components/common/Button';
import Input from '@/components/common/Input';
import Modal from '@/components/common/Modal';
import DataTable, { Column } from '@/components/common/DataTable';
import * as adminApi from '@/lib/api/admin';
import type { Port, PaginatedResponse } from '@/lib/api/types';
import {
  PlusIcon,
  MagnifyingGlassIcon,
  PencilSquareIcon,
} from '@heroicons/react/24/outline';

interface PortFormData {
  name: string;
  city: string;
  region: string;
  country: string;
  latitude: string;
  longitude: string;
}

const emptyForm: PortFormData = {
  name: '',
  city: '',
  region: '',
  country: 'Italia',
  latitude: '',
  longitude: '',
};

const PAGE_SIZE = 15;

export default function AdminPortsPage() {
  const { addToast } = useUIStore();
  const [ports, setPorts] = useState<Port[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [search, setSearch] = useState('');

  // Modal state
  const [modalOpen, setModalOpen] = useState(false);
  const [editingPort, setEditingPort] = useState<Port | null>(null);
  const [form, setForm] = useState<PortFormData>(emptyForm);
  const [saving, setSaving] = useState(false);

  const fetchPorts = useCallback(async () => {
    try {
      setLoading(true);
      const res: PaginatedResponse<Port> = await adminApi.getPorts(page);
      setPorts(res.results);
      setTotalPages(Math.ceil(res.count / PAGE_SIZE));
    } catch {
      addToast({ type: 'error', message: 'Errore nel caricamento dei porti' });
    } finally {
      setLoading(false);
    }
  }, [page, addToast]);

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
      region: port.region,
      country: port.country,
      latitude: String(port.latitude),
      longitude: String(port.longitude),
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
        region: form.region,
        country: form.country,
        latitude: parseFloat(form.latitude),
        longitude: parseFloat(form.longitude),
      };

      if (editingPort) {
        const updated = await adminApi.updatePort(editingPort.id, payload);
        setPorts((prev) => prev.map((p) => (p.id === editingPort.id ? updated : p)));
        addToast({ type: 'success', message: 'Porto aggiornato con successo' });
      } else {
        const created = await adminApi.createPort(payload);
        setPorts((prev) => [created, ...prev]);
        addToast({ type: 'success', message: 'Porto creato con successo' });
      }
      setModalOpen(false);
    } catch {
      addToast({ type: 'error', message: 'Errore nel salvataggio del porto' });
    } finally {
      setSaving(false);
    }
  };

  const updateField = (field: keyof PortFormData, value: string) => {
    setForm((prev) => ({ ...prev, [field]: value }));
  };

  const filteredPorts = search.trim()
    ? ports.filter(
        (p) =>
          p.name.toLowerCase().includes(search.toLowerCase()) ||
          p.city.toLowerCase().includes(search.toLowerCase()) ||
          p.region.toLowerCase().includes(search.toLowerCase())
      )
    : ports;

  const columns: Column<Port & Record<string, unknown>>[] = [
    {
      key: 'name',
      header: 'Nome',
      render: (p) => <span className="font-medium text-slate-800">{p.name}</span>,
    },
    {
      key: 'city',
      header: 'Citta',
      render: (p) => <span className="text-slate-600">{p.city}</span>,
    },
    {
      key: 'region',
      header: 'Regione',
      render: (p) => <span className="text-slate-500">{p.region}</span>,
    },
    {
      key: 'country',
      header: 'Paese',
      render: (p) => <span className="text-slate-500">{p.country}</span>,
    },
    {
      key: 'total_berths',
      header: 'Posti barca',
      render: (p) => (
        <span className="font-medium text-sky-700">{p.total_berths}</span>
      ),
    },
    {
      key: 'actions',
      header: 'Azioni',
      render: (p) => (
        <button
          onClick={() => openEdit(p as Port)}
          className="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-medium text-sky-700 transition-colors hover:bg-sky-50"
        >
          <PencilSquareIcon className="h-3.5 w-3.5" />
          Modifica
        </button>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 className="text-2xl font-bold text-sky-900">Gestione Porti</h1>
          <p className="mt-1 text-sm text-slate-500">
            Aggiungi, modifica e gestisci i porti della piattaforma.
          </p>
        </div>
        <Button onClick={openCreate}>
          <PlusIcon className="h-4 w-4" />
          Aggiungi porto
        </Button>
      </div>

      {/* Search */}
      <div className="relative max-w-md">
        <MagnifyingGlassIcon className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
        <Input
          placeholder="Cerca per nome, citta o regione..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="pl-9"
        />
      </div>

      {/* Table */}
      <DataTable
        columns={columns}
        data={filteredPorts as (Port & Record<string, unknown>)[]}
        loading={loading}
        page={page}
        totalPages={totalPages}
        onPageChange={setPage}
        emptyMessage="Nessun porto trovato"
      />

      {/* Add/Edit Modal */}
      <Modal
        open={modalOpen}
        onClose={() => setModalOpen(false)}
        title={editingPort ? 'Modifica porto' : 'Nuovo porto'}
        footer={
          <>
            <Button variant="secondary" onClick={() => setModalOpen(false)}>
              Annulla
            </Button>
            <Button onClick={handleSubmit} loading={saving}>
              {editingPort ? 'Salva modifiche' : 'Crea porto'}
            </Button>
          </>
        }
      >
        <form onSubmit={handleSubmit} className="space-y-4">
          <Input
            label="Nome porto"
            name="name"
            value={form.name}
            onChange={(e) => updateField('name', e.target.value)}
            required
            placeholder="es. Porto di Genova"
          />
          <div className="grid grid-cols-2 gap-4">
            <Input
              label="Citta"
              name="city"
              value={form.city}
              onChange={(e) => updateField('city', e.target.value)}
              required
              placeholder="es. Genova"
            />
            <Input
              label="Regione"
              name="region"
              value={form.region}
              onChange={(e) => updateField('region', e.target.value)}
              required
              placeholder="es. Liguria"
            />
          </div>
          <Input
            label="Paese"
            name="country"
            value={form.country}
            onChange={(e) => updateField('country', e.target.value)}
            required
            placeholder="es. Italia"
          />
          <div className="grid grid-cols-2 gap-4">
            <Input
              label="Latitudine"
              name="latitude"
              type="number"
              step="any"
              value={form.latitude}
              onChange={(e) => updateField('latitude', e.target.value)}
              required
              placeholder="es. 44.4056"
            />
            <Input
              label="Longitudine"
              name="longitude"
              type="number"
              step="any"
              value={form.longitude}
              onChange={(e) => updateField('longitude', e.target.value)}
              required
              placeholder="es. 8.9463"
            />
          </div>
        </form>
      </Modal>
    </div>
  );
}
