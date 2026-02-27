import type { Metadata } from "next";
import Link from "next/link";
import { Berth, Stats } from "@/lib/api/types";
import BerthCard from "@/components/domain/BerthCard";
import HeroSlideshow from "@/components/domain/HeroSlideshow";
import HeroSearch from "@/components/domain/HeroSearch";
import { serverApiUrl } from "@/lib/api/server";

export const metadata: Metadata = {
  title: "Porty - Il tuo posto barca",
  description:
    "Porty è il marketplace per posti barca in Italia. Trova, prenota e gestisci il tuo ormeggio ideale tra centinaia di porti.",
};

// ─── Server-Side Data Fetching ──────────────────────────────────────────────

async function getHomeData() {
  try {
    const [topRes, latestRes, statsRes] = await Promise.all([
      fetch(serverApiUrl("/api/catalog/berths/top"), {
        next: { revalidate: 300 },
      }).then((r) => (r.ok ? r.json() : { data: [] })),
      fetch(serverApiUrl("/api/catalog/berths/latest"), {
        next: { revalidate: 300 },
      }).then((r) => (r.ok ? r.json() : { data: [] })),
      fetch(serverApiUrl("/api/catalog/stats"), {
        next: { revalidate: 600 },
      }).then((r) =>
        r.ok
          ? r.json()
          : { data: { total_ports: 0, total_berths: 0, total_users: 0, total_bookings: 0 } }
      ),
    ]);
    return {
      topBerths: topRes.data ?? [],
      latestBerths: latestRes.data ?? [],
      stats: statsRes.data ?? { total_ports: 0, total_berths: 0, total_users: 0, total_bookings: 0 },
    };
  } catch {
    return {
      topBerths: [],
      latestBerths: [],
      stats: { total_ports: 0, total_berths: 0, total_users: 0, total_bookings: 0 },
    };
  }
}

// ─── Page Component ─────────────────────────────────────────────────────────

export default async function HomePage() {
  const { topBerths, latestBerths, stats } = await getHomeData();

  return (
    <>
      {/* ── Hero Section ──────────────────────────────────────────────────── */}
      <section className="relative text-white">
        {/* Background (overflow clipped to avoid image bleed) */}
        <div className="absolute inset-0 overflow-hidden">
          <HeroSlideshow />
        </div>

        <div className="relative z-10 mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8 lg:py-32">
          <div className="text-center">
            <h1 className="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl animate-fade-in-up">
              Trova il tuo{" "}
              <span className="text-cyan-300">posto barca</span> ideale
            </h1>
            <p className="mx-auto mt-6 max-w-2xl text-lg text-sky-100 animate-fade-in-up">
              Cerca tra centinaia di ormeggi disponibili nei migliori porti
              italiani. Prenota in pochi clic o scambia con i tuoi Nodi.
            </p>

            {/* ── Search Bar ──────────────────────────────────────────────── */}
            <div className="mx-auto mt-10 max-w-3xl animate-fade-in-up">
              <HeroSearch />
            </div>
          </div>
        </div>

        {/* ── Wave Divider ─────────────────────────────────────────────────── */}
        <div className="wave-bottom">
          <svg
            viewBox="0 0 1440 60"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            preserveAspectRatio="none"
          >
            <path
              d="M0 60L48 53.3C96 46.7 192 33.3 288 28.3C384 23.3 480 26.7 576 33.3C672 40 768 50 864 50C960 50 1056 40 1152 33.3C1248 26.7 1344 23.3 1392 21.7L1440 20V60H1392C1344 60 1248 60 1152 60C1056 60 960 60 864 60C768 60 672 60 576 60C480 60 384 60 288 60C192 60 96 60 48 60H0Z"
              fill="#f8fafc"
            />
          </svg>
        </div>
      </section>

      {/* ── I piu gettonati ───────────────────────────────────────────────── */}
      {topBerths.length > 0 && (
        <section className="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
          <div className="mb-8 flex items-center justify-between">
            <h2 className="text-2xl font-bold text-slate-800 sm:text-3xl">
              I piu gettonati
            </h2>
            <Link
              href="/search"
              className="text-sm font-medium text-sky-600 hover:text-sky-700 transition"
            >
              Vedi tutti &rarr;
            </Link>
          </div>
          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {(topBerths as Berth[]).slice(0, 6).map((berth) => (
              <BerthCard key={berth.id} berth={berth} />
            ))}
          </div>
        </section>
      )}

      {/* ── Ultimi inseriti ───────────────────────────────────────────────── */}
      {latestBerths.length > 0 && (
        <section className="bg-sky-50/50 py-16">
          <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div className="mb-8 flex items-center justify-between">
              <h2 className="text-2xl font-bold text-slate-800 sm:text-3xl">
                Ultimi inseriti
              </h2>
              <Link
                href="/search"
                className="text-sm font-medium text-sky-600 hover:text-sky-700 transition"
              >
                Vedi tutti &rarr;
              </Link>
            </div>
            <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
              {(latestBerths as Berth[]).slice(0, 6).map((berth) => (
                <BerthCard key={berth.id} berth={berth} />
              ))}
            </div>
          </div>
        </section>
      )}

      {/* ── Come funziona ─────────────────────────────────────────────────── */}
      <section className="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <h2 className="mb-12 text-center text-2xl font-bold text-slate-800 sm:text-3xl">
          Come funziona
        </h2>
        <div className="grid gap-8 sm:grid-cols-3">
          {/* Step 1 */}
          <div className="text-center">
            <div className="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-sky-100 text-sky-600">
              <svg className="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <h3 className="mb-2 text-lg font-semibold text-slate-800">Cerca</h3>
            <p className="text-slate-500">
              Esplora centinaia di posti barca nei migliori porti italiani. Filtra per posizione, dimensioni e prezzo.
            </p>
          </div>

          {/* Step 2 */}
          <div className="text-center">
            <div className="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-cyan-100 text-cyan-600">
              <svg className="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                <path strokeLinecap="round" strokeLinejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 className="mb-2 text-lg font-semibold text-slate-800">Prenota</h3>
            <p className="text-slate-500">
              Seleziona le date, scegli se pagare in euro o scambiare con Nodi, e conferma la prenotazione.
            </p>
          </div>

          {/* Step 3 */}
          <div className="text-center">
            <div className="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
              <svg className="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                <path strokeLinecap="round" strokeLinejoin="round" d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
              </svg>
            </div>
            <h3 className="mb-2 text-lg font-semibold text-slate-800">Ormeggia</h3>
            <p className="text-slate-500">
              Goditi il tuo ormeggio in tutta tranquillita. Lascia una recensione e guadagna Nodi per il prossimo viaggio.
            </p>
          </div>
        </div>
      </section>

      {/* ── Stats ─────────────────────────────────────────────────────────── */}
      <section className="ocean-gradient py-16 text-white">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-2 gap-8 sm:grid-cols-4">
            <StatItem
              value={(stats as Stats).total_ports}
              label="Porti"
            />
            <StatItem
              value={(stats as Stats).total_berths}
              label="Posti barca"
            />
            <StatItem
              value={(stats as Stats).total_users}
              label="Utenti"
            />
            <StatItem
              value={(stats as Stats).total_bookings}
              label="Prenotazioni"
            />
          </div>
        </div>
      </section>

      {/* ── CTA Owner ─────────────────────────────────────────────────────── */}
      <section className="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div className="overflow-hidden rounded-3xl bg-gradient-to-r from-sky-900 to-cyan-700 px-8 py-14 text-center text-white shadow-xl sm:px-16">
          <h2 className="text-3xl font-bold sm:text-4xl">
            Hai un posto barca?
          </h2>
          <p className="mx-auto mt-4 max-w-xl text-lg text-sky-100">
            Registrati come proprietario e inizia a guadagnare affittando il tuo
            ormeggio quando non lo utilizzi. Semplice, sicuro e gratuito.
          </p>
          <Link
            href="/register"
            className="mt-8 inline-block rounded-xl bg-white px-8 py-3 font-semibold text-sky-900 shadow-lg transition hover:bg-sky-50 focus:outline-none focus:ring-2 focus:ring-white"
          >
            Registrati come proprietario
          </Link>
        </div>
      </section>
    </>
  );
}

// ─── Stat Item Component ──────────────────────────────────────────────────

function StatItem({ value, label }: { value: number; label: string }) {
  return (
    <div className="text-center">
      <div className="text-3xl font-bold sm:text-4xl lg:text-5xl">
        {(value ?? 0).toLocaleString("it-IT")}
      </div>
      <div className="mt-1 text-sm text-sky-200 sm:text-base">{label}</div>
    </div>
  );
}
