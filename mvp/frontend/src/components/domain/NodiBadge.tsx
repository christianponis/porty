/* eslint-disable @next/next/no-img-element */

interface NodiBadgeProps {
  amount: number;
  className?: string;
}

export default function NodiBadge({ amount, className = '' }: NodiBadgeProps) {
  return (
    <span
      className={`inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-emerald-50 to-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200 ${className}`}
    >
      <img
        src="/knot.svg"
        alt=""
        className="h-4 w-4 opacity-70"
      />
      {amount} Nodi
    </span>
  );
}
