@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-4 pe-4 py-3 border-l-4 border-ocean-500 text-start text-base font-semibold text-ocean-700 bg-ocean-50/50 transition-all duration-200'
            : 'block w-full ps-4 pe-4 py-3 border-l-4 border-transparent text-start text-base font-medium text-slate-600 hover:text-ocean-700 hover:bg-ocean-50/30 hover:border-ocean-300 transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
