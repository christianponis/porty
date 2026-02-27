'use client';

import { useEffect, useState, FormEvent } from 'react';
import { useRouter } from 'next/navigation';
import { useUIStore } from '@/stores/ui';
import Button from '@/components/common/Button';
import Input from '@/components/common/Input';
import * as ownerApi from '@/lib/api/owner';
import * as catalogApi from '@/lib/api/catalog';
import type { Port } from '@/lib/api/types';
import { ArrowLeftIcon } from '@heroicons/react/24/outline';
import Link from 'next/link';

export default function CreateBerthPage() {
  const router = useRouter();
  const { addToast } = useUIStore();
  const [ports, setPorts] = useState<Port[]>([]);
  const [loading, setLoading] = useState(false);
  const [portsLoading, setPortsLoading] = useState(true);
  const [errors, setErrors] = useState<Record<string, string>>({});

  const [form, setForm] = useState({
    name: '',
    description: '',
    port_id: '',
    max_length: '',
    max_beam: '',
    max_draft: '',
    price_per_night: '',
    price_per_month: '',
    sharing_available: false,
    nodi_value_per_day: '',
  });

  useEffect(() => {
    catalogApi
      .getPorts()
      .then(setPorts)
      .catch(() => addToast({ type: 'error', message: 'Errore nel caricamento dei porti' }))
      .finally(() => setPortsLoading(false));
  }, [addToast]);

  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>
  ) => {
    const { name, value, type } = e.target;
    const checked = (e.target as HTMLInputElement).checked;
    setForm((prev) => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value,
    }));
    if (errors[name]) {
      setErrors((prev) => {
        const next = { ...prev };
        delete next[name];
        return next;
      });
    }
  };

  const validate = (): boolean => {
    const errs: Record<string, string> = {};
    if (!form.name.trim()) errs.name = 'Il nome e obbligatorio';
    if (!form.port_id) errs.port_id = 'Seleziona un porto';
    if (!form.max_length || Number(form.max_length) <= 0)
      errs.max_length = 'Inserisci una lunghezza valida';
    if (!form.max_beam || Number(form.max_beam) <= 0)
      errs.max_beam = 'Inserisci una larghezza valida';
    if (!form.max_draft || Number(form.max_draft) <= 0)
      errs.max_draft = 'Inserisci un pescaggio valido';
    if (!form.price_per_night || Number(form.price_per_night) <= 0)
      errs.price_per_night = 'Inserisci un prezzo valido';
    setErrors(errs);
    return Object.keys(errs).length === 0;
  };

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    if (!validate()) return;

    try {
      setLoading(true);
      await ownerApi.createBerth({
        name: form.name,
        description: form.description,
        port: { id: Number(form.port_id) } as any,
        max_length: Number(form.max_length),
        max_beam: Number(form.max_beam),
        max_draft: Number(form.max_draft),
        price_per_night: Number(form.price_per_night),
        sharing_available: form.sharing_available,
      } as any);
      addToast({ type: 'success', message: 'Posto barca creato con successo!' });
      router.push('/owner/berths');
    } catch {
      addToast({ type: 'error', message: 'Errore nella creazione del posto barca' });
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="mx-auto max-w-2xl space-y-6">
      {/* Header */}
      <div className="flex items-center gap-4">
        <Link
          href="/owner/berths"
          className="flex h-9 w-9 items-center justify-center rounded-lg border border-sky-200 bg-white text-slate-500 transition-colors hover:bg-sky-50 hover:text-sky-700"
        >
          <ArrowLeftIcon className="h-4 w-4" />
        </Link>
        <div>
          <h1 className="text-2xl font-bold text-sky-900">Nuovo posto barca</h1>
          <p className="text-sm text-slate-500">Compila i dati per aggiungere un nuovo posto barca</p>
        </div>
      </div>

      {/* Form */}
      <form onSubmit={handleSubmit} className="space-y-6 rounded-xl border border-sky-100 bg-white p-6 shadow-sm">
        {/* Porto */}
        <div className="space-y-1.5">
          <label htmlFor="port_id" className="block text-sm font-medium text-slate-700">
            Porto <span className="text-red-500">*</span>
          </label>
          <select
            id="port_id"
            name="port_id"
            value={form.port_id}
            onChange={handleChange}
            disabled={portsLoading}
            className={`block w-full rounded-lg border px-3 py-2 text-sm text-slate-900 shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-0 ${
              errors.port_id
                ? 'border-red-300 focus:border-red-400 focus:ring-red-200'
                : 'border-slate-200 focus:border-sky-400 focus:ring-sky-200'
            }`}
          >
            <option value="">
              {portsLoading ? 'Caricamento porti...' : 'Seleziona un porto'}
            </option>
            {ports.map((port) => (
              <option key={port.id} value={port.id}>
                {port.name} - {port.city}, {port.region}
              </option>
            ))}
          </select>
          {errors.port_id && <p className="text-xs text-red-600">{errors.port_id}</p>}
        </div>

        {/* Nome */}
        <Input
          label="Nome del posto barca"
          name="name"
          required
          placeholder="es. Posto A12"
          value={form.name}
          onChange={handleChange}
          error={errors.name}
        />

        {/* Descrizione */}
        <div className="space-y-1.5">
          <label htmlFor="description" className="block text-sm font-medium text-slate-700">
            Descrizione
          </label>
          <textarea
            id="description"
            name="description"
            rows={4}
            value={form.description}
            onChange={handleChange}
            placeholder="Descrivi il tuo posto barca..."
            className="block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-900 shadow-sm transition-colors placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200 focus:ring-offset-0"
          />
        </div>

        {/* Dimensioni */}
        <div>
          <h3 className="mb-3 text-sm font-semibold text-slate-700">Dimensioni massime (metri)</h3>
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <Input
              label="Lunghezza"
              name="max_length"
              type="number"
              step="0.1"
              min="0"
              required
              placeholder="es. 12.5"
              value={form.max_length}
              onChange={handleChange}
              error={errors.max_length}
            />
            <Input
              label="Larghezza"
              name="max_beam"
              type="number"
              step="0.1"
              min="0"
              required
              placeholder="es. 4.0"
              value={form.max_beam}
              onChange={handleChange}
              error={errors.max_beam}
            />
            <Input
              label="Pescaggio"
              name="max_draft"
              type="number"
              step="0.1"
              min="0"
              required
              placeholder="es. 2.5"
              value={form.max_draft}
              onChange={handleChange}
              error={errors.max_draft}
            />
          </div>
        </div>

        {/* Prezzi */}
        <div>
          <h3 className="mb-3 text-sm font-semibold text-slate-700">Prezzi</h3>
          <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <Input
              label="Prezzo per notte (EUR)"
              name="price_per_night"
              type="number"
              step="0.01"
              min="0"
              required
              placeholder="es. 50.00"
              value={form.price_per_night}
              onChange={handleChange}
              error={errors.price_per_night}
            />
            <Input
              label="Prezzo per mese (EUR, opzionale)"
              name="price_per_month"
              type="number"
              step="0.01"
              min="0"
              placeholder="es. 1200.00"
              value={form.price_per_month}
              onChange={handleChange}
            />
          </div>
        </div>

        {/* Condivisione */}
        <div className="space-y-3 rounded-lg bg-sky-50/50 p-4">
          <label className="flex items-center gap-3 cursor-pointer">
            <input
              type="checkbox"
              name="sharing_available"
              checked={form.sharing_available}
              onChange={handleChange}
              className="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
            />
            <span className="text-sm font-medium text-slate-700">
              Abilita condivisione posto barca
            </span>
          </label>
          {form.sharing_available && (
            <Input
              label="Valore Nodi per giorno"
              name="nodi_value_per_day"
              type="number"
              step="1"
              min="0"
              placeholder="es. 10"
              value={form.nodi_value_per_day}
              onChange={handleChange}
            />
          )}
        </div>

        {/* Submit */}
        <div className="flex justify-end gap-3 pt-4 border-t border-sky-100">
          <Link href="/owner/berths">
            <Button variant="secondary" type="button">Annulla</Button>
          </Link>
          <Button type="submit" loading={loading}>
            Crea posto barca
          </Button>
        </div>
      </form>
    </div>
  );
}
