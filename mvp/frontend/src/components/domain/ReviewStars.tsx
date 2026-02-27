'use client';

import { StarIcon } from '@heroicons/react/24/solid';
import { StarIcon as StarOutline } from '@heroicons/react/24/outline';

interface ReviewStarsProps {
  value: number;
  onChange?: (value: number) => void;
  readonly?: boolean;
  size?: 'sm' | 'md' | 'lg';
}

const sizeClasses = {
  sm: 'h-4 w-4',
  md: 'h-5 w-5',
  lg: 'h-6 w-6',
};

export default function ReviewStars({
  value,
  onChange,
  readonly = false,
  size = 'md',
}: ReviewStarsProps) {
  const total = 5;
  const cls = sizeClasses[size];

  return (
    <div className="inline-flex items-center gap-0.5">
      {Array.from({ length: total }, (_, i) => {
        const starValue = i + 1;
        const isFilled = starValue <= value;

        if (readonly || !onChange) {
          return isFilled ? (
            <StarIcon
              key={i}
              className={`${cls} text-amber-400`}
            />
          ) : (
            <StarOutline
              key={i}
              className={`${cls} text-slate-300`}
            />
          );
        }

        return (
          <button
            key={i}
            type="button"
            onClick={() => onChange(starValue)}
            className="transition-transform hover:scale-110 focus:outline-none"
          >
            {isFilled ? (
              <StarIcon className={`${cls} text-amber-400`} />
            ) : (
              <StarOutline
                className={`${cls} text-slate-300 hover:text-amber-300`}
              />
            )}
          </button>
        );
      })}
    </div>
  );
}
