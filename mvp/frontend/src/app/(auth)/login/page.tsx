"use client";

import { useState, FormEvent } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { login } from "@/lib/api/auth";

export default function LoginPage() {
  const router = useRouter();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError("");

    if (!email || !password) {
      setError("Inserisci email e password.");
      return;
    }

    setLoading(true);
    try {
      const data = await login(email, password);
      localStorage.setItem("token", data.access);
      localStorage.setItem("user", JSON.stringify(data.user));
      const path = data.user.role === "admin" ? "/admin" : data.user.role === "owner" ? "/owner/berths" : "/guest/bookings";
      router.push(path);
    } catch (err: unknown) {
      if (err instanceof Error) {
        setError(err.message || "Credenziali non valide. Riprova.");
      } else {
        setError("Credenziali non valide. Riprova.");
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="w-full max-w-md">
      <div className="rounded-2xl bg-white px-8 py-10 shadow-xl">
        {/* ── Header ───────────────────────────────────────────────── */}
        <div className="mb-8 text-center">
          <h1 className="text-2xl font-bold text-slate-800">Bentornato!</h1>
          <p className="mt-2 text-slate-500">
            Accedi al tuo account Porty
          </p>
        </div>

        {/* ── Error ────────────────────────────────────────────────── */}
        {error && (
          <div className="mb-6 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-600">
            {error}
          </div>
        )}

        {/* ── Form ─────────────────────────────────────────────────── */}
        <form onSubmit={handleSubmit} className="space-y-5">
          <div>
            <label
              htmlFor="email"
              className="mb-1 block text-sm font-medium text-slate-700"
            >
              Email
            </label>
            <input
              id="email"
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="tu@esempio.it"
              autoComplete="email"
              className="w-full rounded-lg border border-slate-200 px-4 py-3 text-slate-700 placeholder-slate-400 transition focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
            />
          </div>

          <div>
            <label
              htmlFor="password"
              className="mb-1 block text-sm font-medium text-slate-700"
            >
              Password
            </label>
            <input
              id="password"
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              placeholder="La tua password"
              autoComplete="current-password"
              className="w-full rounded-lg border border-slate-200 px-4 py-3 text-slate-700 placeholder-slate-400 transition focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
            />
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full rounded-xl bg-sky-600 py-3 text-base font-semibold text-white shadow-md transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 disabled:opacity-60"
          >
            {loading ? (
              <span className="flex items-center justify-center gap-2">
                <span className="spinner spinner-sm border-white/30 border-t-white" />
                Accesso in corso...
              </span>
            ) : (
              "Accedi"
            )}
          </button>
        </form>

        {/* ── Footer Link ──────────────────────────────────────────── */}
        <p className="mt-6 text-center text-sm text-slate-500">
          Non hai un account?{" "}
          <Link
            href="/register"
            scroll={false}
            className="font-medium text-sky-600 hover:text-sky-700 transition"
          >
            Registrati
          </Link>
        </p>
      </div>
    </div>
  );
}
