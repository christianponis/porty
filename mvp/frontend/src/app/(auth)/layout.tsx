import Link from "next/link";

export default function AuthLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <div className="flex min-h-screen flex-col ocean-gradient-light">
      {/* ── Minimal Header ──────────────────────────────────────────── */}
      <header className="px-6 py-4">
        <Link
          href="/"
          className="inline-flex items-center gap-2 text-xl font-bold text-sky-900"
        >
          <svg
            className="h-8 w-8 text-sky-600"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            strokeWidth={2}
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9V8h2v8zm4 0h-2V8h2v8z"
            />
          </svg>
          Porty
        </Link>
      </header>

      {/* ── Centered Content ────────────────────────────────────────── */}
      <main className="flex flex-1 items-center justify-center px-4 py-8">
        {children}
      </main>
    </div>
  );
}
