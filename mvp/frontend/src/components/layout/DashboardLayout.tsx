'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useAuthStore } from '@/stores/auth';
import {
  HomeIcon,
  ClipboardDocumentListIcon,
  PlusCircleIcon,
  CalendarDaysIcon,
  SparklesIcon,
  UsersIcon,
  MapPinIcon,
  CurrencyDollarIcon,
  StarIcon,
  Squares2X2Icon,
} from '@heroicons/react/24/outline';

interface NavItem {
  label: string;
  href: string;
  icon: React.ComponentType<React.SVGProps<SVGSVGElement>>;
}

const ownerNav: NavItem[] = [
  { label: 'I Miei Ormeggi', href: '/owner/berths', icon: ClipboardDocumentListIcon },
  { label: 'Nuovo Ormeggio', href: '/owner/berths/create', icon: PlusCircleIcon },
  { label: 'Nodi', href: '/owner/nodi', icon: SparklesIcon },
];

const guestNav: NavItem[] = [
  { label: 'Le Mie Prenotazioni', href: '/guest/bookings', icon: CalendarDaysIcon },
  { label: 'Nodi', href: '/guest/nodi', icon: SparklesIcon },
];

const adminNav: NavItem[] = [
  { label: 'Dashboard', href: '/admin', icon: Squares2X2Icon },
  { label: 'Utenti', href: '/admin/users', icon: UsersIcon },
  { label: 'Porti', href: '/admin/ports', icon: MapPinIcon },
  { label: 'Prenotazioni', href: '/admin/bookings', icon: CalendarDaysIcon },
  { label: 'Valutazioni', href: '/admin/ratings', icon: StarIcon },
  { label: 'Transazioni', href: '/admin/transactions', icon: CurrencyDollarIcon },
];

export default function DashboardLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const pathname = usePathname();
  const { user } = useAuthStore();

  const navItems =
    user?.role === 'admin'
      ? adminNav
      : user?.role === 'owner'
        ? ownerNav
        : guestNav;

  return (
    <div className="flex min-h-[calc(100vh-4rem)]">
      {/* Sidebar - Desktop */}
      <aside className="hidden w-64 shrink-0 border-r border-sky-100 bg-gradient-to-b from-sky-50 to-white lg:block">
        <div className="sticky top-16 p-6">
          <div className="mb-6">
            <div className="flex items-center gap-3">
              <div className="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-600 to-cyan-500 text-sm font-bold text-white shadow-md shadow-sky-200">
                {user?.first_name?.[0]}
                {user?.last_name?.[0]}
              </div>
              <div>
                <p className="text-sm font-semibold text-sky-900">
                  {user?.first_name} {user?.last_name}
                </p>
                <p className="text-xs text-slate-500">
                  {user?.role === 'admin'
                    ? 'Amministrazione'
                    : user?.role === 'owner'
                      ? 'Area Proprietario'
                      : 'Area Ospite'}
                </p>
              </div>
            </div>
          </div>

          <nav className="space-y-1">
            {navItems.map((item) => {
              const isActive =
                pathname === item.href ||
                (item.href !== '/admin' && pathname.startsWith(item.href));
              const Icon = item.icon;

              return (
                <Link
                  key={item.href}
                  href={item.href}
                  className={`flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all ${
                    isActive
                      ? 'bg-sky-100 text-sky-900 shadow-sm'
                      : 'text-slate-600 hover:bg-sky-50 hover:text-sky-800'
                  }`}
                >
                  <Icon className="h-5 w-5 shrink-0" />
                  {item.label}
                </Link>
              );
            })}
          </nav>
        </div>
      </aside>

      {/* Mobile top nav */}
      <div className="fixed top-16 left-0 right-0 z-40 overflow-x-auto border-b border-sky-100 bg-white lg:hidden">
        <div className="flex min-w-max gap-1 px-4 py-2">
          {navItems.map((item) => {
            const isActive =
              pathname === item.href ||
              (item.href !== '/admin' && pathname.startsWith(item.href));

            return (
              <Link
                key={item.href}
                href={item.href}
                className={`whitespace-nowrap rounded-lg px-3 py-1.5 text-xs font-medium transition-colors ${
                  isActive
                    ? 'bg-sky-100 text-sky-900'
                    : 'text-slate-500 hover:bg-sky-50'
                }`}
              >
                {item.label}
              </Link>
            );
          })}
        </div>
      </div>

      {/* Main Content */}
      <main className="flex-1 p-4 pt-14 sm:p-6 lg:p-8 lg:pt-8">{children}</main>
    </div>
  );
}
