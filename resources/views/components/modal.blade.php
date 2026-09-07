@props([
    'id',
    'title' => null,
])

<div
    x-data="{ open: false }"
    x-on:open-modal.window="
        if ($event.detail === '{{ $id }}') {
            open = true
        }
    "
    x-on:close-modal.window="
        if ($event.detail === '{{ $id }}') {
            open = false
        }
    "
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
>
    {{-- Backdrop --}}
    <div
        x-show="open"
        x-transition.opacity
        class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm"
        @click="open = false"
    ></div>

    {{-- Modal --}}
    <div
        x-show="open"
        x-transition:enter="transition duration-200 ease-out"
        x-transition:enter-start="scale-95 opacity-0"
        x-transition:enter-end="scale-100 opacity-100"
        x-transition:leave="transition duration-150 ease-in"
        x-transition:leave-start="scale-100 opacity-100"
        x-transition:leave-end="scale-95 opacity-0"
        class="relative z-10 w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl"
        @click.stop
    >

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-4 sm:px-6">

            @if($title)

                <h2 class="text-base font-bold text-slate-900 sm:text-lg">
                    {{ $title }}
                </h2>

            @endif

            <button
                type="button"
                @click="open = false"
                class="ml-auto flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                aria-label="Tutup"
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>

        </div>

        {{-- Content --}}
        <div class="max-h-[75vh] overflow-y-auto p-4 sm:p-6">
            {{ $slot }}
        </div>

    </div>

</div>