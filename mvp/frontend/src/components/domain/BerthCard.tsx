import Link from 'next/link';
import Image from 'next/image';
import { Berth } from '@/lib/api/types';
import AnchorRating from './AnchorRating';
import { ArrowsUpDownIcon, ArrowsRightLeftIcon, ArrowDownIcon } from '@heroicons/react/24/outline';

interface BerthCardProps {
  berth: Berth;
}

export default function BerthCard({ berth }: BerthCardProps) {
  // Use berth image if available, otherwise fallback to port image
  const mainImage = berth.images?.[0] ?? berth.port?.image_url ?? null;
  const anchorCount =
    berth.gold_anchor_count ?? berth.blue_anchor_count ?? berth.grey_anchor_count ?? 0;

  return (
    <Link
      href={`/berths/${berth.id}`}
      className="group overflow-hidden rounded-2xl border border-sky-100 bg-white shadow-sm transition-all duration-300 hover:shadow-lg hover:shadow-sky-100/50 hover:-translate-y-0.5"
    >
      {/* Image */}
      <div className="relative aspect-[4/3] overflow-hidden bg-sky-50">
        {mainImage ? (
          <Image
            src={mainImage}
            alt={berth.name}
            fill
            className="object-cover transition-transform duration-500 group-hover:scale-105"
            sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
          />
        ) : (
          <div className="flex h-full items-center justify-center bg-gradient-to-br from-sky-100 to-cyan-50">
            <svg
              className="h-12 w-12 text-sky-300"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={1.5}
                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"
              />
            </svg>
          </div>
        )}

        {/* Sharing badge */}
        {berth.sharing_enabled && (
          <span className="absolute top-3 left-3 rounded-full bg-cyan-500 px-2.5 py-0.5 text-xs font-medium text-white shadow-sm">
            Condivisione
          </span>
        )}

        {/* Price */}
        <div className="absolute bottom-3 right-3 rounded-lg bg-white/90 px-2.5 py-1 text-sm font-bold text-sky-900 shadow-sm backdrop-blur-sm">
          &euro;{berth.price_per_day}
          <span className="text-xs font-normal text-slate-500">/giorno</span>
        </div>
      </div>

      {/* Content */}
      <div className="p-4">
        <h3 className="text-base font-semibold text-sky-900 group-hover:text-sky-700">
          {berth.name}
        </h3>
        <p className="mt-0.5 text-sm text-slate-500">
          {berth.port.name} &middot; {berth.port.city}
        </p>

        <div className="mt-3 flex items-center justify-between">
          {berth.rating_level && (
            <AnchorRating
              count={anchorCount}
              level={berth.rating_level}
              size="sm"
            />
          )}
          <span className="text-xs text-slate-400">
            {berth.review_count}{' '}
            {berth.review_count === 1 ? 'recensione' : 'recensioni'}
          </span>
        </div>

        {/* Specs */}
        <div className="mt-3 flex flex-wrap gap-3 border-t border-sky-50 pt-3">
          <span className="inline-flex items-center gap-1.5 text-xs text-slate-500">
            <ArrowsUpDownIcon className="h-3.5 w-3.5 text-slate-400" />
            <span className="font-medium text-slate-700">{berth.length}m</span> lung.
          </span>
          <span className="inline-flex items-center gap-1.5 text-xs text-slate-500">
            <ArrowsRightLeftIcon className="h-3.5 w-3.5 text-slate-400" />
            <span className="font-medium text-slate-700">{berth.width}m</span> larg.
          </span>
          <span className="inline-flex items-center gap-1.5 text-xs text-slate-500">
            <ArrowDownIcon className="h-3.5 w-3.5 text-slate-400" />
            <span className="font-medium text-slate-700">{berth.max_draft}m</span> pesc.
          </span>
        </div>
      </div>
    </Link>
  );
}
