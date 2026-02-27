"use client";

import { usePathname } from "next/navigation";
import { useAuthStore } from "@/stores/auth";
import {
  ArrowRightOnRectangleIcon,
  Bars3Icon,
  ChevronRightIcon,
  HomeIcon,
} from "@heroicons/react/24/outline";
import Link from "next/link";

const pageNames: Record<string, string> = {
  "/dashboard": "Dashboard",
  "/ports": "Porti",
  "/berths": "Posti Barca",
  "/bookings": "Prenotazioni",
  "/users": "Utenti",
  "/conventions": "Convenzioni",
  "/ratings": "Rating & Recensioni",
  "/transactions": "Finanza",
};

function getBreadcrumbs(pathname: string) {
  const segments = pathname.split("/").filter(Boolean);
  const crumbs: { label: string; href: string }[] = [];

  let accumulated = "";
  for (const seg of segments) {
    accumulated += `/${seg}`;
    const name = pageNames[accumulated];
    if (name) {
      crumbs.push({ label: name, href: accumulated });
    } else if (/^\d+$/.test(seg)) {
      crumbs.push({ label: `#${seg}`, href: accumulated });
    } else if (seg === "edit") {
      crumbs.push({ label: "Modifica", href: accumulated });
    }
  }
  return crumbs;
}

interface HeaderProps {
  onMenuClick: () => void;
}

export default function Header({ onMenuClick }: HeaderProps) {
  const pathname = usePathname();
  const { user, logout } = useAuthStore();
  const crumbs = getBreadcrumbs(pathname);

  const firstName = user?.first_name?.trim() || "";
  const lastName = user?.last_name?.trim() || "";
  const initials =
    `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase() || "A";

  return (
    <header className="flex h-14 items-center justify-between border-b border-slate-200 bg-white px-4 lg:px-6">
      <div className="flex items-center gap-3">
        {/* Mobile hamburger */}
        <button
          onClick={onMenuClick}
          className="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 lg:hidden"
        >
          <Bars3Icon className="h-5 w-5" />
        </button>

        {/* Breadcrumbs */}
        <nav className="hidden items-center gap-1.5 text-sm sm:flex">
        <Link
          href="/dashboard"
          className="text-slate-400 transition hover:text-slate-600"
        >
          <HomeIcon className="h-4 w-4" />
        </Link>
        {crumbs.map((crumb, i) => (
          <span key={crumb.href} className="flex items-center gap-1.5">
            <ChevronRightIcon className="h-3 w-3 text-slate-300" />
            {i === crumbs.length - 1 ? (
              <span className="font-medium text-slate-700">{crumb.label}</span>
            ) : (
              <Link
                href={crumb.href}
                className="text-slate-400 transition hover:text-slate-600"
              >
                {crumb.label}
              </Link>
            )}
          </span>
        ))}
        </nav>

        {/* Mobile title */}
        <span className="text-sm font-medium text-slate-700 sm:hidden">
          {crumbs[crumbs.length - 1]?.label || "Dashboard"}
        </span>
      </div>

      {/* User */}
      <div className="flex items-center gap-3">
        <div className="hidden text-right sm:block">
          <p className="text-sm font-medium text-slate-700">
            {firstName} {lastName}
          </p>
          <p className="text-xs text-slate-400">Amministratore</p>
        </div>
        <div className="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-sky-600 to-cyan-500 text-xs font-bold text-white">
          {initials}
        </div>
        <button
          onClick={() => {
            logout();
            window.location.href = "/login";
          }}
          title="Esci"
          className="rounded-lg p-1.5 text-slate-400 transition hover:bg-red-50 hover:text-red-500"
        >
          <ArrowRightOnRectangleIcon className="h-5 w-5" />
        </button>
      </div>
    </header>
  );
}
