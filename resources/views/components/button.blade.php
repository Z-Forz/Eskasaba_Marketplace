@props([
    'type' => 'button',
    'variant' => 'primary',
    'href' => null,
])

@php
    $classes = match ($variant) {
        'secondary' =>
            'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50',

        'danger' =>
            'bg-red-600 text-white hover:bg-red-700',

        'success' =>
            'bg-emerald-600 text-white hover:bg-emerald-700',

        'warning' =>
            'bg-amber-500 text-white hover:bg-amber-600',

        'ghost' =>
            'bg-transparent text-slate-700 hover:bg-slate-100',

        default =>
            'bg-slate-900 text-white hover:bg-slate-800',
    };

    $base = 'inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition duration-200 focus:outline-none focus:ring-2 focus:ring-slate-300 disabled:pointer-events-none disabled:opacity-50';
@endphp

@if($href)

    <a
        href="{{ $href }}"
        {{ $attributes->merge([
            'class' => "{$base} {$classes}"
        ]) }}
    >
        {{ $slot }}
    </a>

@else

    <button
        type="{{ $type }}"
        {{ $attributes->merge([
            'class' => "{$base} {$classes}"
        ]) }}
    >
        {{ $slot }}
    </button>

@endif