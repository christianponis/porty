import { ReactNode } from 'react';

interface StatsCardProps {
  title: string;
  value: string | number;
  icon?: ReactNode;
  color?: 'sky' | 'cyan' | 'emerald' | 'amber' | 'red';
}

const colorClasses: Record<string, { bg: string; icon: string; border: string }> = {
  sky: {
    bg: 'from-sky-50 to-white',
    icon: 'bg-sky-100 text-sky-600',
    border: 'border-sky-100',
  },
  cyan: {
    bg: 'from-cyan-50 to-white',
    icon: 'bg-cyan-100 text-cyan-600',
    border: 'border-cyan-100',
  },
  emerald: {
    bg: 'from-emerald-50 to-white',
    icon: 'bg-emerald-100 text-emerald-600',
    border: 'border-emerald-100',
  },
  amber: {
    bg: 'from-amber-50 to-white',
    icon: 'bg-amber-100 text-amber-600',
    border: 'border-amber-100',
  },
  red: {
    bg: 'from-red-50 to-white',
    icon: 'bg-red-100 text-red-600',
    border: 'border-red-100',
  },
};

export default function StatsCard({
  title,
  value,
  icon,
  color = 'sky',
}: StatsCardProps) {
  const cls = colorClasses[color];

  return (
    <div
      className={`rounded-xl border bg-gradient-to-br p-5 shadow-sm ${cls.bg} ${cls.border}`}
    >
      <div className="flex items-start justify-between">
        <div>
          <p className="text-sm font-medium text-slate-500">{title}</p>
          <p className="mt-1 text-2xl font-bold text-slate-900">{value}</p>
        </div>
        {icon && (
          <div
            className={`flex h-10 w-10 items-center justify-center rounded-xl ${cls.icon}`}
          >
            {icon}
          </div>
        )}
      </div>
    </div>
  );
}
