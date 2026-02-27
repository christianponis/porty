interface AnchorRatingProps {
  count: number;
  level: 'grey' | 'blue' | 'gold';
  size?: 'sm' | 'md' | 'lg';
}

const levelColors = {
  grey: '#94a3b8',
  blue: '#0284c7',
  gold: '#d97706',
};

const sizeMap = {
  sm: 16,
  md: 20,
  lg: 28,
};

function AnchorIcon({ color, size }: { color: string; size: number }) {
  return (
    <svg
      width={size}
      height={size}
      viewBox="0 0 24 24"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path
        d="M12 2C10.34 2 9 3.34 9 5C9 6.3 9.84 7.4 11 7.82V10H9C8.45 10 8 10.45 8 11C8 11.55 8.45 12 9 12H11V19.92C8.16 19.48 6 17.02 6 14H4C4 17.87 6.93 21.08 10.75 21.82C11.16 21.94 11.58 22 12 22C12.42 22 12.84 21.94 13.25 21.82C17.07 21.08 20 17.87 20 14H18C18 17.02 15.84 19.48 13 19.92V12H15C15.55 12 16 11.55 16 11C16 10.45 15.55 10 15 10H13V7.82C14.16 7.4 15 6.3 15 5C15 3.34 13.66 2 12 2ZM12 4C12.55 4 13 4.45 13 5C13 5.55 12.55 6 12 6C11.45 6 11 5.55 11 5C11 4.45 11.45 4 12 4Z"
        fill={color}
      />
    </svg>
  );
}

export default function AnchorRating({
  count,
  level,
  size = 'md',
}: AnchorRatingProps) {
  const color = levelColors[level];
  const iconSize = sizeMap[size];
  const total = 5;

  return (
    <div className="inline-flex items-center gap-0.5" title={`${count}/5 ancore ${level}`}>
      {Array.from({ length: total }, (_, i) => (
        <span key={i} className={i < count ? 'opacity-100' : 'opacity-20'}>
          <AnchorIcon color={color} size={iconSize} />
        </span>
      ))}
    </div>
  );
}
