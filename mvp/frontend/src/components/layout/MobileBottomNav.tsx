'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useAuthStore } from '@/stores/auth';
import {
  HomeIcon,
  MagnifyingGlassIcon,
  Squares2X2Icon,
  SparklesIcon,
  UserIcon,
} from '@heroicons/react/24/outline';
import {
  HomeIcon as HomeIconSolid,
  MagnifyingGlassIcon as MagnifyingGlassIconSolid,
  Squares2X2Icon as Squares2X2IconSolid,
  SparklesIcon as SparklesIconSolid,
  UserIcon as UserIconSolid,
} from '@heroicons/react/24/solid';

export default function MobileBottomNav() {
  const pathname = usePathname();
  const { user, isAuthenticated } = useAuthStore();

  const dashboardPath =
    user?.role === 'admin'
      ? '/admin'
      : user?.role === 'owner'
        ? '/owner/berths'
        : '/guest/bookings';

  const nodiPath = user?.role ? `/${user.role}/nodi` : '/guest/nodi';

  const items = [
    {
      label: 'Home',
      href: '/',
      icon: HomeIcon,
      activeIcon: HomeIconSolid,
    },
    {
      label: 'Cerca',
      href: '/search',
      icon: MagnifyingGlassIcon,
      activeIcon: MagnifyingGlassIconSolid,
    },
    ...(isAuthenticated
      ? [
          {
            label: 'Dashboard',
            href: dashboardPath,
            icon: Squares2X2Icon,
            activeIcon: Squares2X2IconSolid,
          },
          {
            label: 'Nodi',
            href: nodiPath,
            icon: SparklesIcon,
            activeIcon: SparklesIconSolid,
          },
          {
            label: 'Profilo',
            href: '/profile',
            icon: UserIcon,
            activeIcon: UserIconSolid,
          },
        ]
      : [
          {
            label: 'Accedi',
            href: '/login',
            icon: UserIcon,
            activeIcon: UserIconSolid,
          },
        ]),
  ];

  return (
    <nav className="fixed bottom-0 left-0 right-0 z-50 border-t border-sky-100 bg-white/95 backdrop-blur-md md:hidden">
      <div className="flex items-center justify-around py-2">
        {items.map((item) => {
          const isActive = pathname === item.href || pathname.startsWith(item.href + '/');
          const Icon = isActive ? item.activeIcon : item.icon;

          return (
            <Link
              key={item.href}
              href={item.href}
              className={`flex flex-col items-center gap-0.5 px-3 py-1 text-xs font-medium transition-colors ${
                isActive
                  ? 'text-sky-700'
                  : 'text-slate-400 hover:text-slate-600'
              }`}
            >
              <Icon className="h-5 w-5" />
              <span>{item.label}</span>
            </Link>
          );
        })}
      </div>
    </nav>
  );
}
