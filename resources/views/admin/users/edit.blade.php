<x-layouts.admin title="Edit Pengguna">

    <div class="mx-auto max-w-3xl space-y-6">

        {{-- Header --}}
        <div>
            <a
                href="{{ route('admin.users.index', $user) }}"
                class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
            >
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke detail pengguna
            </a>

            <h1 class="mt-3 text-2xl font-black text-slate-900 dark:text-white">
                Edit Pengguna: {{ $user->username }}
            </h1>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Perbarui informasi akun, email, NIS/NIP, atau kelas pengguna ini.
            </p>
        </div>

        @if ($errors->any())
            <x-alert type="error" :message="$errors->first()" class="mb-4" />
        @endif

        {{-- Form Edit Data --}}
        <form
            action="{{ route('admin.users.update', $user) }}"
            method="POST"
            class="space-y-6 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 sm:p-8"
        >
            @csrf
            @method('PUT')

            <div class="space-y-5">

                <h2 class="font-bold text-slate-900 dark:text-white text-base flex items-center justify-between">
                    <span>Informasi Identitas & Akun</span>
                    <span class="text-xs font-normal text-slate-400"><i class="fa-solid fa-lock text-amber-500 mr-1"></i> Data Induk Sekolah</span>
                </h2>

                {{-- Username --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Nama Lengkap / Username <span class="text-xs font-normal text-slate-400">(Terkunci)</span>
                    </label>

                    <input
                        type="text"
                        value="{{ $user->username }}"
                        disabled
                        class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-500 cursor-not-allowed dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400"
                    >
                </div>

                {{-- NIS / NIP --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        NIS / NIP <span class="text-xs font-normal text-slate-400">(Terkunci)</span>
                    </label>

                    <input
                        type="text"
                        value="{{ $user->nis_nip ?? '-' }}"
                        disabled
                        class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-500 cursor-not-allowed dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400"
                    >
                </div>

                {{-- Email --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Alamat Email Sekolah <span class="text-xs font-normal text-slate-400">(Terkunci)</span>
                    </label>

                    <input
                        type="email"
                        value="{{ $user->email }}"
                        disabled
                        class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-500 cursor-not-allowed dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400"
                    >
                </div>

                {{-- Role --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Peran (Role) <span class="text-xs font-normal text-slate-400">(Terkunci)</span>
                    </label>

                    <select
                        disabled
                        class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-500 cursor-not-allowed dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400"
                    >
                        <option @selected($user->role === 'student')>Siswa</option>
                        <option @selected($user->role === 'teacher')>Guru</option>
                    </select>
                </div>

                {{-- Class --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Kelas <span class="text-xs font-normal text-slate-400">(Terkunci)</span>
                    </label>

                    <input
                        type="text"
                        value="{{ $user->class_room ?? '-' }}"
                        disabled
                        class="w-full rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-500 cursor-not-allowed dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400"
                    >
                </div>

                {{-- Phone (EDITABLE ONLY) --}}
                <div class="border-t border-slate-100 pt-4 dark:border-slate-800">
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Nomor HP / WhatsApp <span class="text-emerald-600 font-bold">(Dapat Diubah)</span>
                    </label>

                    <input
                        type="text"
                        name="phone"
                        value="{{ old('phone', $user->phone) }}"
                        placeholder="Contoh: 081234567890"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >
                    @error('phone')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-2xl bg-amber-50 p-4 border border-amber-200/80 dark:bg-amber-950/40 dark:border-amber-900/60">
                    <p class="text-xs font-semibold text-amber-800 dark:text-amber-300 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info text-amber-600"></i> Data identitas disinkronkan dari Database Sekolah dan tidak dapat diubah manual. Hanya Nomor HP/WhatsApp yang dapat diubah.
                    </p>
                </div>

            </div>

            {{-- Actions --}}
            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end dark:border-slate-800">
                <a
                    href="{{ route('admin.users.show', $user) }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-emerald-700 px-6 py-3 text-xs font-bold text-white shadow-xs hover:bg-emerald-800"
                >
                    Simpan Perubahan
                </button>
            </div>

        </form>

    </div>

</x-layouts.admin>
