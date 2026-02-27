"use client";

import { useRef } from "react";
import { useRouter } from "next/navigation";
import PortAutocomplete from "@/components/domain/PortAutocomplete";

export default function HeroSearch() {
  const router = useRouter();
  const checkInRef = useRef<HTMLInputElement>(null);
  const checkOutRef = useRef<HTMLInputElement>(null);

  function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    const params = new URLSearchParams();
    const checkIn = checkInRef.current?.value;
    const checkOut = checkOutRef.current?.value;
    if (checkIn) params.set("check_in", checkIn);
    if (checkOut) params.set("check_out", checkOut);
    router.push(`/search?${params.toString()}`);
  }

  function handlePortSelect(port: {
    id: number;
    name: string;
    city: string;
    region: string | null;
    country: string | null;
  }) {
    const params = new URLSearchParams();
    params.set("port_id", String(port.id));
    if (port.country) params.set("country", port.country);
    if (port.region) params.set("region", port.region);
    const checkIn = checkInRef.current?.value;
    const checkOut = checkOutRef.current?.value;
    if (checkIn) params.set("check_in", checkIn);
    if (checkOut) params.set("check_out", checkOut);
    router.push(`/search?${params.toString()}`);
  }

  return (
    <form
      onSubmit={handleSubmit}
      className="flex flex-col gap-3 rounded-2xl bg-white/10 p-4 backdrop-blur-md sm:flex-row sm:items-center"
    >
      <div className="flex-1">
        <PortAutocomplete
          onSelect={handlePortSelect}
          placeholder="Porto o città..."
        />
      </div>
      <div className="flex gap-3 sm:w-auto">
        <input
          ref={checkInRef}
          type="date"
          name="check_in"
          className="w-full rounded-xl bg-white px-4 py-3 text-slate-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 sm:w-40"
        />
        <input
          ref={checkOutRef}
          type="date"
          name="check_out"
          className="w-full rounded-xl bg-white px-4 py-3 text-slate-800 shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-400 sm:w-40"
        />
      </div>
      <button
        type="submit"
        className="rounded-xl bg-cyan-500 px-8 py-3 font-semibold text-white shadow-lg transition hover:bg-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-300"
      >
        Cerca
      </button>
    </form>
  );
}
