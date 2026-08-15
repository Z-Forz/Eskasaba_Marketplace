@props([
    'title' => 'Belum ada data',
    'description' => 'Belum ada data yang tersedia.',
    'icon' => 'default',
    'action' => null,
    'actionText' => null,
])

<div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-white px-5 py-12 text-center sm:px-8">

    {{-- Icon --}}
    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 sm:h-16 sm:w-16">

        @if($icon === 'cart')

            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.7"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1 5h13m-9-5v5m5-5v5M9 21h.01M17 21h.01"
                />
            </svg>

        @elseif($icon === 'search')

            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.7"
                    d="m21 21-4.35-4.35m2.1-5.4a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z"
                />
            </svg>

        @elseif($icon === 'order')

            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.7"
                    d="M6 2h9l4 4v16H6V2zm9 0v5h5M9 13h6M9 17h6"
                />
            </svg>

        @elseif($icon === 'chat')

            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.7"
                    d="M21 11.5a8.4 8.4 0 01-9 8.5 9.7 9.7 0 01-4-.8L3 21l1.8-4A8.2 8.2 0 013 11.5 8.5 8.5 0 0112 3a8.5 8.5 0 019 8.5z"
                />
            </svg>

        @else

            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="1.7"
                    d="M4 7h16M4 12h16M4 17h10"
                />
            </svg>

        @endif

    </div>

    {{-- Content --}}
    <h3 class="mt-5 text-base font-bold text-slate-900 sm:text-lg">
        {{ $title }}
    </h3>

    <p class="mx-auto mt-2 max-w-md text-xs leading-5 text-slate-500 sm:text-sm">
        {{ $description }}
    </p>

    {{-- Action --}}
    @if($action && $actionText)

        <div class="mt-5">
            <x-button
                :href="$action"
            >
                {{ $actionText }}
            </x-button>
        </div>

    @endif

</div>