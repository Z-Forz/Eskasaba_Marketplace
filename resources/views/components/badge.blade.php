@props([
    'type' => 'default',
    'label' => null,
])

@php
    $classes = match ($type) {
        'success', 'completed', 'paid', 'verified' => 'bg-emerald-50 text-emerald-800 ring-emerald-200/60 dark:bg-emerald-950/60 dark:text-emerald-300 dark:ring-emerald-800',
        'warning', 'pending', 'processing', 'ready_for_pickup', 'confirmed' => 'bg-amber-50 text-amber-800 ring-amber-200/60 dark:bg-amber-950/60 dark:text-amber-300 dark:ring-amber-800',
        'danger', 'cancelled', 'rejected' => 'bg-red-50 text-red-800 ring-red-200/60 dark:bg-red-950/60 dark:text-red-300 dark:ring-red-800',
        'info' => 'bg-blue-50 text-blue-800 ring-blue-200/60 dark:bg-blue-950/60 dark:text-blue-300 dark:ring-blue-800',
        'primary' => 'bg-slate-900 text-white ring-slate-900',
        default => 'bg-slate-100 text-slate-700 ring-slate-200/60 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700',
    };
@endphp

<span {{ $attributes->merge([
    'class' => "inline-flex items-center rounded-full px-3 py-1 text-xs font-bold ring-1 {$classes}"
]) }}>
    {{ $label ?? $slot }}
</span>