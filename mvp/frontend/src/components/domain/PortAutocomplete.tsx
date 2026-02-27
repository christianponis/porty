"use client";

import { useState, useRef, useEffect } from "react";

const countryCode: Record<string, string> = {
  Italia: "it",
  France: "fr",
  Greece: "gr",
  Croatia: "hr",
  Spain: "es",
  Montenegro: "me",
  Slovenia: "si",
  Albania: "al",
  Malta: "mt",
  Tunisia: "tn",
};

interface PortSuggestion {
  id: number;
  name: string;
  city: string;
  region: string | null;
  country: string | null;
}

interface PortAutocompleteProps {
  onSelect: (port: PortSuggestion) => void;
  placeholder?: string;
  className?: string;
}

export default function PortAutocomplete({
  onSelect,
  placeholder = "Cerca un porto...",
  className = "",
}: PortAutocompleteProps) {
  const [query, setQuery] = useState("");
  const [suggestions, setSuggestions] = useState<PortSuggestion[]>([]);
  const [open, setOpen] = useState(false);
  const [loading, setLoading] = useState(false);
  const [activeIndex, setActiveIndex] = useState(-1);
  const containerRef = useRef<HTMLDivElement>(null);
  const timerRef = useRef<ReturnType<typeof setTimeout> | null>(null);

  // ── Close on outside click ───────────────────────────────────────────────
  useEffect(() => {
    function handleClick(e: MouseEvent) {
      if (containerRef.current && !containerRef.current.contains(e.target as Node)) {
        setOpen(false);
      }
    }
    document.addEventListener("mousedown", handleClick);
    return () => document.removeEventListener("mousedown", handleClick);
  }, []);

  // ── Input change with debounce ───────────────────────────────────────────
  function handleChange(e: React.ChangeEvent<HTMLInputElement>) {
    const val = e.target.value;
    setQuery(val);

    if (timerRef.current) clearTimeout(timerRef.current);

    if (val.length < 3) {
      setSuggestions([]);
      setOpen(false);
      setLoading(false);
      return;
    }

    setLoading(true);
    timerRef.current = setTimeout(async () => {
      try {
        const params = new URLSearchParams({ search: val, per_page: "8" });
        const res = await fetch(`/api/catalog/ports?${params.toString()}`);
        if (!res.ok) throw new Error("fetch failed");
        const json = await res.json();
        const data: PortSuggestion[] = json.data ?? [];
        setSuggestions(data);
        setOpen(data.length > 0);
        setActiveIndex(-1);
      } catch {
        setSuggestions([]);
        setOpen(false);
      } finally {
        setLoading(false);
      }
    }, 300);
  }

  // ── Keyboard navigation ──────────────────────────────────────────────────
  function handleKeyDown(e: React.KeyboardEvent<HTMLInputElement>) {
    if (!open) return;
    if (e.key === "ArrowDown") {
      e.preventDefault();
      setActiveIndex((i) => Math.min(i + 1, suggestions.length - 1));
    } else if (e.key === "ArrowUp") {
      e.preventDefault();
      setActiveIndex((i) => Math.max(i - 1, -1));
    } else if (e.key === "Enter" && activeIndex >= 0) {
      e.preventDefault();
      selectPort(suggestions[activeIndex]);
    } else if (e.key === "Escape") {
      setOpen(false);
    }
  }

  function selectPort(port: PortSuggestion) {
    setQuery("");
    setSuggestions([]);
    setOpen(false);
    onSelect(port);
  }

  return (
    <div ref={containerRef} className={`relative ${className}`}>
      <div className="relative">
        <svg
          className="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          strokeWidth={2}
        >
          <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input
          type="text"
          value={query}
          onChange={handleChange}
          onKeyDown={handleKeyDown}
          placeholder={placeholder}
          autoComplete="off"
          className="w-full rounded-xl border border-slate-200 bg-white py-3 pl-10 pr-4 text-sm text-slate-800 shadow-sm placeholder:text-slate-400 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200"
        />
        {loading && (
          <div className="absolute right-3 top-1/2 -translate-y-1/2">
            <div className="h-4 w-4 animate-spin rounded-full border-2 border-sky-300 border-t-sky-600" />
          </div>
        )}
      </div>

      {open && suggestions.length > 0 && (
        <ul className="absolute z-50 mt-1 w-full overflow-hidden rounded-xl border border-slate-100 bg-white shadow-xl">
          {suggestions.map((port, idx) => (
            <li key={port.id}>
              <button
                type="button"
                onMouseDown={(e) => e.preventDefault()}
                onClick={() => selectPort(port)}
                className={`flex w-full flex-col px-4 py-3 text-left transition-colors hover:bg-sky-50 ${
                  idx === activeIndex ? "bg-sky-50" : ""
                } ${idx > 0 ? "border-t border-slate-50" : ""}`}
              >
                <span className="flex items-center gap-2 text-sm font-medium text-slate-800">
                  {port.country && countryCode[port.country] && (
                    <img
                      src={`https://flagcdn.com/20x15/${countryCode[port.country]}.png`}
                      srcSet={`https://flagcdn.com/40x30/${countryCode[port.country]}.png 2x`}
                      width={20}
                      height={15}
                      alt={port.country}
                      className="rounded-sm object-cover"
                    />
                  )}
                  {port.name}
                </span>
                <span className="mt-0.5 text-xs text-slate-500">
                  {[port.city, port.region, port.country].filter(Boolean).join(" · ")}
                </span>
              </button>
            </li>
          ))}
        </ul>
      )}
    </div>
  );
}
