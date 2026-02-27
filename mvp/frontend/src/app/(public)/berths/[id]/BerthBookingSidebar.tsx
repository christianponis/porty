"use client";

import { useState, useMemo } from "react";
import { useRouter } from "next/navigation";

interface BerthBookingSidebarProps {
  berthId: number;
  pricePerNight: number;
  sharingAvailable: boolean;
  isAvailable: boolean;
}

export default function BerthBookingSidebar({
  berthId,
  pricePerNight,
  sharingAvailable,
  isAvailable,
}: BerthBookingSidebarProps) {
  const router = useRouter();
  const [checkIn, setCheckIn] = useState("");
  const [checkOut, setCheckOut] = useState("");
  const [mode, setMode] = useState<"eur" | "nodi">("eur");
  const [loading, setLoading] = useState(false);

  const nights = useMemo(() => {
    if (!checkIn || !checkOut) return 0;
    const diff =
      (new Date(checkOut).getTime() - new Date(checkIn).getTime()) /
      (1000 * 60 * 60 * 24);
    return Math.max(0, Math.round(diff));
  }, [checkIn, checkOut]);

  const totalPrice = nights * pricePerNight;
  const totalNodi = nights * 10; // Example Nodi calculation

  const handleBooking = async () => {
    if (!checkIn || !checkOut || nights <= 0) return;

    const token =
      typeof window !== "undefined" ? localStorage.getItem("token") : null;
    if (!token) {
      router.push("/login");
      return;
    }

    setLoading(true);
    try {
      const res = await fetch("/api/guest/bookings/", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({
          berth_id: berthId,
          start_date: checkIn,
          end_date: checkOut,
          booking_mode: mode === "nodi" ? "sharing" : "rental",
          boat_length: 1,
          boat_width: 1,
          boat_draft: 1,
          boat_name: "Barca",
        }),
      });

      if (res.ok) {
        router.push("/guest/bookings");
      } else {
        const data = await res.json();
        alert(data.detail || "Errore nella prenotazione");
      }
    } catch {
      alert("Errore di connessione");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="rounded-2xl bg-white p-6 shadow-lg">
      {/* ── Price Display ──────────────────────────────────────────────── */}
      <div className="mb-6">
        <span className="text-2xl font-bold text-slate-800">
          &euro;{pricePerNight}
        </span>
        <span className="text-slate-500"> / notte</span>
      </div>

      {/* ── Booking Mode ───────────────────────────────────────────────── */}
      {sharingAvailable && (
        <div className="mb-6">
          <label className="mb-2 block text-sm font-medium text-slate-600">
            Modalita
          </label>
          <div className="flex gap-2">
            <button
              onClick={() => setMode("eur")}
              className={`flex-1 rounded-lg px-4 py-2 text-sm font-medium transition ${
                mode === "eur"
                  ? "bg-sky-600 text-white"
                  : "border border-slate-200 text-slate-600 hover:bg-slate-50"
              }`}
            >
              Affitto EUR
            </button>
            <button
              onClick={() => setMode("nodi")}
              className={`flex-1 rounded-lg px-4 py-2 text-sm font-medium transition ${
                mode === "nodi"
                  ? "bg-cyan-600 text-white"
                  : "border border-slate-200 text-slate-600 hover:bg-slate-50"
              }`}
            >
              Scambio Nodi
            </button>
          </div>
        </div>
      )}

      {/* ── Date Pickers ───────────────────────────────────────────────── */}
      <div className="mb-4">
        <label className="mb-1 block text-sm font-medium text-slate-600">
          Check-in
        </label>
        <input
          type="date"
          value={checkIn}
          onChange={(e) => setCheckIn(e.target.value)}
          min={new Date().toISOString().split("T")[0]}
          className="w-full rounded-lg border border-slate-200 px-4 py-3 text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
        />
      </div>

      <div className="mb-6">
        <label className="mb-1 block text-sm font-medium text-slate-600">
          Check-out
        </label>
        <input
          type="date"
          value={checkOut}
          onChange={(e) => setCheckOut(e.target.value)}
          min={checkIn || new Date().toISOString().split("T")[0]}
          className="w-full rounded-lg border border-slate-200 px-4 py-3 text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
        />
      </div>

      {/* ── Price Summary ──────────────────────────────────────────────── */}
      {nights > 0 && (
        <div className="mb-6 rounded-lg bg-slate-50 p-4">
          <div className="flex justify-between text-sm text-slate-600">
            <span>
              {mode === "eur"
                ? `€${pricePerNight} x ${nights} notti`
                : `10 Nodi x ${nights} notti`}
            </span>
            <span className="font-medium">
              {mode === "eur" ? `€${totalPrice}` : `${totalNodi} Nodi`}
            </span>
          </div>
          <div className="mt-3 flex justify-between border-t border-slate-200 pt-3 text-base font-bold text-slate-800">
            <span>Totale</span>
            <span>
              {mode === "eur" ? `€${totalPrice}` : `${totalNodi} Nodi`}
            </span>
          </div>
        </div>
      )}

      {/* ── Book Button ────────────────────────────────────────────────── */}
      <button
        onClick={handleBooking}
        disabled={!isAvailable || nights <= 0 || loading}
        className="w-full rounded-xl bg-sky-600 py-3 text-base font-semibold text-white shadow-md transition hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
      >
        {loading ? (
          <span className="flex items-center justify-center gap-2">
            <span className="spinner spinner-sm border-white/30 border-t-white" />
            Prenotazione in corso...
          </span>
        ) : !isAvailable ? (
          "Non disponibile"
        ) : nights <= 0 ? (
          "Seleziona le date"
        ) : (
          "Prenota ora"
        )}
      </button>

      {!isAvailable && (
        <p className="mt-3 text-center text-sm text-red-500">
          Questo posto barca non e al momento disponibile.
        </p>
      )}
    </div>
  );
}
