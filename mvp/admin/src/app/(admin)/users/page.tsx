"use client";

import { useEffect, useState, useCallback } from "react";
import { useUIStore } from "@/stores/ui";
import * as adminApi from "@/lib/api/admin";
import type { User, PaginatedResponse } from "@/lib/api/types";
import DataTable, { Column } from "@/components/common/DataTable";
import SearchInput from "@/components/common/SearchInput";
import Select from "@/components/common/Select";
import Badge from "@/components/common/Badge";
import Modal from "@/components/common/Modal";
import Button from "@/components/common/Button";
import { formatDate } from "@/lib/utils/formatters";
import { roleLabels, roleColors } from "@/lib/utils/constants";
import { PencilSquareIcon } from "@heroicons/react/24/outline";

export default function UsersPage() {
  const { addToast } = useUIStore();
  const [users, setUsers] = useState<User[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [search, setSearch] = useState("");
  const [roleFilter, setRoleFilter] = useState("");

  const [editingUser, setEditingUser] = useState<User | null>(null);
  const [newRole, setNewRole] = useState("");
  const [saving, setSaving] = useState(false);

  const fetchUsers = useCallback(async () => {
    try {
      setLoading(true);
      const res: PaginatedResponse<User> = await adminApi.getUsers(page, search, roleFilter);
      setUsers(res.data);
      setTotalPages(res.last_page);
    } catch {
      addToast({ type: "error", message: "Errore nel caricamento utenti" });
    } finally {
      setLoading(false);
    }
  }, [page, search, roleFilter, addToast]);

  useEffect(() => {
    fetchUsers();
  }, [fetchUsers]);

  const openRoleModal = (user: User) => {
    setEditingUser(user);
    setNewRole(user.role);
  };

  const handleRoleChange = async () => {
    if (!editingUser || !newRole) return;
    try {
      setSaving(true);
      await adminApi.updateUserRole(editingUser.id, newRole);
      addToast({ type: "success", message: "Ruolo aggiornato" });
      setEditingUser(null);
      fetchUsers();
    } catch {
      addToast({ type: "error", message: "Errore nell'aggiornamento" });
    } finally {
      setSaving(false);
    }
  };

  const columns: Column<User & Record<string, unknown>>[] = [
    {
      key: "name",
      header: "Utente",
      render: (u) => (
        <div className="flex items-center gap-3">
          <div className="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">
            {(u.first_name?.[0] || "").toUpperCase()}
            {(u.last_name?.[0] || "").toUpperCase()}
          </div>
          <div>
            <p className="font-medium text-slate-800">{u.name}</p>
            <p className="text-xs text-slate-400">{u.email}</p>
          </div>
        </div>
      ),
    },
    {
      key: "role",
      header: "Ruolo",
      render: (u) => (
        <Badge variant={roleColors[u.role] || "default"}>
          {roleLabels[u.role] || u.role}
        </Badge>
      ),
    },
    {
      key: "phone",
      header: "Telefono",
      render: (u) => (
        <span className="text-sm text-slate-500">{u.phone || "-"}</span>
      ),
    },
    {
      key: "nodi",
      header: "Nodi",
      render: (u) => (
        <span className="font-medium text-emerald-600">{u.nodi_balance}</span>
      ),
    },
    {
      key: "is_active",
      header: "Stato",
      render: (u) => (
        <Badge variant={u.is_active ? "success" : "default"}>
          {u.is_active ? "Attivo" : "Inattivo"}
        </Badge>
      ),
    },
    {
      key: "created_at",
      header: "Registrato",
      render: (u) => (
        <span className="text-xs text-slate-400">{formatDate(u.created_at)}</span>
      ),
    },
    {
      key: "actions",
      header: "",
      className: "text-right",
      render: (u) => (
        <button
          onClick={() => openRoleModal(u as User)}
          className="rounded-lg p-1.5 text-slate-400 transition hover:bg-sky-50 hover:text-sky-600"
        >
          <PencilSquareIcon className="h-4 w-4" />
        </button>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-slate-800">Gestione Utenti</h1>
        <p className="mt-1 text-sm text-slate-500">
          Gestisci gli utenti della piattaforma
        </p>
      </div>

      <div className="flex flex-wrap items-end gap-4">
        <SearchInput
          value={search}
          onChange={(v) => {
            setSearch(v);
            setPage(1);
          }}
          placeholder="Cerca per nome o email..."
          className="w-64"
        />
        <Select
          options={[
            { value: "admin", label: "Admin" },
            { value: "owner", label: "Proprietario" },
            { value: "guest", label: "Ospite" },
          ]}
          value={roleFilter}
          onChange={(e) => {
            setRoleFilter(e.target.value);
            setPage(1);
          }}
          placeholder="Tutti i ruoli"
          className="w-44"
        />
      </div>

      <DataTable
        columns={columns}
        data={users as (User & Record<string, unknown>)[]}
        loading={loading}
        page={page}
        totalPages={totalPages}
        onPageChange={setPage}
        emptyMessage="Nessun utente trovato"
      />

      {/* Role Edit Modal */}
      <Modal
        open={!!editingUser}
        onClose={() => setEditingUser(null)}
        title="Modifica ruolo utente"
        size="sm"
        footer={
          <>
            <Button variant="secondary" onClick={() => setEditingUser(null)}>
              Annulla
            </Button>
            <Button onClick={handleRoleChange} loading={saving}>
              Salva
            </Button>
          </>
        }
      >
        {editingUser && (
          <div className="space-y-4">
            <p className="text-sm text-slate-600">
              Cambia ruolo per <strong>{editingUser.name}</strong>
            </p>
            <Select
              label="Nuovo ruolo"
              options={[
                { value: "admin", label: "Admin" },
                { value: "owner", label: "Proprietario" },
                { value: "guest", label: "Ospite" },
              ]}
              value={newRole}
              onChange={(e) => setNewRole(e.target.value)}
            />
          </div>
        )}
      </Modal>
    </div>
  );
}
