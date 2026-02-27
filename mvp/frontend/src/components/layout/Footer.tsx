import Link from 'next/link';
import Image from 'next/image';

export default function Footer() {
  return (
    <footer className="bg-sky-950 text-sky-200">
      <div className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
          {/* Brand */}
          <div className="col-span-1 sm:col-span-2 lg:col-span-1">
            <Link href="/" className="flex items-center gap-3">
              <Image
                src="/porty_logo.png"
                alt="Porty"
                width={36}
                height={36}
                className="brightness-0 invert"
                style={{ width: 'auto', height: 'auto' }}
              />
              <span className="text-sm text-sky-200">Parte dell&apos;ecosistema EasyPortAI</span>
            </Link>
            <p className="mt-3 text-sm leading-relaxed text-sky-300">
              La piattaforma italiana per prenotare ormeggi e posti barca.
              Scopri, prenota e guadagna Nodi.
            </p>
          </div>

          {/* Esplora */}
          <div>
            <h3 className="text-sm font-semibold uppercase tracking-wider text-white">
              Esplora
            </h3>
            <ul className="mt-4 space-y-2">
              <li>
                <Link
                  href="/search"
                  className="text-sm text-sky-300 transition-colors hover:text-white"
                >
                  Cerca Ormeggi
                </Link>
              </li>
              <li>
                <Link
                  href="/search?top=true"
                  className="text-sm text-sky-300 transition-colors hover:text-white"
                >
                  Ormeggi Top
                </Link>
              </li>
              <li>
                <Link
                  href="/search?new=true"
                  className="text-sm text-sky-300 transition-colors hover:text-white"
                >
                  Nuovi Arrivi
                </Link>
              </li>
            </ul>
          </div>

          {/* Informazioni */}
          <div>
            <h3 className="text-sm font-semibold uppercase tracking-wider text-white">
              Informazioni
            </h3>
            <ul className="mt-4 space-y-2">
              <li>
                <Link
                  href="/about"
                  className="text-sm text-sky-300 transition-colors hover:text-white"
                >
                  Chi Siamo
                </Link>
              </li>
              <li>
                <Link
                  href="/terms"
                  className="text-sm text-sky-300 transition-colors hover:text-white"
                >
                  Termini di Servizio
                </Link>
              </li>
              <li>
                <Link
                  href="/privacy"
                  className="text-sm text-sky-300 transition-colors hover:text-white"
                >
                  Privacy Policy
                </Link>
              </li>
              <li>
                <Link
                  href="/contact"
                  className="text-sm text-sky-300 transition-colors hover:text-white"
                >
                  Contatti
                </Link>
              </li>
            </ul>
          </div>

          {/* Supporto */}
          <div>
            <h3 className="text-sm font-semibold uppercase tracking-wider text-white">
              Supporto
            </h3>
            <ul className="mt-4 space-y-2">
              <li>
                <Link
                  href="/faq"
                  className="text-sm text-sky-300 transition-colors hover:text-white"
                >
                  FAQ
                </Link>
              </li>
              <li>
                <Link
                  href="/help"
                  className="text-sm text-sky-300 transition-colors hover:text-white"
                >
                  Centro Assistenza
                </Link>
              </li>
            </ul>
          </div>
        </div>

        {/* Bottom */}
        <div className="mt-10 border-t border-sky-800 pt-6">
          <p className="text-center text-xs text-sky-400">
            &copy; {new Date().getFullYear()} Porty. Tutti i diritti riservati.
          </p>
        </div>
      </div>
    </footer>
  );
}
