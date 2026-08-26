<x-layouts.auth title="Admin Login - Panel Admin">

    <div class="w-full max-w-md space-y-6">

        {{-- Main Card --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xl shadow-slate-200/50 sm:p-8 dark:border-slate-800 dark:bg-slate-900 dark:shadow-none">

            {{-- Header --}}
            <div class="text-center">

                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-tr from-slate-900 via-slate-800 to-slate-950 text-amber-400 shadow-lg shadow-slate-950/20 border border-slate-700/50">
                    <i class="fa-solid fa-user-shield text-2xl"></i>
                </div>

                <h1 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl dark:text-white">
                    Admin Login
                </h1>

                <p class="mt-1.5 text-xs text-slate-500 sm:text-sm dark:text-slate-400">
                    Masuk ke Panel Pengelolaan Eskasaba Market
                </p>

            </div>

            {{-- Form Section --}}
            <div class="mt-6 sm:mt-8">

                @if (session('status'))
                    <x-alert type="success" :message="session('status')" class="mb-5" />
                @endif

                @if ($errors->any())
                    <x-alert type="error" :message="$errors->first()" class="mb-5" />
                @endif

                <form
                    method="POST"
                    action="{{ route('admin.login.store') }}"
                    class="space-y-4 sm:space-y-5"
                >
                    @csrf

                    {{-- Username Input --}}
                    <div>
                        <label class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Username Admin <span class="text-red-500">*</span>
                        </label>

                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-slate-400">
                                <i class="fa-solid fa-user text-sm"></i>
                            </span>

                            <input
                                type="text"
                                name="username"
                                value="{{ old('username') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Masukkan username admin"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3.5 pl-11 pr-4 text-sm font-semibold text-slate-900 outline-none transition focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 dark:border-slate-800 dark:bg-slate-800/50 dark:text-white dark:focus:border-amber-500"
                            >
                        </div>
                    </div>

                    {{-- Password Input with Toggle --}}
                    <div x-data="{ showPassword: false }">
                        <label class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Kata Sandi <span class="text-red-500">*</span>
                        </label>

                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-slate-400">
                                <i class="fa-solid fa-key text-sm"></i>
                            </span>

                            <input
                                :type="showPassword ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan kata sandi admin"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3.5 pl-11 pr-12 text-sm font-semibold text-slate-900 outline-none transition focus:border-amber-500 focus:bg-white focus:ring-4 focus:ring-amber-500/10 dark:border-slate-800 dark:bg-slate-800/50 dark:text-white dark:focus:border-amber-500"
                            >

                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3.5 flex h-8 w-8 items-center justify-center text-slate-400 transition hover:text-slate-600 dark:hover:text-slate-200"
                                aria-label="Tampilkan kata sandi"
                            >
                                <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button
                        type="submit"
                        class="w-full rounded-2xl bg-slate-900 py-3.5 text-sm font-bold text-amber-400 shadow-lg shadow-slate-900/25 transition duration-200 hover:bg-slate-800 active:scale-[0.98] sm:text-base dark:bg-emerald-700 dark:text-white dark:hover:bg-emerald-800"
                    >
                        <i class="fa-solid fa-shield-halved mr-2"></i> Masuk ke Panel Admin
                    </button>

                </form>

            </div>

        </div>

        {{-- Back Link --}}
        <div class="text-center">
            <a
                href="{{ route('home') }}"
                class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
            >
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>

    </div>

</x-layouts.auth>
