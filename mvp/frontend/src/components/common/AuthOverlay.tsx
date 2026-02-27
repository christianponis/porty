'use client';

import { MouseEvent, ReactNode, useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import { XMarkIcon } from '@heroicons/react/24/outline';

export default function AuthOverlay({ children }: { children: ReactNode }) {
  const router = useRouter();
  const [visible, setVisible] = useState(false);
  const [closing, setClosing] = useState(false);

  useEffect(() => {
    const prevOverflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';

    const raf = requestAnimationFrame(() => setVisible(true));
    const onKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        setClosing(true);
      }
    };
    window.addEventListener('keydown', onKeyDown);

    return () => {
      cancelAnimationFrame(raf);
      window.removeEventListener('keydown', onKeyDown);
      document.body.style.overflow = prevOverflow;
    };
  }, []);

  useEffect(() => {
    if (!closing) return;
    const timeout = setTimeout(() => {
      router.back();
    }, 180);
    return () => clearTimeout(timeout);
  }, [closing, router]);

  const close = () => setClosing(true);

  const stopPropagation = (e: MouseEvent<HTMLDivElement>) => {
    e.stopPropagation();
  };

  return (
    <div
      className={`fixed inset-0 z-[120] flex items-center justify-center px-4 py-8 transition-all duration-200 ${
        visible && !closing ? 'bg-slate-950/40 opacity-100 backdrop-blur-sm' : 'bg-slate-950/0 opacity-0'
      }`}
      onClick={close}
      role="dialog"
      aria-modal="true"
    >
      <div
        className={`relative w-full max-w-md transition-all duration-200 ${
          visible && !closing ? 'translate-y-0 scale-100 opacity-100' : 'translate-y-1 scale-[0.985] opacity-0'
        }`}
        onClick={stopPropagation}
      >
        <button
          type="button"
          onClick={close}
          className="absolute -top-2 -right-2 z-10 rounded-full bg-white p-1.5 text-slate-500 shadow-md transition-colors hover:text-slate-700"
          aria-label="Chiudi"
        >
          <XMarkIcon className="h-5 w-5" />
        </button>
        {children}
      </div>
    </div>
  );
}
