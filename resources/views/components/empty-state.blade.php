@props([
    'title' => 'Belum ada data',
    'description' => 'Belum ada data yang tersedia.',
    'icon' => 'default',
    'action' => null,
    'actionText' => null,
])

<div class="flex flex-col items-center justify-center rounded-3xl border border-dashed border-slate-200/80 bg-white px-5 py-12 text-center sm:px-8 dark:border-slate-800 dark:bg-slate-900">

    {{-- Font Awesome Icon --}}
    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500 sm:h-16 sm:w-16">

        @if($icon === 'cart')
            <i class="fa-solid fa-cart-shopping text-2xl"></i>
        @elseif($icon === 'search')
            <i class="fa-solid fa-magnifying-glass text-2xl"></i>
        @elseif($icon === 'order')
            <i class="fa-solid fa-receipt text-2xl"></i>
        @elseif($icon === 'chat')
            <i class="fa-solid fa-comments text-2xl"></i>
        @else
            <i class="fa-solid fa-folder-open text-2xl"></i>
        @endif

    </div>

    {{-- Content --}}
    <h3 class="mt-5 text-base font-bold text-slate-900 dark:text-white sm:text-lg">
        {{ $title }}
    </h3>

    <p class="mx-auto mt-2 max-w-md text-xs leading-relaxed text-slate-500 dark:text-slate-400 sm:text-sm">
        {{ $description }}
    </p>

    {{-- Action --}}
    @if($action && $actionText)

        <div class="mt-6">
            <a
                href="{{ $action }}"
                class="inline-flex items-center gap-2 rounded-2xl bg-emerald-700 px-5 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800"
            >
                <i class="fa-solid fa-plus"></i> {{ $actionText }}
            </a>
        </div>

    @endif

</div>