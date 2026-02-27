'use client';

import Link from 'next/link';
import Image from 'next/image';
import { useEffect, useRef, useState } from 'react';
import { useAuthStore } from '@/stores/auth';
import { useUIStore } from '@/stores/ui';
import {
  Bars3Icon,
  XMarkIcon,
  UserCircleIcon,
  ChevronDownIcon,
  ArrowRightOnRectangleIcon,
} from '@heroicons/react/24/outline';

export default function Navbar() {
  const { user, isAuthenticated, logout, init } = useAuthStore();
  const { mobileMenuOpen, setMobileMenuOpen } = useUIStore();
  const [dropdownOpen, setDropdownOpen] = useState(false);
  const dropdownRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    init();
  }, [init]);

  useEffect(() => {
    function handleClickOutside(e: MouseEvent) {
      if (dropdownRef.current && !dropdownRef.current.contains(e.target as Node)) {
        setDropdownOpen(false);
      }
    }
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const dashboardPath =
    user?.role === 'admin'
      ? '/admin'
      : user?.role === 'owner'
        ? '/owner/berths'
        : '/guest/bookings';
  const fullName = [user?.first_name, user?.last_name].filter(Boolean).join(' ').trim() || user?.name || 'Utente';
  const userIdLabel = user?.email || '-';

  return (
    <nav className="sticky top-0 z-50 border-b border-sky-100 bg-white/90 backdrop-blur-md">
      <div className="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        {/* Logo */}
        <Link href="/" className="relative h-10 w-[150px] transition-transform hover:scale-105">
          <Image
            src="/porty_logo.png"
            alt="Porty"
            fill
            sizes="150px"
            className="object-contain object-left"
            priority
          />
        </Link>

        {/* Desktop Nav */}
        <div className="hidden items-center gap-6 md:flex">
          <Link
            href="/search"
            className="text-sm font-medium text-slate-600 transition-colors hover:text-sky-700"
          >
            Cerca Ormeggi
          </Link>

          {isAuthenticated && user ? (
            <>
              <Link
                href={dashboardPath}
                className="text-sm font-medium text-slate-600 transition-colors hover:text-sky-700"
              >
                Dashboard
              </Link>

              {/* User Dropdown */}
              <div className="relative" ref={dropdownRef}>
                <button
                  onClick={() => setDropdownOpen(!dropdownOpen)}
                  className="flex items-center gap-2 rounded-xl border border-sky-200 px-3 py-1.5 text-sm font-medium text-sky-900 transition-colors hover:border-sky-300 hover:bg-sky-50"
                >
                  {user.avatar ? (
                    <img
                      src={user.avatar}
                      alt=""
                      className="h-6 w-6 rounded-full object-cover"
                    />
                  ) : (
                    <UserCircleIcon className="h-6 w-6 text-sky-600" />
                  )}
                  <span className="text-left leading-tight">
                    <span className="block text-xs font-semibold text-sky-900">
                      {fullName}
                    </span>
                    <span className="block max-w-44 truncate text-[11px] font-normal text-slate-500">
                      User ID: {userIdLabel}
                    </span>
                  </span>
                  <ChevronDownIcon className="h-4 w-4" />
                </button>

                {dropdownOpen && (
                  <div className="absolute right-0 mt-2 w-48 rounded-xl border border-sky-100 bg-white py-1 shadow-lg shadow-sky-100/50">
                    <div className="border-b border-sky-50 px-4 py-2">
                      <p className="text-sm font-medium text-sky-900">
                        {user.first_name} {user.last_name}
                      </p>
                      <p className="text-xs text-slate-500">{user.email}</p>
                    </div>
                    <Link
                      href={dashboardPath}
                      className="block px-4 py-2 text-sm text-slate-700 hover:bg-sky-50"
                      onClick={() => setDropdownOpen(false)}
                    >
                      Dashboard
                    </Link>
                    <Link
                      href={`/${user.role}/nodi`}
                      className="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-sky-50"
                      onClick={() => setDropdownOpen(false)}
                    >
                      Nodi
                      <span className="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">
                        {user.nodi_balance}
                      </span>
                    </Link>
                    <hr className="my-1 border-sky-50" />
                    <button
                      onClick={() => {
                        setDropdownOpen(false);
                        logout();
                      }}
                      className="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                    >
                      <ArrowRightOnRectangleIcon className="h-4 w-4" />
                      Esci
                    </button>
                  </div>
                )}
              </div>
            </>
          ) : (
            <div className="flex items-center gap-3">
              <Link
                href="/login"
                scroll={false}
                className="text-sm font-medium text-slate-600 transition-colors hover:text-sky-700"
              >
                Accedi
              </Link>
              <Link
                href="/register"
                scroll={false}
                className="rounded-lg bg-gradient-to-r from-sky-900 to-sky-700 px-4 py-2 text-sm font-medium text-white shadow-md shadow-sky-900/20 transition-all hover:shadow-lg hover:shadow-sky-900/30"
              >
                Registrati
              </Link>
            </div>
          )}
        </div>

        {/* Mobile Hamburger */}
        <button
          className="rounded-lg p-2 text-slate-600 hover:bg-sky-50 md:hidden"
          onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
        >
          {mobileMenuOpen ? (
            <XMarkIcon className="h-6 w-6" />
          ) : (
            <Bars3Icon className="h-6 w-6" />
          )}
        </button>
      </div>

      {/* Mobile Menu */}
      {mobileMenuOpen && (
        <div className="border-t border-sky-100 bg-white md:hidden">
          <div className="space-y-1 px-4 py-3">
            <Link
              href="/search"
              className="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-sky-50"
              onClick={() => setMobileMenuOpen(false)}
            >
              Cerca Ormeggi
            </Link>

            {isAuthenticated && user ? (
              <>
                <Link
                  href={dashboardPath}
                  className="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-sky-50"
                  onClick={() => setMobileMenuOpen(false)}
                >
                  Dashboard
                </Link>
                <Link
                  href={`/${user.role}/nodi`}
                  className="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-sky-50"
                  onClick={() => setMobileMenuOpen(false)}
                >
                  Nodi
                </Link>
                <button
                  onClick={() => {
                    setMobileMenuOpen(false);
                    logout();
                  }}
                  className="block w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50"
                >
                  Esci
                </button>
              </>
            ) : (
              <>
                <Link
                  href="/login"
                  scroll={false}
                  className="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-sky-50"
                  onClick={() => setMobileMenuOpen(false)}
                >
                  Accedi
                </Link>
                <Link
                  href="/register"
                  scroll={false}
                  className="block rounded-lg bg-sky-900 px-3 py-2 text-center text-sm font-medium text-white hover:bg-sky-800"
                  onClick={() => setMobileMenuOpen(false)}
                >
                  Registrati
                </Link>
              </>
            )}
          </div>
        </div>
      )}
    </nav>
  );
}
