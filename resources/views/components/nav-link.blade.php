@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 py-2 rounded-lg text-sm font-semibold text-ocean-700 bg-ocean-50 transition-all duration-200'
            : 'inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-ocean-700 hover:bg-ocean-50/50 transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
