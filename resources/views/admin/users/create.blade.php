<x-layouts.admin title="Tambah Pengguna">

    <div class="mx-auto max-w-3xl space-y-6">

        {{-- Header --}}
        <div>
            <a
                href="{{ route('admin.users.index') }}"
                class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
            >
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke pengguna
            </a>

            <h1 class="mt-3 text-2xl font-black text-slate-900 dark:text-white">
                Tambah Pengguna Baru
            </h1>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Tambahkan pengguna baru (Siswa atau Guru) ke dalam sistem Eskasaba Marketplace.
            </p>
        </div>

        @if ($errors->any())
            <x-alert type="error" :message="$errors->first()" class="mb-4" />
        @endif

        {{-- Form --}}
        <form
            action="{{ route('admin.users.store') }}"
            method="POST"
            class="space-y-6 rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 sm:p-8"
        >
            @csrf

            <div class="space-y-5">

                <h2 class="font-bold text-slate-900 dark:text-white text-base">
                    Informasi Identitas & Akun
                </h2>

                {{-- Username / Nama --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Nama Lengkap / Username <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        required
                        placeholder="Contoh: Ahmad Budi"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >

                    @error('username')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- NIS / NIP --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        NIS / NIP
                    </label>

                    <input
                        type="text"
                        name="nis_nip"
                        value="{{ old('nis_nip') }}"
                        placeholder="Contoh: 12345678 / 19850101..."
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >

                    @error('nis_nip')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Alamat Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Contoh: email@smkn1bangsri.sch.id"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >

                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Peran (Role) <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="role"
                        required
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >
                        <option value="student" @selected(old('role') === 'student')>Siswa</option>
                        <option value="teacher" @selected(old('role') === 'teacher')>Guru</option>
                    </select>

                    @error('role')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {{-- Class --}}
                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-300">
                            Kelas (khusus untuk Siswa)
                        </label>

                        <input
                            type="text"
                            name="class_room"
                            value="{{ old('class_room') }}"
                            placeholder="Contoh: XII RPL 1, XI TKJ 2"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >

                        @error('class_room')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Phone --}}
                <div>
                    <label class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Nomor HP / WhatsApp
                    </label>

                    <input
                        type="text"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="Contoh: 081234567890"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >
                </div>

            </div>

            {{-- Actions --}}
            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end dark:border-slate-800">
                <a
                    href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-emerald-700 px-6 py-3 text-xs font-bold text-white shadow-xs hover:bg-emerald-800"
                >
                    Simpan Pengguna
                </button>
            </div>

        </form>

    </div>

</x-layouts.admin>
