<x-layouts.auth
    title="Login"
>
    <div class="w-full max-w-md items-center justify-center rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-900 text-xl font-bold text-white shadow-lg">
                E
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Selamat Datang
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Masuk ke Eskasaba Market menggunakan NIS/NIP.
            </p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

            @if (session('status'))
                <x-alert
                    type="success"
                    :message="session('status')"
                />
            @endif

            @if ($errors->any())
                <x-alert
                    type="error"
                    :message="$errors->first()"
                />
            @endif

            <form
                method="POST"
                action="{{ route('login.store') }}"
                class="space-y-5"
            >
                @csrf

                <x-input
                    name="nis_nip"
                    label="NIS / NIP"
                    type="text"
                    placeholder="Masukkan NIS atau NIP"
                    :value="old('nis_nip')"
                    required
                    autofocus
                />

                <x-input
                    name="password"
                    label="Password"
                    type="password"
                    placeholder="Masukkan password"
                    required
                />

                <x-button
                    type="submit"
                    class="w-full"
                >
                    Masuk
                </x-button>
            </form>

            <div class="mt-6 border-t border-slate-100 pt-5 text-center">
                <p class="text-xs leading-relaxed text-slate-500">
                    Login siswa dan guru menggunakan identitas sekolah.
                    Data akun diverifikasi melalui sistem sekolah.
                </p>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a
                href="{{ route('home') }}"
                class="inline-flex items-center text-sm font-medium text-slate-500 transition hover:text-slate-900"
            >
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke beranda
            </a>
        </div>
    </div>
</x-layouts.auth>