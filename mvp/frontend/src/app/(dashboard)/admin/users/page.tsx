'use client';

import { useEffect, useState, useCallback } from 'react';
import { useUIStore } from '@/stores/ui';
import Input from '@/components/common/Input';
import Badge from '@/components/common/Badge';
import DataTable, { Column } from '@/components/common/DataTable';
import * as adminApi from '@/lib/api/admin';
import type { User, PaginatedResponse } from '@/lib/api/types';
import { MagnifyingGlassIcon } from '@heroicons/react/24/outline';

const roleOptions = [
  { value: 'guest', label: 'Ospite' },
  { value: 'owner', label: 'Armatore' },
  { value: 'admin', label: 'Admin' },
];

const roleVariant: Record<string, 'success' | 'warning' | 'info'> = {
  admin: 'info',
  owner: 'warning',
  guest: 'success',
};

const roleLabel: Record<string, string> = {
  admin: 'Admin',
  owner: 'Armatore',
  guest: 'Ospite',
};

const PAGE_SIZE = 15;

export default function AdminUsersPage() {
  const { addToast } = useUIStore();
  const [users, setUsers] = useState<User[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [search, setSearch] = useState('');
  const [updatingId, setUpdatingId] = useState<number | null>(null);

  const fetchUsers = useCallback(async () => {
    try {
      setLoading(true);
      const res: PaginatedResponse<User> = await adminApi.getUsers(page);
      setUsers(res.results);
      setTotalPages(Math.ceil(res.count / PAGE_SIZE));
    } catch {
      addToast({ type: 'error', message: 'Errore nel caricamento degli utenti' });
    } finally {
      setLoading(false);
    }
  }, [page, addToast]);

  useEffect(() => {
    fetchUsers();
  }, [fetchUsers]);

  const handleRoleChange = async (userId: number, newRole: string) => {
    try {
      setUpdatingId(userId);
      await adminApi.updateUserRole(userId, newRole as User['role']);
      setUsers((prev) =>
        prev.map((u) => (u.id === userId ? { ...u, role: newRole as User['role'] } : u))
      );
      addToast({ type: 'success', message: 'Ruolo aggiornato con successo' });
    } catch {
      addToast({ type: 'error', message: 'Errore nell\'aggiornamento del ruolo' });
    } finally {
      setUpdatingId(null);
    }
  };

  const filteredUsers = search.trim()
    ? users.filter(
        (u) =>
          `${u.first_name} ${u.last_name}`.toLowerCase().includes(search.toLowerCase()) ||
          u.email.toLowerCase().includes(search.toLowerCase())
      )
    : users;

  const columns: Column<User & Record<string, unknown>>[] = [
    {
      key: 'name',
      header: 'Nome',
      render: (u) => (
        <span className="font-medium text-slate-800">
          {u.first_name} {u.last_name}
        </span>
      ),
    },
    {
      key: 'email',
      header: 'Email',
      render: (u) => <span className="text-slate-500">{u.email}</span>,
    },
    {
      key: 'role',
      header: 'Ruolo',
      render: (u) => (
        <Badge variant={roleVariant[u.role] ?? 'info'}>
          {roleLabel[u.role] ?? u.role}
        </Badge>
      ),
    },
    {
      key: 'created_at',
      header: 'Registrato',
      render: (u) => (
        <span className="text-slate-500">
          {new Date(u.created_at).toLocaleDateString('it-IT')}
        </span>
      ),
    },
    {
      key: 'is_active',
      header: 'Attivo',
      render: (u) => (
        <span className="flex items-center gap-2">
          <span
            className={`inline-block h-2.5 w-2.5 rounded-full ${
              u.is_active ? 'bg-emerald-500' : 'bg-red-400'
            }`}
          />
          <span className="text-xs text-slate-500">
            {u.is_active ? 'Attivo' : 'Inattivo'}
          </span>
        </span>
      ),
    },
    {
      key: 'actions',
      header: 'Azioni',
      render: (u) => (
        <select
          value={u.role}
          onChange={(e) => handleRoleChange(u.id, e.target.value)}
          disabled={updatingId === u.id}
          className="rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs text-slate-700 shadow-sm transition-colors focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200 disabled:opacity-50"
        >
          {roleOptions.map((opt) => (
            <option key={opt.value} value={opt.value}>
              {opt.label}
            </option>
          ))}
        </select>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      {/* Header */}
      <div>
        <h1 className="text-2xl font-bold text-sky-900">Gestione Utenti</h1>
        <p className="mt-1 text-sm text-slate-500">
          Visualizza e gestisci tutti gli utenti della piattaforma.
        </p>
      </div>

      {/* Search */}
      <div className="relative max-w-md">
        <MagnifyingGlassIcon className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
        <Input
          placeholder="Cerca per nome o email..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          className="pl-9"
        />
      </div>

      {/* Table */}
      <DataTable
        columns={columns}
        data={filteredUsers as (User & Record<string, unknown>)[]}
        loading={loading}
        page={page}
        totalPages={totalPages}
        onPageChange={setPage}
        emptyMessage="Nessun utente trovato"
      />
    </div>
  );
}
