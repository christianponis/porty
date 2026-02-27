'use client';

import { useState, useEffect } from 'react';
import Image from 'next/image';

const heroImages = [
  '/hero/porto-di-anzio.jpg',
  '/hero/porto-di-nettuno.jpg',
  '/hero/porto-di-santa-marinella.jpg',
  '/hero/porto-turistico-di-roma.jpg',
];

export default function HeroSlideshow() {
  const [currentIndex, setCurrentIndex] = useState(0);

  useEffect(() => {
    const timer = setInterval(() => {
      setCurrentIndex((prev) => (prev + 1) % heroImages.length);
    }, 5000);
    return () => clearInterval(timer);
  }, []);

  return (
    <div className="absolute inset-0">
      {heroImages.map((src, i) => (
        <Image
          key={src}
          src={src}
          alt=""
          fill
          className={`object-cover transition-opacity duration-1000 ${
            i === currentIndex ? 'opacity-100' : 'opacity-0'
          }`}
          priority={i === 0}
          sizes="100vw"
        />
      ))}
      {/* Overlay gradient */}
      <div className="absolute inset-0 bg-gradient-to-br from-sky-900/85 via-sky-800/75 to-cyan-700/70" />
    </div>
  );
}
