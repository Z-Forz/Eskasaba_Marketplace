<x-layouts.auth
    title="Admin Login"
>
    <div class="w-full max-w-md">

        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-900 text-xl font-bold text-white shadow-lg">
                A
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                Admin Login
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Masuk ke panel administrasi Eskasaba Market.
            </p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

            @if (session('status'))
                <x-alert
                    type="success"
                    :message="session('status')"
                    class="mb-4"
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
                action="{{ route('admin.login.store') }}"
                class="space-y-5"
            >
                @csrf

                <x-input
                    name="username"
                    label="Username"
                    type="text"
                    placeholder="Masukkan username admin"
                    :value="old('username')"
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
                    Masuk sebagai Admin
                </x-button>
            </form>
        </div>

        <div class="mt-6 text-center">
            <a
                href="{{ route('home') }}"
                class="text-sm font-medium text-slate-500 transition hover:text-slate-900"
            >
                ← Kembali ke beranda
            </a>
        </div>

    </div>
</x-layouts.auth>
