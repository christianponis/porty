import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "Porty Admin",
  description: "Console di gestione amministrativa Porty",
};

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="it">
      <body className="bg-slate-50 text-slate-900 antialiased">
        {children}
      </body>
    </html>
  );
}
