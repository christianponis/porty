"use client";

import { Suspense, useState, useEffect, useCallback } from "react";
import { useSearchParams, useRouter } from "next/navigation";
import type { Berth, SearchParams } from "@/lib/api/types";
import BerthCard from "@/components/domain/BerthCard";

export default function SearchPage() {
  return (
    <Suspense fallback={<div className="flex items-center justify-center py-20"><div className="spinner" /></div>}>
      <SearchPageContent />
    </Suspense>
  );
}

function SearchPageContent() {
  const searchParams = useSearchParams();
  const router = useRouter();

  const [berths, setBerths] = useState<Berth[]>([]);
  const [totalCount, setTotalCount] = useState(0);
  const [totalPages, setTotalPages] = useState(1);
  const [loading, setLoading] = useState(true);
  const [currentPage, setCurrentPage] = useState(1);
  const [filtersOpen, setFiltersOpen] = useState(false);
  const [ports, setPorts] = useState<{ id: number; name: string; city: string }[]>([]);
  const [countries, setCountries] = useState<string[]>([]);
  const [regions, setRegions] = useState<string[]>([]);

  // ── Filter State ────────────────────────────────────────────────────────
  const [filters, setFilters] = useState<SearchParams>({
    country: searchParams.get("country") || undefined,
    region: searchParams.get("region") || undefined,
    port_id: searchParams.get("port_id") ? Number(searchParams.get("port_id")) : undefined,
    min_price: searchParams.get("min_price")
      ? Number(searchParams.get("min_price"))
      : undefined,
    max_price: searchParams.get("max_price")
      ? Number(searchParams.get("max_price"))
      : undefined,
    min_length: searchParams.get("min_length")
      ? Number(searchParams.get("min_length"))
      : undefined,
    max_length: searchParams.get("max_length")
      ? Number(searchParams.get("max_length"))
      : undefined,
    min_rating: searchParams.get("min_rating")
      ? Number(searchParams.get("min_rating"))
      : undefined,
    sharing_enabled: searchParams.get("sharing") === "true" ? true : undefined,
    date_from: searchParams.get("check_in") || undefined,
    date_to: searchParams.get("check_out") || undefined,
  });

  const query = searchParams.get("q") || "";
  const pageSize = 12;

  // ── Fetch Countries ─────────────────────────────────────────────────────
  useEffect(() => {
    fetch("/api/catalog/countries")
      .then((r) => r.json())
      .then((json) => {
        if (json.data) {
          setCountries(json.data);
        }
      })
      .catch(() => setCountries([]));
  }, []);

  // ── Fetch Regions ───────────────────────────────────────────────────────
  useEffect(() => {
    if (filters.country) {
      const params = new URLSearchParams();
      params.set("country", filters.country);

      fetch(`/api/catalog/regions?${params.toString()}`)
        .then((r) => r.json())
        .then((json) => {
          if (json.data) {
            setRegions(json.data);
          }
        })
        .catch(() => setRegions([]));
    } else {
      setRegions([]);
    }
  }, [filters.country]);

  // ── Fetch Berths ────────────────────────────────────────────────────────
  const fetchBerths = useCallback(
    async (page: number) => {
      setLoading(true);
      try {
        const params = new URLSearchParams();
        if (query) params.set("q", query);
        if (filters.country) params.set("country", filters.country);
        if (filters.region) params.set("region", filters.region);
        if (filters.port_id) params.set("port_id", String(filters.port_id));
        if (filters.min_price) params.set("min_price", String(filters.min_price));
        if (filters.max_price) params.set("max_price", String(filters.max_price));
        if (filters.min_length) params.set("min_length", String(filters.min_length));
        if (filters.max_length) params.set("max_length", String(filters.max_length));
        if (filters.min_rating) params.set("min_rating", String(filters.min_rating));
        if (filters.sharing_enabled) params.set("sharing_enabled", "true");
        if (filters.date_from) params.set("date_from", filters.date_from);
        if (filters.date_to) params.set("date_to", filters.date_to);
        params.set("page", String(page));

        const res = await fetch(`/api/catalog/berths/search?${params.toString()}`);
        if (!res.ok) throw new Error("Fetch failed");
        const json = await res.json();

        setBerths(json.data ?? []);
        setTotalCount(json.meta?.total ?? 0);
        setTotalPages(json.meta?.last_page ?? 1);
      } catch {
        setBerths([]);
        setTotalCount(0);
        setTotalPages(1);
      } finally {
        setLoading(false);
      }
    },
    [query, filters]
  );

  // ── Fetch Ports ─────────────────────────────────────────────────────────
  useEffect(() => {
    if (filters.country || filters.region) {
      const params = new URLSearchParams();
      if (filters.country) params.set("country", filters.country);
      if (filters.region) params.set("region", filters.region);
      params.set("per_page", "100");

      fetch(`/api/catalog/ports?${params.toString()}`)
        .then((r) => r.json())
        .then((json) => {
          if (json.data) {
            setPorts(json.data.map((p: any) => ({ id: p.id, name: p.name, city: p.city })));
          }
        })
        .catch(() => setPorts([]));
    } else {
      setPorts([]);
    }
  }, [filters.country, filters.region]);

  useEffect(() => {
    fetchBerths(currentPage);
  }, [fetchBerths, currentPage]);

  const handleFilterChange = (key: keyof SearchParams, value: string | boolean) => {
    setFilters((prev) => ({
      ...prev,
      [key]: value === "" ? undefined : value,
    }));
    setCurrentPage(1);
  };

  const clearFilters = () => {
    setFilters({});
    setCurrentPage(1);
    router.push("/search");
  };

  return (
    <div className="min-h-screen bg-slate-50">
      {/* ── Header ──────────────────────────────────────────────────────── */}
      <div className="bg-gradient-to-r from-sky-900 to-cyan-700 px-4 py-8 text-white sm:px-6 lg:px-8">
        <div className="mx-auto max-w-7xl">
          <h1 className="text-2xl font-bold sm:text-3xl">Cerca posti barca</h1>
          {query && (
            <p className="mt-2 text-sky-200">
              Risultati per &quot;{query}&quot;
              {totalCount > 0 && (
                <span> - {totalCount} risultati trovati</span>
              )}
            </p>
          )}
        </div>
      </div>

      <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {/* ── Mobile Filter Toggle ─────────────────────────────────────── */}
        <button
          onClick={() => setFiltersOpen(!filtersOpen)}
          className="mb-4 flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 lg:hidden"
        >
          <svg className="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
            <path strokeLinecap="round" strokeLinejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
          </svg>
          Filtri
        </button>

        <div className="flex gap-8">
          {/* ── Sidebar Filters ─────────────────────────────────────────── */}
          <aside
            className={`${
              filtersOpen ? "fixed inset-0 z-50 block bg-black/40" : "hidden"
            } lg:static lg:block lg:bg-transparent`}
          >
            <div
              className={`${
                filtersOpen
                  ? "animate-slide-up fixed bottom-0 left-0 right-0 z-50 max-h-[80vh] overflow-y-auto rounded-t-2xl bg-white p-6 shadow-2xl"
                  : ""
              } lg:sticky lg:top-24 lg:w-64 lg:rounded-xl lg:bg-white lg:p-6 lg:shadow-sm`}
            >
              {/* Mobile close button */}
              {filtersOpen && (
                <div className="mb-4 flex items-center justify-between lg:hidden">
                  <h3 className="text-lg font-semibold text-slate-800">Filtri</h3>
                  <button
                    onClick={() => setFiltersOpen(false)}
                    className="rounded-lg p-1 text-slate-400 hover:text-slate-600"
                  >
                    <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                      <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              )}

              <h3 className="mb-4 hidden text-lg font-semibold text-slate-800 lg:block">
                Filtri
              </h3>

              {/* Country */}
              <div className="mb-4">
                <label className="mb-1 block text-sm font-medium text-slate-600">
                  Paese
                </label>
                <select
                  value={filters.country || ""}
                  onChange={(e) => {
                    handleFilterChange("country", e.target.value);
                    if (e.target.value !== filters.country) {
                      setFilters(prev => ({ ...prev, region: undefined, port_id: undefined }));
                    }
                  }}
                  className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                >
                  <option value="">Tutti i paesi</option>
                  {countries.map((country) => (
                    <option key={country} value={country}>{country}</option>
                  ))}
                </select>
              </div>

              {/* Region */}
              <div className="mb-4">
                <label className="mb-1 block text-sm font-medium text-slate-600">
                  Regione
                </label>
                <select
                  value={filters.region || ""}
                  onChange={(e) => {
                    handleFilterChange("region", e.target.value);
                    if (e.target.value !== filters.region) {
                      setFilters(prev => ({ ...prev, port_id: undefined }));
                    }
                  }}
                  disabled={!filters.country}
                  className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 disabled:bg-slate-100 disabled:cursor-not-allowed"
                >
                  <option value="">Tutte le regioni</option>
                  {regions.map((region) => (
                    <option key={region} value={region}>{region}</option>
                  ))}
                </select>
              </div>

              {/* Port */}
              <div className="mb-4">
                <label className="mb-1 block text-sm font-medium text-slate-600">
                  Porto
                </label>
                <select
                  value={filters.port_id || ""}
                  onChange={(e) => handleFilterChange("port_id", e.target.value ? Number(e.target.value) : "")}
                  disabled={ports.length === 0}
                  className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500 disabled:bg-slate-100 disabled:cursor-not-allowed"
                >
                  <option value="">Tutti i porti</option>
                  {ports.map((port) => (
                    <option key={port.id} value={port.id}>
                      {port.name} - {port.city}
                    </option>
                  ))}
                </select>
              </div>

              {/* Price Range */}
              <div className="mb-4">
                <label className="mb-1 block text-sm font-medium text-slate-600">
                  Prezzo per notte (EUR)
                </label>
                <div className="flex gap-2">
                  <input
                    type="number"
                    placeholder="Min"
                    value={filters.min_price || ""}
                    onChange={(e) => handleFilterChange("min_price", e.target.value)}
                    className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                  />
                  <input
                    type="number"
                    placeholder="Max"
                    value={filters.max_price || ""}
                    onChange={(e) => handleFilterChange("max_price", e.target.value)}
                    className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                  />
                </div>
              </div>

              {/* Boat Length */}
              <div className="mb-4">
                <label className="mb-1 block text-sm font-medium text-slate-600">
                  Lunghezza barca (m)
                </label>
                <div className="flex gap-2">
                  <input
                    type="number"
                    placeholder="Min"
                    value={filters.min_length || ""}
                    onChange={(e) => handleFilterChange("min_length", e.target.value)}
                    className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                  />
                  <input
                    type="number"
                    placeholder="Max"
                    value={filters.max_length || ""}
                    onChange={(e) => handleFilterChange("max_length", e.target.value)}
                    className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                  />
                </div>
              </div>

              {/* Rating */}
              <div className="mb-4">
                <label className="mb-1 block text-sm font-medium text-slate-600">
                  Valutazione minima
                </label>
                <select
                  value={filters.min_rating || ""}
                  onChange={(e) => handleFilterChange("min_rating", e.target.value)}
                  className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                >
                  <option value="">Qualsiasi</option>
                  <option value="3">3+ stelle</option>
                  <option value="4">4+ stelle</option>
                  <option value="4.5">4.5+ stelle</option>
                </select>
              </div>

              {/* Dates */}
              <div className="mb-4">
                <label className="mb-1 block text-sm font-medium text-slate-600">
                  Date
                </label>
                <input
                  type="date"
                  value={filters.date_from || ""}
                  onChange={(e) => handleFilterChange("date_from", e.target.value)}
                  className="mb-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                />
                <input
                  type="date"
                  value={filters.date_to || ""}
                  onChange={(e) => handleFilterChange("date_to", e.target.value)}
                  className="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                />
              </div>

              {/* Sharing */}
              <div className="mb-6">
                <label className="flex items-center gap-2 text-sm text-slate-600">
                  <input
                    type="checkbox"
                    checked={filters.sharing_enabled || false}
                    onChange={(e) => handleFilterChange("sharing_enabled", e.target.checked)}
                    className="rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                  />
                  Solo scambio Nodi
                </label>
              </div>

              {/* Actions */}
              <button
                onClick={clearFilters}
                className="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
              >
                Cancella filtri
              </button>

              {/* Mobile apply */}
              {filtersOpen && (
                <button
                  onClick={() => setFiltersOpen(false)}
                  className="mt-2 w-full rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-sky-700 lg:hidden"
                >
                  Applica filtri
                </button>
              )}
            </div>
          </aside>

          {/* ── Results Grid ───────────────────────────────────────────────── */}
          <div className="flex-1">
            {loading ? (
              <div className="flex items-center justify-center py-20">
                <div className="spinner" />
              </div>
            ) : berths.length === 0 ? (
              <div className="rounded-2xl bg-white py-20 text-center shadow-sm">
                <svg
                  className="mx-auto h-16 w-16 text-slate-300"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  strokeWidth={1}
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                  />
                </svg>
                <h3 className="mt-4 text-lg font-semibold text-slate-700">
                  Nessun risultato trovato
                </h3>
                <p className="mt-2 text-slate-500">
                  Prova a modificare i filtri o a cercare in un&apos;altra zona.
                </p>
                <button
                  onClick={clearFilters}
                  className="mt-4 rounded-lg bg-sky-600 px-6 py-2 text-sm font-medium text-white transition hover:bg-sky-700"
                >
                  Cancella filtri
                </button>
              </div>
            ) : (
              <>
                <div className="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                  {berths.map((berth) => (
                    <BerthCard key={berth.id} berth={berth} />
                  ))}
                </div>

                {/* ── Pagination ────────────────────────────────────────────── */}
                {totalPages > 1 && (
                  <nav className="mt-8 flex items-center justify-center gap-2">
                    <button
                      onClick={() => setCurrentPage((p) => Math.max(1, p - 1))}
                      disabled={currentPage === 1}
                      className="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                      Precedente
                    </button>

                    {Array.from({ length: Math.min(totalPages, 5) }, (_, i) => {
                      let pageNum: number;
                      if (totalPages <= 5) {
                        pageNum = i + 1;
                      } else if (currentPage <= 3) {
                        pageNum = i + 1;
                      } else if (currentPage >= totalPages - 2) {
                        pageNum = totalPages - 4 + i;
                      } else {
                        pageNum = currentPage - 2 + i;
                      }
                      return (
                        <button
                          key={pageNum}
                          onClick={() => setCurrentPage(pageNum)}
                          className={`rounded-lg px-3 py-2 text-sm font-medium transition ${
                            currentPage === pageNum
                              ? "bg-sky-600 text-white"
                              : "border border-slate-200 text-slate-600 hover:bg-slate-50"
                          }`}
                        >
                          {pageNum}
                        </button>
                      );
                    })}

                    <button
                      onClick={() => setCurrentPage((p) => Math.min(totalPages, p + 1))}
                      disabled={currentPage === totalPages}
                      className="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                      Successiva
                    </button>
                  </nav>
                )}
              </>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
