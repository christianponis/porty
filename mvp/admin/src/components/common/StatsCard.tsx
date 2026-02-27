"use client";

interface StatsCardProps {
  title: string;
  value: string | number;
  subtitle?: string;
  icon: React.ComponentType<{ className?: string }>;
  trend?: { value: number; label: string };
  color?: "sky" | "emerald" | "amber" | "purple" | "cyan" | "red";
}

const colorMap: Record<string, { bg: string; icon: string; trend: string }> = {
  sky: { bg: "bg-sky-50", icon: "text-sky-600", trend: "text-sky-600" },
  emerald: { bg: "bg-emerald-50", icon: "text-emerald-600", trend: "text-emerald-600" },
  amber: { bg: "bg-amber-50", icon: "text-amber-600", trend: "text-amber-600" },
  purple: { bg: "bg-purple-50", icon: "text-purple-600", trend: "text-purple-600" },
  cyan: { bg: "bg-cyan-50", icon: "text-cyan-600", trend: "text-cyan-600" },
  red: { bg: "bg-red-50", icon: "text-red-600", trend: "text-red-600" },
};

export default function StatsCard({
  title,
  value,
  subtitle,
  icon: Icon,
  trend,
  color = "sky",
}: StatsCardProps) {
  const c = colorMap[color];
  return (
    <div className="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:shadow-md">
      <div className="flex items-start justify-between">
        <div className="flex-1">
          <p className="text-xs font-medium uppercase tracking-wider text-slate-500">
            {title}
          </p>
          <p className="mt-2 text-2xl font-bold text-slate-800">{value}</p>
          {subtitle && (
            <p className="mt-1 text-xs text-slate-400">{subtitle}</p>
          )}
          {trend && (
            <p className={`mt-1.5 text-xs font-medium ${trend.value >= 0 ? "text-emerald-600" : "text-red-500"}`}>
              {trend.value >= 0 ? "+" : ""}
              {trend.value}% {trend.label}
            </p>
          )}
        </div>
        <div className={`rounded-lg p-2.5 ${c.bg}`}>
          <Icon className={`h-5 w-5 ${c.icon}`} />
        </div>
      </div>
    </div>
  );
}
