@props(['amount' => 0, 'size' => 'md'])

@php
    $sizeClasses = match($size) {
        'sm' => 'text-xs px-2 py-0.5',
        'lg' => 'text-base px-4 py-2',
        default => 'text-sm px-3 py-1',
    };
@endphp

<span class="inline-flex items-center gap-1.5 {{ $sizeClasses }} font-semibold rounded-full bg-seafoam-50 text-seafoam-700 border border-seafoam-200">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="16" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 400 400">
        <path d="M24 219.943C73.8113 211.642 120.446 207.312 169.006 155.742"/>
        <path d="M199.206 130.337C250.916 99.842 326.254 151.462 318.438 211.641C310.211 274.983 221.728 286.903 176.199 281.931C130.669 276.958 109.033 266.778 88.6484 233.191"/>
        <path d="M27.875 242.635C92.0763 233.78 144.795 214.431 190.039 169.025"/>
        <path d="M221.711 148.426C250.633 136.713 322.466 192.991 283.323 225.705C235.939 265.308 146.315 263.113 119.195 227.691"/>
        <path d="M79.9414 189.261C82.332 133.37 147.283 104.175 182.528 122.421C216.417 139.967 245.676 179.692 289.891 197.842"/>
        <path d="M319.883 205.305C347.282 210.877 363.254 212.284 375.998 206.857"/>
        <path d="M104.094 184.721C132.549 119.68 181.475 149.902 198.771 163.673C216.068 177.444 258.788 215.116 282.202 219.8"/>
        <path d="M315.047 228.935C328.797 233.688 357.329 236.533 376 230.429"/>
    </svg>
    {{ number_format($amount, 0) }} Nodi
</span>
