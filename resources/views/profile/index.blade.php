<x-layouts.app
    title="Profil"
>
    <div class="mx-auto w-full max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <p class="text-sm font-medium text-slate-500">
                Akun Saya
            </p>

            <div class="mt-1 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                        Profil
                    </h1>

                    <p class="mt-2 text-sm text-slate-500">
                        Informasi akun dan identitas Anda.
                    </p>
                </div>

                <a
                    href="{{ route('profile.edit') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800"
                >
                    Edit Profil
                </a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Profile Card --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                <div class="flex flex-col items-center text-center">

                    <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-full bg-slate-100 text-3xl font-bold text-slate-700">
                        @if (auth()->user()->avatar)
                            <img
                                src="{{ Storage::url(auth()->user()->avatar) }}"
                                alt="{{ auth()->user()->name }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </div>

                    <h2 class="mt-5 text-xl font-bold text-slate-900">
                        {{ auth()->user()->name }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        @{{ auth()->user()->username }}
                    </p>

                    <div class="mt-4">
                        <x-badge
                            :type="auth()->user()->role"
                        >
                            {{ ucfirst(auth()->user()->role) }}
                        </x-badge>
                    </div>
                </div>

            </div>

            {{-- Identity --}}
            <div class="lg:col-span-2 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

                <h2 class="text-lg font-bold text-slate-900">
                    Informasi Identitas
                </h2>

                <div class="mt-6 grid gap-5 sm:grid-cols-2">

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Nama
                        </p>

                        <p class="mt-1 font-medium text-slate-900">
                            {{ auth()->user()->name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            NIS / NIP
                        </p>

                        <p class="mt-1 font-medium text-slate-900">
                            {{ auth()->user()->school_number ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Email
                        </p>

                        <p class="mt-1 break-all font-medium text-slate-900">
                            {{ auth()->user()->email ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Tipe
                        </p>

                        <p class="mt-1 font-medium capitalize text-slate-900">
                            {{ auth()->user()->type ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Kelas
                        </p>

                        <p class="mt-1 font-medium text-slate-900">
                            {{ auth()->user()->class ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Jurusan
                        </p>

                        <p class="mt-1 font-medium text-slate-900">
                            {{ auth()->user()->major ?? '-' }}
                        </p>
                    </div>

                </div>

            </div>

        </div>

        {{-- Seller Status --}}
        @if (auth()->user()->seller)
            <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">

                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">
                            Status Seller
                        </p>

                        <h2 class="mt-1 text-lg font-bold text-slate-900">
                            Akun Penjual
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Status pendaftaran penjual Anda saat ini.
                        </p>
                    </div>

                    <x-badge
                        :type="auth()->user()->seller->status"
                    >
                        {{ ucfirst(auth()->user()->seller->status) }}
                    </x-badge>

                </div>

            </div>
        @endif

    </div>
</x-layouts.app>