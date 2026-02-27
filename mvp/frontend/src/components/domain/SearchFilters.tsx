'use client';

import { useState } from 'react';
import Button from '@/components/common/Button';
import Input from '@/components/common/Input';

export interface FilterValues {
  country?: string;
  region?: string;
  min_price?: number;
  max_price?: number;
  min_length?: number;
  max_length?: number;
  min_rating?: number;
  sharing?: boolean;
}

interface SearchFiltersProps {
  onFilter: (filters: FilterValues) => void;
  initialValues?: FilterValues;
}

const countries = [
  { value: '', label: 'Tutti i paesi' },
  { value: 'IT', label: 'Italia' },
  { value: 'FR', label: 'Francia' },
  { value: 'ES', label: 'Spagna' },
  { value: 'HR', label: 'Croazia' },
  { value: 'GR', label: 'Grecia' },
  { value: 'MT', label: 'Malta' },
  { value: 'ME', label: 'Montenegro' },
];

export default function SearchFilters({
  onFilter,
  initialValues = {},
}: SearchFiltersProps) {
  const [filters, setFilters] = useState<FilterValues>(initialValues);

  function update(key: keyof FilterValues, value: string | number | boolean) {
    setFilters((prev) => ({ ...prev, [key]: value }));
  }

  function handleApply() {
    // Clean empty values
    const cleaned: FilterValues = {};
    Object.entries(filters).forEach(([key, val]) => {
      if (val !== '' && val !== undefined && val !== null) {
        (cleaned as Record<string, unknown>)[key] = val;
      }
    });
    onFilter(cleaned);
  }

  function handleReset() {
    setFilters({});
    onFilter({});
  }

  return (
    <div className="rounded-2xl border border-sky-100 bg-white p-5 shadow-sm">
      <h3 className="mb-4 text-sm font-semibold text-sky-900">Filtri</h3>

      <div className="space-y-4">
        {/* Country */}
        <div className="space-y-1.5">
          <label className="block text-sm font-medium text-slate-700">
            Paese
          </label>
          <select
            value={filters.country || ''}
            onChange={(e) => update('country', e.target.value)}
            className="block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200"
          >
            {countries.map((c) => (
              <option key={c.value} value={c.value}>
                {c.label}
              </option>
            ))}
          </select>
        </div>

        {/* Region */}
        <Input
          label="Regione"
          name="region"
          placeholder="Es. Liguria, Sardegna..."
          value={filters.region || ''}
          onChange={(e) => update('region', e.target.value)}
        />

        {/* Price Range */}
        <div className="grid grid-cols-2 gap-3">
          <Input
            label="Prezzo min"
            name="min_price"
            type="number"
            placeholder="0"
            value={filters.min_price ?? ''}
            onChange={(e) =>
              update('min_price', e.target.value ? Number(e.target.value) : '')
            }
          />
          <Input
            label="Prezzo max"
            name="max_price"
            type="number"
            placeholder="999"
            value={filters.max_price ?? ''}
            onChange={(e) =>
              update('max_price', e.target.value ? Number(e.target.value) : '')
            }
          />
        </div>

        {/* Length Range */}
        <div className="grid grid-cols-2 gap-3">
          <Input
            label="Lungh. min (m)"
            name="min_length"
            type="number"
            placeholder="0"
            value={filters.min_length ?? ''}
            onChange={(e) =>
              update('min_length', e.target.value ? Number(e.target.value) : '')
            }
          />
          <Input
            label="Lungh. max (m)"
            name="max_length"
            type="number"
            placeholder="50"
            value={filters.max_length ?? ''}
            onChange={(e) =>
              update('max_length', e.target.value ? Number(e.target.value) : '')
            }
          />
        </div>

        {/* Rating */}
        <div className="space-y-1.5">
          <label className="block text-sm font-medium text-slate-700">
            Valutazione minima
          </label>
          <select
            value={filters.min_rating ?? ''}
            onChange={(e) =>
              update('min_rating', e.target.value ? Number(e.target.value) : '')
            }
            className="block w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200"
          >
            <option value="">Qualsiasi</option>
            <option value="1">1+ ancora</option>
            <option value="2">2+ ancore</option>
            <option value="3">3+ ancore</option>
            <option value="4">4+ ancore</option>
            <option value="5">5 ancore</option>
          </select>
        </div>

        {/* Sharing Toggle */}
        <label className="flex cursor-pointer items-center gap-3">
          <input
            type="checkbox"
            checked={filters.sharing || false}
            onChange={(e) => update('sharing', e.target.checked)}
            className="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
          />
          <span className="text-sm text-slate-700">
            Solo con condivisione
          </span>
        </label>

        {/* Actions */}
        <div className="flex gap-2 pt-2">
          <Button onClick={handleApply} size="sm" className="flex-1">
            Applica
          </Button>
          <Button onClick={handleReset} variant="ghost" size="sm">
            Reset
          </Button>
        </div>
      </div>
    </div>
  );
}
