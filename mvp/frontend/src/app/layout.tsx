import type { Metadata } from "next";
import { Inter } from "next/font/google";
import "./globals.css";

import Navbar from "@/components/layout/Navbar";
import Footer from "@/components/layout/Footer";
import MobileBottomNav from "@/components/layout/MobileBottomNav";
import Toast from "@/components/common/Toast";

const inter = Inter({
  variable: "--font-inter",
  subsets: ["latin"],
  display: "swap",
});

export const metadata: Metadata = {
  title: {
    default: "Porty - Il tuo posto barca",
    template: "%s | Porty",
  },
  description:
    "Porty è il marketplace per posti barca in Italia. Cerca, prenota e gestisci il tuo ormeggio in modo semplice e sicuro.",
  keywords: ["posti barca", "ormeggio", "marina", "porto", "prenotazione", "Italia"],
  icons: {
    icon: "/porty_logo.png",
    apple: "/porty_logo.png",
  },
};

export default function RootLayout({
  children,
  authModal,
}: Readonly<{
  children: React.ReactNode;
  authModal: React.ReactNode;
}>) {
  return (
    <html lang="it" data-scroll-behavior="smooth">
      <body className={`${inter.variable} antialiased`} suppressHydrationWarning>
        <Navbar />
        <main className="min-h-screen">{children}</main>
        {authModal}
        <Footer />
        <MobileBottomNav />
        <Toast />
      </body>
    </html>
  );
}
