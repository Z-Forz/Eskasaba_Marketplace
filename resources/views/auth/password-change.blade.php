<x-layouts.auth title="Ganti Password">
    <div class="w-full max-w-md">

        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-950 text-xl font-bold text-white shadow-md border border-emerald-500/30">
                🔐
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                Ganti Password Akun
            </h1>

            <p class="mt-2 text-sm leading-relaxed text-slate-500 dark:text-slate-400">
                @if (auth()->user()->is_default_password)
                    Demi keamanan akun Anda, silakan buat password baru sebelum melanjutkan.
                @else
                    Masukkan password saat ini dan password baru Anda untuk memperbarui keamanan akun.
                @endif
            </p>
        </div>

        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">

            @if ($errors->any())
                <x-alert type="error" :message="$errors->first()" class="mb-6" />
            @endif

            @if (session('success'))
                <x-alert type="success" :message="session('success')" class="mb-6" />
            @endif

            <form method="POST" action="{{ route('password.change.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                @if (! auth()->user()->is_default_password)
                    <x-input
                        name="current_password"
                        label="Password Saat Ini"
                        type="password"
                        placeholder="Masukkan password saat ini"
                        required
                    />
                @endif

                <x-input
                    name="password"
                    label="Password Baru"
                    type="password"
                    placeholder="Masukkan password baru (minimal 8 karakter)"
                    required
                />

                <x-input
                    name="password_confirmation"
                    label="Konfirmasi Password Baru"
                    type="password"
                    placeholder="Ulangi password baru"
                    required
                />

                <x-button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-3 rounded-2xl">
                    Simpan Password Baru
                </x-button>

                @if (! auth()->user()->is_default_password)
                    <div class="text-center pt-2">
                        <a href="{{ route('profile.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white inline-flex items-center gap-1">
                            <i class="fa-solid fa-arrow-left"></i> Batal & Kembali ke Profil
                        </a>
                    </div>
                @endif
            </form>

        </div>

    </div>
</x-layouts.auth>