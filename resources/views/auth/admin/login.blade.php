<x-layouts.auth title="Admin Login - Panel Admin">

    <div class="w-full max-w-md space-y-6">

        {{-- Main Card --}}
        <div class="overflow-hidden rounded-3xl border border-emerald-100 bg-white p-6 shadow-xl shadow-emerald-950/10 backdrop-blur-xl sm:p-8 dark:border-slate-800 dark:bg-slate-900/95 dark:shadow-none">

            {{-- Header --}}
            <div class="text-center">
                @if(!empty($settings->logo))
                    <img
                        src="{{ asset('storage/' . $settings->logo) }}"
                        alt="{{ $settings->website_name ?? 'Logo' }}"
                        class="mx-auto mb-2 h-24 w-auto object-contain"
                    >
                @else
                    <div class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-lg shadow-emerald-600/20">
                        <i class="fa-solid fa-user-shield text-2xl"></i>
                    </div>
                @endif

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
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3.5 pl-11 pr-4 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-800 dark:bg-slate-800/50 dark:text-white dark:focus:border-emerald-500"
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
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3.5 pl-11 pr-12 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-800 dark:bg-slate-800/50 dark:text-white dark:focus:border-emerald-500"
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
                        class="w-full rounded-2xl bg-emerald-700 py-3.5 text-sm font-bold text-white shadow-lg transition duration-200 hover:bg-emerald-800 active:scale-[0.98] sm:text-base flex items-center justify-center gap-2"
                    >
                        <i class="fa-solid fa-shield-halved"></i> Masuk ke Panel Admin
                    </button>

                </form>

            </div>

        </div>

        {{-- Back Link --}}
        <div class="text-center">
            <a
                href="{{ route('home') }}"
                class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-300 transition hover:text-white dark:text-slate-400 dark:hover:text-white"
            >
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>

    </div>

</x-layouts.auth>
