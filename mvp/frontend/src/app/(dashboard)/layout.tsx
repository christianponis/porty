'use client';

import { useEffect, useState } from 'react';
import { useRouter, usePathname } from 'next/navigation';
import Link from 'next/link';
import { useAuthStore } from '@/stores/auth';
import { useUIStore } from '@/stores/ui';
import {
  HomeIcon,
  Squares2X2Icon,
  WalletIcon,
  UsersIcon,
  BuildingOfficeIcon,
  CalendarDaysIcon,
  StarIcon,
  BanknotesIcon,
  Bars3Icon,
  XMarkIcon,
  ArrowRightOnRectangleIcon,
} from '@heroicons/react/24/outline';

interface NavItem {
  label: string;
  href: string;
  icon: React.ComponentType<{ className?: string }>;
}

const ownerNav: NavItem[] = [
  { label: 'Dashboard', href: '/owner', icon: HomeIcon },
  { label: 'I miei posti barca', href: '/owner/berths', icon: Squares2X2Icon },
  { label: 'Portafoglio Nodi', href: '/owner/nodi', icon: WalletIcon },
];

const guestNav: NavItem[] = [
  { label: 'Dashboard', href: '/guest', icon: HomeIcon },
  { label: 'Le mie prenotazioni', href: '/guest/bookings', icon: CalendarDaysIcon },
  { label: 'Portafoglio Nodi', href: '/guest/nodi', icon: WalletIcon },
];

const adminNav: NavItem[] = [
  { label: 'Dashboard', href: '/admin', icon: HomeIcon },
  { label: 'Utenti', href: '/admin/users', icon: UsersIcon },
  { label: 'Porti', href: '/admin/ports', icon: BuildingOfficeIcon },
  { label: 'Prenotazioni', href: '/admin/bookings', icon: CalendarDaysIcon },
  { label: 'Rating', href: '/admin/ratings', icon: StarIcon },
  { label: 'Transazioni', href: '/admin/transactions', icon: BanknotesIcon },
];

function getNavItems(role: string | undefined): NavItem[] {
  switch (role) {
    case 'owner':
      return ownerNav;
    case 'guest':
      return guestNav;
    case 'admin':
      return adminNav;
    default:
      return [];
  }
}

export default function DashboardLayout({ children }: { children: React.ReactNode }) {
  const router = useRouter();
  const pathname = usePathname();
  const { user, isAuthenticated, isLoading, init, logout } = useAuthStore();
  const { toasts, removeToast } = useUIStore();
  const [sidebarOpen, setSidebarOpen] = useState(false);

  useEffect(() => {
    init();
  }, [init]);

  useEffect(() => {
    if (!isLoading && !isAuthenticated) {
      router.push('/login');
    }
  }, [isLoading, isAuthenticated, router]);

  if (isLoading) {
    return (
      <div className="flex h-screen items-center justify-center bg-gradient-to-br from-sky-50 via-white to-cyan-50">
        <div className="flex flex-col items-center gap-4">
          <div className="h-10 w-10 animate-spin rounded-full border-4 border-sky-200 border-t-sky-600" />
          <p className="text-sm text-slate-500">Caricamento...</p>
        </div>
      </div>
    );
  }

  if (!isAuthenticated || !user) return null;

  const navItems = getNavItems(user.role);
  const firstName = user.first_name?.trim() ?? '';
  const lastName = user.last_name?.trim() ?? '';
  const initials = `${firstName.charAt(0)}${lastName.charAt(0)}`.toUpperCase() || 'U';

  const handleLogout = () => {
    logout();
    router.push('/login');
  };

  return (
    <div className="flex h-screen bg-gradient-to-br from-sky-50 via-white to-cyan-50">
      {/* Mobile sidebar overlay */}
      {sidebarOpen && (
        <div
          className="fixed inset-0 z-40 bg-sky-950/30 backdrop-blur-sm lg:hidden"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      {/* Sidebar */}
      <aside
        className={`fixed inset-y-0 left-0 z-50 w-64 transform bg-white/80 backdrop-blur-xl border-r border-sky-100 shadow-xl transition-transform duration-300 lg:relative lg:translate-x-0 ${
          sidebarOpen ? 'translate-x-0' : '-translate-x-full'
        }`}
      >
        {/* Logo area */}
        <div className="flex h-16 items-center justify-between px-6 border-b border-sky-100">
          <Link href="/" className="flex items-center gap-2">
            <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-sky-600 to-cyan-500 text-white font-bold text-sm shadow-md">
              P
            </div>
            <span className="text-lg font-bold bg-gradient-to-r from-sky-800 to-cyan-600 bg-clip-text text-transparent">
              Porty
            </span>
          </Link>
          <button
            className="lg:hidden rounded-lg p-1 text-slate-400 hover:text-slate-600"
            onClick={() => setSidebarOpen(false)}
          >
            <XMarkIcon className="h-5 w-5" />
          </button>
        </div>

        {/* User info */}
        <div className="px-6 py-4 border-b border-sky-50">
          <p className="text-sm font-semibold text-slate-800 truncate">
            {user.first_name} {user.last_name}
          </p>
          <p className="text-xs text-slate-500 capitalize">{user.role}</p>
        </div>

        {/* Navigation */}
        <nav className="flex-1 px-3 py-4 space-y-1">
          {navItems.map((item) => {
            const isActive = pathname === item.href || (item.href !== `/${user.role}` && pathname.startsWith(item.href));
            return (
              <Link
                key={item.href}
                href={item.href}
                onClick={() => setSidebarOpen(false)}
                className={`flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 ${
                  isActive
                    ? 'bg-sky-100 text-sky-800 shadow-sm'
                    : 'text-slate-600 hover:bg-sky-50 hover:text-sky-700'
                }`}
              >
                <item.icon className={`h-5 w-5 ${isActive ? 'text-sky-600' : ''}`} />
                {item.label}
              </Link>
            );
          })}
        </nav>

        {/* Logout */}
        <div className="border-t border-sky-100 p-3">
          <button
            onClick={handleLogout}
            className="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors"
          >
            <ArrowRightOnRectangleIcon className="h-5 w-5" />
            Esci
          </button>
        </div>
      </aside>

      {/* Main content */}
      <div className="flex flex-1 flex-col overflow-hidden">
        {/* Top bar (mobile) */}
        <header className="flex h-16 items-center gap-4 border-b border-sky-100 bg-white/60 backdrop-blur-md px-4 lg:px-8">
          <button
            className="lg:hidden rounded-lg p-2 text-slate-500 hover:bg-sky-50"
            onClick={() => setSidebarOpen(true)}
          >
            <Bars3Icon className="h-5 w-5" />
          </button>
          <div className="flex-1" />
          <div className="flex items-center gap-3">
            <div className="hidden sm:block text-right">
              <p className="text-sm font-medium text-slate-700">{user.first_name} {user.last_name}</p>
              <p className="text-xs text-slate-400 capitalize">{user.role}</p>
            </div>
            <div className="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-cyan-500 text-white text-sm font-bold shadow-md">
              {initials}
            </div>
          </div>
        </header>

        {/* Page content */}
        <main className="flex-1 overflow-y-auto p-4 lg:p-8">
          {children}
        </main>
      </div>

      {/* Toast notifications */}
      <div className="fixed bottom-4 right-4 z-[200] flex flex-col gap-2">
        {toasts.map((toast) => (
          <div
            key={toast.id}
            className={`flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium shadow-lg backdrop-blur-sm animate-in slide-in-from-right ${
              toast.type === 'success'
                ? 'bg-emerald-500 text-white'
                : toast.type === 'error'
                ? 'bg-red-500 text-white'
                : toast.type === 'warning'
                ? 'bg-amber-500 text-white'
                : 'bg-sky-500 text-white'
            }`}
          >
            <span>{toast.message}</span>
            <button onClick={() => removeToast(toast.id)} className="ml-2 opacity-70 hover:opacity-100">
              <XMarkIcon className="h-4 w-4" />
            </button>
          </div>
        ))}
      </div>
    </div>
  );
}
