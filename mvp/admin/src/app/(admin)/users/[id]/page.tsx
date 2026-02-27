"use client";

import { useParams } from "next/navigation";
import Link from "next/link";
import { ArrowLeftIcon } from "@heroicons/react/24/outline";

export default function UserDetailPage() {
  const params = useParams();
  const userId = params.id;

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-3">
        <Link
          href="/users"
          className="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
        >
          <ArrowLeftIcon className="h-5 w-5" />
        </Link>
        <h1 className="text-xl font-bold text-slate-800">Utente #{userId}</h1>
      </div>

      <div className="rounded-xl border border-dashed border-slate-300 bg-white p-12 text-center">
        <p className="text-slate-400">
          Il dettaglio utente sara disponibile nella prossima release.
        </p>
      </div>
    </div>
  );
}
