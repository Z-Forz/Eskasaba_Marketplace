<x-layouts.app title="Edit Profil">
    <div class="mx-auto w-full max-w-3xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-8">
            <a
                href="{{ route('profile.index') }}"
                class="text-sm font-medium text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-white"
            >
                ← Kembali ke profil
            </a>

            <h1 class="mt-4 text-2xl font-black tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                Edit Profil Saya
            </h1>

            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                Perbarui foto profil, email, dan nomor telepon akun Anda.
            </p>
        </div>

        @if ($errors->any())
            <x-alert
                type="error"
                :message="$errors->first()"
                class="mb-6"
            />
        @endif

        @if (session('success'))
            <x-alert
                type="success"
                :message="session('success')"
                class="mb-6"
            />
        @endif

        <form
            method="POST"
            action="{{ route('profile.update') }}"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            {{-- Avatar --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 sm:p-8">

                <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                    Foto Profil
                </h2>

                <div class="mt-6 flex flex-col gap-5 sm:flex-row sm:items-center">

                    <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-900 text-3xl font-bold text-white shadow-md dark:bg-slate-700">
                        @if (auth()->user()->avatar)
                            <img
                                src="{{ Storage::url(auth()->user()->avatar) }}"
                                alt="{{ auth()->user()->username }}"
                                class="h-full w-full object-cover"
                            >
                        @else
                            {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                        @endif
                    </div>

                    <div class="w-full">
                        <label for="avatar" class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-300">
                            Upload Foto Baru
                        </label>
                        <input
                            type="file"
                            id="avatar"
                            name="avatar"
                            accept="image/*"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-900 file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-700 file:px-4 file:py-2 file:text-xs file:font-bold file:text-white hover:file:bg-emerald-800 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >

                        <p class="mt-2 text-xs text-slate-400">
                            Format gambar JPG, JPEG, PNG, atau WebP (otomatis dikompresi).
                        </p>
                    </div>

                </div>

            </div>

            {{-- Editable Data --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 sm:p-8">

                <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                    Informasi Akun
                </h2>

                <div class="mt-6 space-y-5">

                    <div>
                        <label for="email" class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-300">
                            Alamat Email
                        </label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            placeholder="Masukkan email..."
                            value="{{ old('email', auth()->user()->email) }}"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                    </div>

                    <div>
                        <label for="phone" class="mb-1.5 block text-xs font-semibold text-slate-700 dark:text-slate-300">
                            Nomor HP / WhatsApp
                        </label>
                        <input
                            id="phone"
                            type="text"
                            name="phone"
                            placeholder="Contoh: 081234567890"
                            value="{{ old('phone', auth()->user()->phone) }}"
                            class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                    </div>

                </div>

                <div class="mt-6 rounded-2xl bg-slate-50 p-4 dark:bg-slate-800/60">
                    <p class="text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                        📌 Username, NIS/NIP, kelas, jurusan, atau mata pelajaran terhubung secara otomatis dengan sistem data induk sekolah dan tidak diubah di sini.
                    </p>
                </div>

            </div>

            {{-- Actions --}}
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                <a
                    href="{{ route('profile.index') }}"
                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-xs font-bold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-emerald-700 px-6 py-3 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-800"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>
</x-layouts.app>