import type { Metadata } from "next";
import Link from "next/link";
import type { BerthDetail, Review } from "@/lib/api/types";
import BerthBookingSidebar from "./BerthBookingSidebar";
import { serverApiUrl } from "@/lib/api/server";

// ─── Data Fetching ────────────────────────────────────────────────────────

async function getBerthData(id: string): Promise<BerthDetail | null> {
  try {
    const res = await fetch(serverApiUrl(`/api/catalog/berths/${id}`), {
      cache: "no-store",
    });
    if (!res.ok) return null;
    const json = await res.json();
    return json.data ?? null;
  } catch {
    return null;
  }
}

// ─── Dynamic Metadata ─────────────────────────────────────────────────────

export async function generateMetadata({
  params,
}: {
  params: Promise<{ id: string }>;
}): Promise<Metadata> {
  const { id } = await params;
  const berth = await getBerthData(id);
  if (!berth) {
    return { title: "Posto barca non trovato" };
  }
  return {
    title: `${berth.name} - ${berth.port.name} | Porty`,
    description: `Prenota il posto barca "${berth.name}" a ${berth.port.city}. ${berth.length}m x ${berth.width}m. A partire da €${berth.price_per_day}/giorno.`,
  };
}

// ─── Page Component ───────────────────────────────────────────────────────

export default async function BerthDetailPage({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  const { id } = await params;
  const berth = await getBerthData(id);

  if (!berth) {
    return (
      <div className="flex min-h-[60vh] items-center justify-center">
        <div className="text-center">
          <h1 className="text-2xl font-bold text-slate-800">
            Posto barca non trovato
          </h1>
          <p className="mt-2 text-slate-500">
            Il posto barca richiesto non esiste o non e piu disponibile.
          </p>
          <Link
            href="/search"
            className="mt-4 inline-block rounded-lg bg-sky-600 px-6 py-2 text-sm font-medium text-white transition hover:bg-sky-700"
          >
            Torna alla ricerca
          </Link>
        </div>
      </div>
    );
  }

  const anchorCount =
    berth.gold_anchor_count ?? berth.blue_anchor_count ?? berth.grey_anchor_count ?? 0;
  const hasAvailability = berth.availability?.some((a) => a.is_available) ?? false;

  return (
    <div className="min-h-screen bg-slate-50">
      {/* ── Image Gallery ──────────────────────────────────────────────── */}
      <div className="bg-slate-200">
        <div className="mx-auto max-w-7xl">
          {berth.images && berth.images.length > 0 ? (
            <div className="grid grid-cols-1 gap-1 sm:grid-cols-2 lg:grid-cols-4 lg:grid-rows-2">
              {/* Main Image */}
              <div className="relative col-span-1 row-span-2 sm:col-span-1 lg:col-span-2 lg:row-span-2">
                <img
                  src={berth.images[0]}
                  alt={berth.name}
                  className="h-64 w-full object-cover sm:h-80 lg:h-full"
                />
              </div>
              {/* Secondary Images */}
              {berth.images.slice(1, 5).map((img, i) => (
                <div key={i} className="relative hidden lg:block">
                  <img
                    src={img}
                    alt={`${berth.name} - ${i + 2}`}
                    className="h-full w-full object-cover"
                  />
                </div>
              ))}
            </div>
          ) : berth.port.image_url ? (
            <div className="relative h-64 sm:h-80">
              <img
                src={berth.port.image_url}
                alt={berth.port.name}
                className="h-full w-full object-cover"
              />
            </div>
          ) : (
            <div className="flex h-64 items-center justify-center bg-gradient-to-br from-sky-100 to-cyan-50 sm:h-80">
              <svg
                className="h-20 w-20 text-sky-300"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                strokeWidth={1}
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
              </svg>
            </div>
          )}
        </div>
      </div>

      {/* ── Content ────────────────────────────────────────────────────── */}
      <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div className="lg:flex lg:gap-10">
          {/* ── Left: Berth Info ──────────────────────────────────────── */}
          <div className="flex-1">
            {/* Breadcrumb */}
            <nav className="mb-4 text-sm text-slate-500">
              <Link href="/" className="hover:text-sky-600 transition">
                Home
              </Link>
              <span className="mx-2">/</span>
              <Link href="/search" className="hover:text-sky-600 transition">
                Cerca
              </Link>
              <span className="mx-2">/</span>
              <span className="text-slate-700">{berth.name}</span>
            </nav>

            {/* Title & Location */}
            <h1 className="text-2xl font-bold text-slate-800 sm:text-3xl">
              {berth.name}
            </h1>
            <p className="mt-1 flex items-center gap-1 text-slate-500">
              <svg
                className="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                strokeWidth={2}
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                />
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                />
              </svg>
              {berth.port.name}, {berth.port.city}
            </p>

            {/* Anchor Rating */}
            <div className="mt-4 flex items-center gap-3">
              {berth.rating_level && (
                <AnchorRating
                  level={berth.rating_level}
                  count={anchorCount}
                />
              )}
              {berth.review_average && (
                <span className="text-sm font-medium text-slate-700">
                  {berth.review_average.toFixed(1)}
                </span>
              )}
              <span className="text-sm text-slate-500">
                {berth.review_count} recensioni
              </span>
              {berth.sharing_enabled && (
                <span className="rounded-full bg-cyan-100 px-3 py-1 text-xs font-medium text-cyan-700">
                  Scambio Nodi
                </span>
              )}
            </div>

            {/* Dimensions */}
            <div className="mt-6 grid grid-cols-3 gap-4 rounded-xl bg-white p-4 shadow-sm">
              <DimensionItem
                label="Lunghezza max"
                value={`${berth.length} m`}
                icon="length"
              />
              <DimensionItem
                label="Larghezza max"
                value={`${berth.width} m`}
                icon="width"
              />
              <DimensionItem
                label="Pescaggio max"
                value={`${berth.max_draft} m`}
                icon="draft"
              />
            </div>

            {/* Description */}
            <div className="mt-8">
              <h2 className="text-lg font-semibold text-slate-800">
                Descrizione
              </h2>
              <p className="mt-3 leading-relaxed text-slate-600">
                {berth.description}
              </p>
            </div>

            {/* Owner Card */}
            {berth.owner && (
              <div className="mt-8 rounded-xl bg-white p-6 shadow-sm">
                <h2 className="mb-4 text-lg font-semibold text-slate-800">
                  Proprietario
                </h2>
                <div className="flex items-center gap-4">
                  <div className="flex h-12 w-12 items-center justify-center rounded-full bg-sky-100 text-lg font-bold text-sky-600">
                    {berth.owner.name?.[0]?.toUpperCase()}
                  </div>
                  <div>
                    <p className="font-medium text-slate-800">
                      {berth.owner.name}
                    </p>
                  </div>
                </div>
              </div>
            )}

            {/* Reviews */}
            <div className="mt-8">
              <h2 className="text-lg font-semibold text-slate-800">
                Recensioni ({berth.review_count})
              </h2>

              {berth.reviews && berth.reviews.length > 0 ? (
                <div className="mt-4 space-y-4">
                  {berth.reviews.map((review) => (
                    <ReviewCard key={review.id} review={review} />
                  ))}
                </div>
              ) : (
                <p className="mt-4 text-slate-500">
                  Nessuna recensione ancora. Sii il primo a recensire!
                </p>
              )}
            </div>
          </div>

          {/* ── Right: Booking Sidebar ────────────────────────────────── */}
          <div className="mt-8 lg:mt-0 lg:w-[380px]">
            {/* Desktop: sticky sidebar */}
            <div className="hidden lg:sticky lg:top-24 lg:block">
              <BerthBookingSidebar
                berthId={berth.id}
                pricePerNight={berth.price_per_day}
                sharingAvailable={berth.sharing_enabled}
                isAvailable={hasAvailability}
              />
            </div>

            {/* Mobile: fixed bottom bar */}
            <div className="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white p-4 shadow-lg lg:hidden">
              <div className="flex items-center justify-between">
                <div>
                  <span className="text-lg font-bold text-slate-800">
                    &euro;{berth.price_per_day}
                  </span>
                  <span className="text-sm text-slate-500"> / giorno</span>
                </div>
                <Link
                  href={`/berths/${berth.id}#booking`}
                  className="rounded-xl bg-sky-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-700"
                >
                  Prenota ora
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Mobile booking section (scrollable) */}
      <div id="booking" className="px-4 pb-24 lg:hidden">
        <BerthBookingSidebar
          berthId={berth.id}
          pricePerNight={berth.price_per_day}
          sharingAvailable={berth.sharing_enabled}
          isAvailable={hasAvailability}
        />
      </div>
    </div>
  );
}

// ─── Sub-components ───────────────────────────────────────────────────────

function AnchorRating({
  level,
  count,
}: {
  level: string;
  count: number;
}) {
  const colorMap: Record<string, string> = {
    gold: "text-yellow-500",
    blue: "text-sky-500",
    grey: "text-slate-400",
  };
  const bgMap: Record<string, string> = {
    gold: "bg-yellow-50",
    blue: "bg-sky-50",
    grey: "bg-slate-50",
  };

  return (
    <div
      className={`inline-flex items-center gap-1.5 rounded-full px-3 py-1 ${bgMap[level] || bgMap.grey}`}
    >
      {Array.from({ length: 5 }, (_, i) => (
        <svg
          key={i}
          className={`h-4 w-4 ${i < count ? (colorMap[level] || colorMap.grey) : 'text-slate-200'}`}
          fill="currentColor"
          viewBox="0 0 24 24"
        >
          <path d="M12 2C10.34 2 9 3.34 9 5C9 6.3 9.84 7.4 11 7.82V10H9C8.45 10 8 10.45 8 11C8 11.55 8.45 12 9 12H11V19.92C8.16 19.48 6 17.02 6 14H4C4 17.87 6.93 21.08 10.75 21.82C11.16 21.94 11.58 22 12 22C12.42 22 12.84 21.94 13.25 21.82C17.07 21.08 20 17.87 20 14H18C18 17.02 15.84 19.48 13 19.92V12H15C15.55 12 16 11.55 16 11C16 10.45 15.55 10 15 10H13V7.82C14.16 7.4 15 6.3 15 5C15 3.34 13.66 2 12 2ZM12 4C12.55 4 13 4.45 13 5C13 5.55 12.55 6 12 6C11.45 6 11 5.55 11 5C11 4.45 11.45 4 12 4Z" />
        </svg>
      ))}
    </div>
  );
}

function DimensionItem({
  label,
  value,
  icon,
}: {
  label: string;
  value: string;
  icon: "length" | "width" | "draft";
}) {
  const Icon = () => {
    if (icon === "length") {
      return (
        <svg className="h-4 w-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.8}>
          <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v16m0-16l-3 3m3-3l3 3m-3 13l-3-3m3 3l3-3" />
        </svg>
      );
    }
    if (icon === "width") {
      return (
        <svg className="h-4 w-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.8}>
          <path strokeLinecap="round" strokeLinejoin="round" d="M4 12h16m-16 0l3-3m-3 3l3 3m13-3l-3-3m3 3l-3 3" />
        </svg>
      );
    }
    return (
      <svg className="h-4 w-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.8}>
        <path strokeLinecap="round" strokeLinejoin="round" d="M12 4v13m0 0l-4-4m4 4l4-4M6 20h12" />
      </svg>
    );
  };

  return (
    <div className="text-center">
      <p className="inline-flex items-center gap-1 text-xs text-slate-500">
        <Icon />
        {label}
      </p>
      <p className="mt-1 text-lg font-semibold text-slate-800">{value}</p>
    </div>
  );
}

function ReviewCard({ review }: { review: Review }) {
  return (
    <div className="rounded-xl bg-white p-5 shadow-sm">
      <div className="flex items-start justify-between">
        <div className="flex items-center gap-3">
          <div className="flex h-10 w-10 items-center justify-center rounded-full bg-sky-100 text-sm font-bold text-sky-600">
            {review.guest.name?.[0]?.toUpperCase()}
          </div>
          <div>
            <p className="font-medium text-slate-800">
              {review.guest.name}
            </p>
            <p className="text-xs text-slate-400">
              {new Date(review.created_at).toLocaleDateString("it-IT", {
                day: "numeric",
                month: "long",
                year: "numeric",
              })}
            </p>
          </div>
        </div>
        <div className="flex items-center gap-1">
          <ReviewStars rating={review.average_rating} />
          {review.is_verified && (
            <span className="ml-2 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">
              Verificata
            </span>
          )}
        </div>
      </div>
      {review.comment && (
        <p className="mt-3 text-sm leading-relaxed text-slate-600">
          {review.comment}
        </p>
      )}
    </div>
  );
}

function ReviewStars({ rating }: { rating: number }) {
  const rounded = Math.round(rating);
  return (
    <div className="flex gap-0.5">
      {Array.from({ length: 5 }, (_, i) => (
        <svg
          key={i}
          className={`h-4 w-4 ${
            i < rounded ? "text-yellow-400" : "text-slate-200"
          }`}
          fill="currentColor"
          viewBox="0 0 20 20"
        >
          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
        </svg>
      ))}
    </div>
  );
}
