@props([
    'type' => 'info',
    'title' => null,
    'message' => null,
    'dismissible' => true,
    'dismissAfter' => 4000,
])

@php
    $styles = match ($type) {
        'success' => [
            'wrapper' => 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300',
            'icon'    => 'text-emerald-600 dark:text-emerald-400',
            'close'   => 'text-emerald-600 hover:text-emerald-900 dark:text-emerald-400 dark:hover:text-white',
        ],
        'warning' => [
            'wrapper' => 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-300',
            'icon'    => 'text-amber-600 dark:text-amber-400',
            'close'   => 'text-amber-600 hover:text-amber-900 dark:text-amber-400 dark:hover:text-white',
        ],
        'danger', 'error' => [
            'wrapper' => 'border-red-200 bg-red-50 text-red-800 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-300',
            'icon'    => 'text-red-600 dark:text-red-400',
            'close'   => 'text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-white',
        ],
        default => [
            'wrapper' => 'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-900/50 dark:bg-blue-950/40 dark:text-blue-300',
            'icon'    => 'text-blue-600 dark:text-blue-400',
            'close'   => 'text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-white',
        ],
    };

    $content = $message ?? (string) $slot;
@endphp

@if (trim($content) !== '')
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, {{ (int) $dismissAfter }})"
        x-show="show"
        x-transition:leave="transition ease-in duration-300 transform opacity-100 scale-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        {{ $attributes->merge([
            'class' => "js-auto-dismiss flex items-start gap-3 rounded-2xl border p-4 shadow-sm transition-all duration-300 {$styles['wrapper']}"
        ]) }}
        role="alert"
        data-dismiss-after="{{ (int) $dismissAfter }}"
    >
        <div class="shrink-0 mt-0.5 {{ $styles['icon'] }}">
            @if(in_array($type, ['danger', 'error']))
                <i class="fa-solid fa-circle-xmark text-lg"></i>
            @elseif($type === 'success')
                <i class="fa-solid fa-circle-check text-lg"></i>
            @elseif($type === 'warning')
                <i class="fa-solid fa-triangle-exclamation text-lg"></i>
            @else
                <i class="fa-solid fa-circle-info text-lg"></i>
            @endif
        </div>

        <div class="min-w-0 flex-1 text-sm font-medium leading-relaxed">
            @if($title)
                <p class="font-bold text-base mb-0.5">
                    {{ $title }}
                </p>
            @endif

            <div>
                {!! $content !!}
            </div>
        </div>

        @if($dismissible)
            <button
                type="button"
                @click="show = false"
                onclick="const el = this.closest('[role=\'alert\']'); if(el) { el.style.opacity='0'; el.style.transform='scale(0.95)'; setTimeout(() => el.remove(), 300); }"
                class="shrink-0 rounded-xl p-1 transition opacity-70 hover:opacity-100 {{ $styles['close'] }}"
                aria-label="Tutup notifikasi"
            >
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        @endif
    </div>
@endif