@props([
    'type' => 'info',
    'title' => null,
])

@php
    $styles = match ($type) {
        'success' => [
            'wrapper' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
            'icon' => 'text-emerald-600',
        ],
        'warning' => [
            'wrapper' => 'border-amber-200 bg-amber-50 text-amber-800',
            'icon' => 'text-amber-600',
        ],
        'danger', 'error' => [
            'wrapper' => 'border-red-200 bg-red-50 text-red-800',
            'icon' => 'text-red-600',
        ],
        default => [
            'wrapper' => 'border-blue-200 bg-blue-50 text-blue-800',
            'icon' => 'text-blue-600',
        ],
    };
@endphp

<div
    {{ $attributes->merge([
        'class' => "flex gap-3 rounded-2xl border p-4 {$styles['wrapper']}"
    ]) }}
    role="alert"
>
    <div class="shrink-0 {{ $styles['icon'] }}">

        @if(in_array($type, ['danger', 'error']))
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 9v4m0 4h.01M10.29 3.86l-7.5 13A2 2 0 004.5 20h15a2 2 0 001.71-3.14l-7.5-13a2 2 0 00-3.42 0z"
                />
            </svg>

        @elseif($type === 'success')

            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 13l4 4L19 7"
                />
            </svg>

        @elseif($type === 'warning')

            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 9v4m0 4h.01M10.29 3.86l-7.5 13A2 2 0 004.5 20h15a2 2 0 001.71-3.14l-7.5-13a2 2 0 00-3.42 0z"
                />
            </svg>

        @else

            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"
                />
            </svg>

        @endif

    </div>

    <div class="min-w-0 flex-1 text-sm">

        @if($title)
            <p class="font-semibold">
                {{ $title }}
            </p>
        @endif

        <div class="{{ $title ? 'mt-1' : '' }}">
            {{ $slot }}
        </div>

    </div>
</div>