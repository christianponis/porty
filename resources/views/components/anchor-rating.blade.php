@props(['count' => 0, 'level' => 'grey', 'size' => 'md', 'showLabel' => false])

@php
    $colors = match($level) {
        'gold' => 'text-amber-500',
        'blue' => 'text-ocean-500',
        default => 'text-slate-400',
    };
    $emptyColor = 'text-slate-200';
    $sizeClass = match($size) {
        'sm' => 'w-4 h-4',
        'lg' => 'w-7 h-7',
        default => 'w-5 h-5',
    };
    $labels = match($level) {
        'gold' => 'Certificato Porty',
        'blue' => 'Verificato Community',
        default => 'Autovalutazione',
    };
@endphp

<div class="inline-flex items-center gap-1">
    @for ($i = 1; $i <= 5; $i++)
        <svg class="{{ $sizeClass }} {{ $i <= $count ? $colors : $emptyColor }}" viewBox="0 0 204.851 204.851" fill="currentColor">
            <path fill-rule="evenodd" d="M139.518,128.595l16.834,16.336c0,0-20.644,29.877-42.725,30.473c0.479,0,0.117-84.092,0.039-104.472c14.694-4.797,25.402-18.182,25.402-34.117c0-20.009-16.697-36.218-37.273-36.218c-20.615,0-37.312,16.209-37.312,36.208c0,15.671,10.376,28.929,24.748,33.961l0.098,104.277c-26.643-1.837-42.061-27.474-42.061-27.474l17.997-17.41L0,120.505l9.887,63.301l17.362-16.795c15.036,12.105,32.017,37.244,72.876,37.244c51.332-1.309,63.184-28.939,76.344-39.804l18.993,18.514l9.389-63.907L139.518,128.595z M82.558,36.208c0-10.298,8.608-18.661,19.218-18.661s19.257,8.363,19.257,18.661c0,10.327-8.647,18.681-19.257,18.681S82.558,46.535,82.558,36.208z"/>
        </svg>
    @endfor
    @if ($showLabel && $count > 0)
        <span class="text-xs font-medium {{ $colors }} ml-1">{{ $labels }}</span>
    @endif
</div>
