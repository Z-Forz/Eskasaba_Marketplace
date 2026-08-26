<x-layouts.auth title="Ganti Password - Eskasaba Market">

    <div class="w-full max-w-md space-y-6">

        {{-- Main Card --}}
        <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xl shadow-slate-200/50 sm:p-8 dark:border-slate-800 dark:bg-slate-900 dark:shadow-none">

            {{-- Header --}}
            <div class="text-center">

                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-tr from-emerald-800 to-emerald-600 text-white shadow-lg shadow-emerald-700/20">
                    <i class="fa-solid fa-key text-2xl"></i>
                </div>

                <h1 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl dark:text-white">
                    Ganti Password Akun
                </h1>

                <p class="mt-1.5 text-xs text-slate-500 sm:text-sm dark:text-slate-400">
                    @if (auth()->user()->is_default_password)
                        Demi keamanan akun Anda, silakan buat password baru sebelum melanjutkan.
                    @else
                        Masukkan password saat ini dan password baru Anda untuk memperbarui keamanan akun.
                    @endif
                </p>

            </div>

            {{-- Form Section --}}
            <div class="mt-6 sm:mt-8">

                @if ($errors->any())
                    <x-alert type="error" :message="$errors->first()" class="mb-5" />
                @endif

                @if (session('success'))
                    <x-alert type="success" :message="session('success')" class="mb-5" />
                @endif

                <form
                    method="POST"
                    action="{{ route('password.change.update') }}"
                    class="space-y-4 sm:space-y-5"
                    x-data="{ showCurr: false, showNew: false, showConf: false }"
                >
                    @csrf
                    @method('PUT')

                    @if (! auth()->user()->is_default_password)
                        {{-- Current Password --}}
                        <div>
                            <label class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">
                                Password Saat Ini <span class="text-red-500">*</span>
                            </label>

                            <div class="relative flex items-center">
                                <span class="absolute left-4 text-slate-400">
                                    <i class="fa-solid fa-lock text-sm"></i>
                                </span>

                                <input
                                    :type="showCurr ? 'text' : 'password'"
                                    name="current_password"
                                    required
                                    placeholder="Masukkan password saat ini"
                                    class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3.5 pl-11 pr-12 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-800 dark:bg-slate-800/50 dark:text-white dark:focus:border-emerald-500"
                                >

                                <button
                                    type="button"
                                    @click="showCurr = !showCurr"
                                    class="absolute right-3.5 flex h-8 w-8 items-center justify-center text-slate-400 transition hover:text-slate-600 dark:hover:text-slate-200"
                                    aria-label="Tampilkan password"
                                >
                                    <i class="fa-solid" :class="showCurr ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- New Password --}}
                    <div>
                        <label class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Password Baru <span class="text-red-500">*</span>
                        </label>

                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-slate-400">
                                <i class="fa-solid fa-key text-sm"></i>
                            </span>

                            <input
                                :type="showNew ? 'text' : 'password'"
                                name="password"
                                required
                                placeholder="Masukkan password baru (minimal 8 karakter)"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3.5 pl-11 pr-12 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-800 dark:bg-slate-800/50 dark:text-white dark:focus:border-emerald-500"
                            >

                            <button
                                type="button"
                                @click="showNew = !showNew"
                                class="absolute right-3.5 flex h-8 w-8 items-center justify-center text-slate-400 transition hover:text-slate-600 dark:hover:text-slate-200"
                                aria-label="Tampilkan password"
                            >
                                <i class="fa-solid" :class="showNew ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="mb-2 block text-xs font-bold text-slate-700 dark:text-slate-300">
                            Konfirmasi Password Baru <span class="text-red-500">*</span>
                        </label>

                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-slate-400">
                                <i class="fa-solid fa-check-double text-sm"></i>
                            </span>

                            <input
                                :type="showConf ? 'text' : 'password'"
                                name="password_confirmation"
                                required
                                placeholder="Ulangi password baru"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 py-3.5 pl-11 pr-12 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10 dark:border-slate-800 dark:bg-slate-800/50 dark:text-white dark:focus:border-emerald-500"
                            >

                            <button
                                type="button"
                                @click="showConf = !showConf"
                                class="absolute right-3.5 flex h-8 w-8 items-center justify-center text-slate-400 transition hover:text-slate-600 dark:hover:text-slate-200"
                                aria-label="Tampilkan password"
                            >
                                <i class="fa-solid" :class="showConf ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button
                        type="submit"
                        class="w-full rounded-2xl bg-emerald-700 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-700/25 transition duration-200 hover:bg-emerald-800 active:scale-[0.98] sm:text-base"
                    >
                        <i class="fa-solid fa-floppy-disk mr-2"></i> Simpan Password Baru
                    </button>

                </form>

            </div>

        </div>

        @if (! auth()->user()->is_default_password)
            <div class="text-center">
                <a
                    href="{{ route('profile.index') }}"
                    class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
                >
                    <i class="fa-solid fa-arrow-left"></i> Batal & Kembali ke Profil
                </a>
            </div>
        @endif

    </div>

</x-layouts.auth>