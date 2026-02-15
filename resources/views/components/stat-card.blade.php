@props(['label', 'value', 'color' => 'ocean', 'icon' => null, 'trend' => null])

@php
$colorMap = [
    'ocean' => ['from-ocean-500', 'to-ocean-600', 'text-ocean-600', 'bg-ocean-50'],
    'blue' => ['from-ocean-500', 'to-ocean-600', 'text-ocean-600', 'bg-ocean-50'],
    'seafoam' => ['from-seafoam-500', 'to-seafoam-600', 'text-seafoam-600', 'bg-seafoam-50'],
    'emerald' => ['from-emerald-500', 'to-emerald-600', 'text-emerald-600', 'bg-emerald-50'],
    'green' => ['from-seafoam-500', 'to-seafoam-600', 'text-seafoam-600', 'bg-seafoam-50'],
    'amber' => ['from-amber-500', 'to-amber-600', 'text-amber-600', 'bg-amber-50'],
    'yellow' => ['from-amber-500', 'to-amber-600', 'text-amber-600', 'bg-amber-50'],
    'red' => ['from-red-500', 'to-red-600', 'text-red-600', 'bg-red-50'],
    'indigo' => ['from-indigo-500', 'to-indigo-600', 'text-indigo-600', 'bg-indigo-50'],
    'purple' => ['from-purple-500', 'to-purple-600', 'text-purple-600', 'bg-purple-50'],
];
$colors = $colorMap[$color] ?? $colorMap['ocean'];
@endphp

<div class="card group hover:shadow-card-hover transition-all duration-300 overflow-hidden relative">
    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r {{ $colors[0] }} {{ $colors[1] }}"></div>

    <div class="p-5 pt-6">
        <div class="flex items-start justify-between">
            <div class="flex-1 min-w-0">
                <dt class="text-sm font-medium text-slate-500 truncate">{{ $label }}</dt>
                <dd class="mt-2 stat-value {{ $colors[2] }}">{!! $value !!}</dd>
                @if($trend)
                    <dd class="mt-1 text-xs font-medium {{ $trend > 0 ? 'text-seafoam-600' : 'text-red-500' }}">
                        {{ $trend > 0 ? '+' : '' }}{{ $trend }}%
                    </dd>
                @endif
            </div>
            @if($icon)
                <div class="ml-4 flex-shrink-0 w-10 h-10 rounded-lg {{ $colors[3] }} flex items-center justify-center">
                    {!! $icon !!}
                </div>
            @endif
        </div>
    </div>
</div>
