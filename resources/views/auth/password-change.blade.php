<x-layouts.auth
    title="Ganti Password"
>
    <div class="w-full max-w-md">

        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-900 text-xl font-bold text-white">
                🔐
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Ganti Password
            </h1>

            <p class="mt-2 text-sm leading-relaxed text-slate-500">
                Demi keamanan akun, silakan buat password baru
                sebelum melanjutkan.
            </p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

            @if ($errors->any())
                <x-alert
                    type="error"
                    :message="$errors->first()"
                />
            @endif

            <form
                method="POST"
                action="{{ route('password.change.update') }}"
                class="space-y-5"
            >
                @csrf
                @method('PUT')

                <x-input
                    name="current_password"
                    label="Password Saat Ini"
                    type="password"
                    placeholder="Masukkan password sementara"
                    required
                />

                <x-input
                    name="password"
                    label="Password Baru"
                    type="password"
                    placeholder="Masukkan password baru"
                    required
                />

                <x-input
                    name="password_confirmation"
                    label="Konfirmasi Password"
                    type="password"
                    placeholder="Ulangi password baru"
                    required
                />

                <x-button
                    type="submit"
                    class="w-full"
                >
                    Simpan Password Baru
                </x-button>
            </form>

        </div>

    </div>
</x-layouts.auth>