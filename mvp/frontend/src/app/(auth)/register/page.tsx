"use client";

import { useState, FormEvent } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { register } from "@/lib/api/auth";

type Role = "guest" | "owner";

export default function RegisterPage() {
  const router = useRouter();
  const [firstName, setFirstName] = useState("");
  const [lastName, setLastName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [role, setRole] = useState<Role>("guest");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError("");

    // Validation
    if (!firstName || !lastName || !email || !password || !confirmPassword) {
      setError("Compila tutti i campi.");
      return;
    }

    if (password.length < 8) {
      setError("La password deve essere di almeno 8 caratteri.");
      return;
    }

    if (password !== confirmPassword) {
      setError("Le password non corrispondono.");
      return;
    }

    setLoading(true);
    try {
      const data = await register({
        first_name: firstName,
        last_name: lastName,
        email,
        password,
        role,
      });
      localStorage.setItem("token", data.access);
      localStorage.setItem("user", JSON.stringify(data.user));
      const path = data.user.role === "admin" ? "/admin" : data.user.role === "owner" ? "/owner/berths" : "/guest/bookings";
      router.push(path);
    } catch (err: unknown) {
      if (err instanceof Error) {
        setError(err.message || "Registrazione fallita. Riprova.");
      } else {
        setError("Registrazione fallita. Riprova.");
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
          <h1 className="text-2xl font-bold text-slate-800">
            Crea il tuo account
          </h1>
          <p className="mt-2 text-slate-500">
            Unisciti alla community di Porty
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
          {/* Role Selector */}
          <div>
            <label className="mb-2 block text-sm font-medium text-slate-700">
              Sono un...
            </label>
            <div className="flex gap-3">
              <button
                type="button"
                onClick={() => setRole("guest")}
                className={`flex-1 rounded-xl border-2 px-4 py-3 text-sm font-medium transition ${
                  role === "guest"
                    ? "border-sky-500 bg-sky-50 text-sky-700"
                    : "border-slate-200 text-slate-500 hover:border-slate-300"
                }`}
              >
                <div className="text-lg mb-1">
                  <svg
                    className={`mx-auto h-6 w-6 ${
                      role === "guest" ? "text-sky-500" : "text-slate-400"
                    }`}
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    strokeWidth={2}
                  >
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                    />
                  </svg>
                </div>
                Ospite
              </button>
              <button
                type="button"
                onClick={() => setRole("owner")}
                className={`flex-1 rounded-xl border-2 px-4 py-3 text-sm font-medium transition ${
                  role === "owner"
                    ? "border-cyan-500 bg-cyan-50 text-cyan-700"
                    : "border-slate-200 text-slate-500 hover:border-slate-300"
                }`}
              >
                <div className="text-lg mb-1">
                  <svg
                    className={`mx-auto h-6 w-6 ${
                      role === "owner" ? "text-cyan-500" : "text-slate-400"
                    }`}
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    strokeWidth={2}
                  >
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                    />
                  </svg>
                </div>
                Proprietario
              </button>
            </div>
          </div>

          {/* Name Fields */}
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label
                htmlFor="firstName"
                className="mb-1 block text-sm font-medium text-slate-700"
              >
                Nome
              </label>
              <input
                id="firstName"
                type="text"
                value={firstName}
                onChange={(e) => setFirstName(e.target.value)}
                placeholder="Mario"
                autoComplete="given-name"
                className="w-full rounded-lg border border-slate-200 px-4 py-3 text-slate-700 placeholder-slate-400 transition focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
              />
            </div>
            <div>
              <label
                htmlFor="lastName"
                className="mb-1 block text-sm font-medium text-slate-700"
              >
                Cognome
              </label>
              <input
                id="lastName"
                type="text"
                value={lastName}
                onChange={(e) => setLastName(e.target.value)}
                placeholder="Rossi"
                autoComplete="family-name"
                className="w-full rounded-lg border border-slate-200 px-4 py-3 text-slate-700 placeholder-slate-400 transition focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
              />
            </div>
          </div>

          {/* Email */}
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

          {/* Password */}
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
              placeholder="Almeno 8 caratteri"
              autoComplete="new-password"
              className="w-full rounded-lg border border-slate-200 px-4 py-3 text-slate-700 placeholder-slate-400 transition focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
            />
          </div>

          {/* Confirm Password */}
          <div>
            <label
              htmlFor="confirmPassword"
              className="mb-1 block text-sm font-medium text-slate-700"
            >
              Conferma password
            </label>
            <input
              id="confirmPassword"
              type="password"
              value={confirmPassword}
              onChange={(e) => setConfirmPassword(e.target.value)}
              placeholder="Ripeti la password"
              autoComplete="new-password"
              className="w-full rounded-lg border border-slate-200 px-4 py-3 text-slate-700 placeholder-slate-400 transition focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
            />
          </div>

          {/* Submit */}
          <button
            type="submit"
            disabled={loading}
            className="w-full rounded-xl bg-sky-600 py-3 text-base font-semibold text-white shadow-md transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 disabled:opacity-60"
          >
            {loading ? (
              <span className="flex items-center justify-center gap-2">
                <span className="spinner spinner-sm border-white/30 border-t-white" />
                Registrazione in corso...
              </span>
            ) : (
              "Registrati"
            )}
          </button>
        </form>

        {/* ── Footer Link ──────────────────────────────────────────── */}
        <p className="mt-6 text-center text-sm text-slate-500">
          Hai gia un account?{" "}
          <Link
            href="/login"
            scroll={false}
            className="font-medium text-sky-600 hover:text-sky-700 transition"
          >
            Accedi
          </Link>
        </p>
      </div>
    </div>
  );
}
