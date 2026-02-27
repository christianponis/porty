"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useUIStore } from "@/stores/ui";
import {
  ChartBarIcon,
  BuildingOfficeIcon,
  Squares2X2Icon,
  CalendarDaysIcon,
  UsersIcon,
  TicketIcon,
  StarIcon,
  BanknotesIcon,
  ChevronDoubleLeftIcon,
  ChevronDoubleRightIcon,
  XMarkIcon,
} from "@heroicons/react/24/outline";

const navItems = [
  { label: "Dashboard", href: "/dashboard", icon: ChartBarIcon },
  { label: "Porti", href: "/ports", icon: BuildingOfficeIcon },
  { label: "Posti Barca", href: "/berths", icon: Squares2X2Icon },
  { label: "Prenotazioni", href: "/bookings", icon: CalendarDaysIcon },
  { label: "Utenti", href: "/users", icon: UsersIcon },
  { label: "Convenzioni", href: "/conventions", icon: TicketIcon },
  { label: "Rating", href: "/ratings", icon: StarIcon },
  { label: "Finanza", href: "/transactions", icon: BanknotesIcon },
];

interface SidebarProps {
  mobileOpen: boolean;
  onMobileClose: () => void;
}

export default function Sidebar({ mobileOpen, onMobileClose }: SidebarProps) {
  const pathname = usePathname();
  const { sidebarCollapsed, toggleSidebar } = useUIStore();

  const sidebarContent = (
    <>
      {/* Logo */}
      <div className="flex h-16 items-center justify-between border-b border-slate-700/50 px-4">
        {!sidebarCollapsed && (
          <Link href="/dashboard" className="flex items-center gap-2.5">
            <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-sky-500 to-cyan-400 text-sm font-bold shadow-lg">
              P
            </div>
            <span className="text-base font-bold tracking-tight">
              Porty Admin
            </span>
          </Link>
        )}
        {sidebarCollapsed && (
          <Link href="/dashboard" className="mx-auto">
            <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-sky-500 to-cyan-400 text-sm font-bold shadow-lg">
              P
            </div>
          </Link>
        )}
        {/* Mobile close button */}
        <button
          onClick={onMobileClose}
          className="rounded-lg p-1 text-slate-500 hover:text-slate-300 lg:hidden"
        >
          <XMarkIcon className="h-5 w-5" />
        </button>
      </div>

      {/* Navigation */}
      <nav className="flex-1 overflow-y-auto px-2 py-4">
        <ul className="space-y-1">
          {navItems.map((item) => {
            const isActive =
              pathname === item.href ||
              (item.href !== "/dashboard" && pathname.startsWith(item.href));
            return (
              <li key={item.href}>
                <Link
                  href={item.href}
                  onClick={onMobileClose}
                  title={sidebarCollapsed ? item.label : undefined}
                  className={`flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200 ${
                    isActive
                      ? "bg-sky-600/20 text-sky-400"
                      : "text-slate-400 hover:bg-slate-800 hover:text-slate-200"
                  }`}
                >
                  <item.icon
                    className={`h-5 w-5 flex-shrink-0 ${
                      isActive ? "text-sky-400" : ""
                    }`}
                  />
                  {!sidebarCollapsed && <span>{item.label}</span>}
                </Link>
              </li>
            );
          })}
        </ul>
      </nav>

      {/* Collapse Toggle (desktop only) */}
      <div className="hidden border-t border-slate-700/50 p-2 lg:block">
        <button
          onClick={toggleSidebar}
          className="flex w-full items-center justify-center rounded-lg px-3 py-2 text-sm text-slate-500 transition hover:bg-slate-800 hover:text-slate-300"
        >
          {sidebarCollapsed ? (
            <ChevronDoubleRightIcon className="h-4 w-4" />
          ) : (
            <>
              <ChevronDoubleLeftIcon className="h-4 w-4" />
              <span className="ml-2">Comprimi</span>
            </>
          )}
        </button>
      </div>
    </>
  );

  return (
    <>
      {/* Mobile overlay */}
      {mobileOpen && (
        <div
          className="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm lg:hidden"
          onClick={onMobileClose}
        />
      )}

      {/* Mobile sidebar */}
      <aside
        className={`fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-slate-900 text-white shadow-2xl transition-transform duration-300 lg:hidden ${
          mobileOpen ? "translate-x-0" : "-translate-x-full"
        }`}
      >
        {sidebarContent}
      </aside>

      {/* Desktop sidebar */}
      <aside
        className={`hidden h-screen flex-col border-r border-slate-200 bg-slate-900 text-white transition-all duration-300 lg:flex ${
          sidebarCollapsed ? "w-16" : "w-60"
        }`}
      >
        {sidebarContent}
      </aside>
    </>
  );
}
