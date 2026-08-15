<x-layouts.app
    title="Edit Profil"
>
    <div class="mx-auto w-full max-w-3xl px-4 py-8 sm:px-6 lg:px-8">

        <div class="mb-8">
            <a
                href="{{ route('profile.index') }}"
                class="text-sm font-medium text-slate-500 transition hover:text-slate-900"
            >
                ← Kembali ke profil
            </a>

            <h1 class="mt-4 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Edit Profil
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Perbarui informasi akun yang dapat Anda ubah.
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
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

                <h2 class="text-lg font-bold text-slate-900">
                    Foto Profil
                </h2>

                <div class="mt-6 flex flex-col gap-5 sm:flex-row sm:items-center">

                    <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-100 text-3xl font-bold text-slate-700">
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
                        <x-input
                            name="avatar"
                            label="Upload Foto Baru"
                            type="file"
                            accept="image/*"
                        />

                        <p class="mt-2 text-xs text-slate-400">
                            Gunakan gambar JPG, JPEG, PNG, atau WebP.
                        </p>
                    </div>

                </div>

            </div>

            {{-- Editable Data --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">

                <h2 class="text-lg font-bold text-slate-900">
                    Informasi Akun
                </h2>

                <div class="mt-6 space-y-5">

                    <!-- <x-input
                        name="username"
                        label="Username"
                        type="text"
                        placeholder="Masukkan username"
                        :value="old('username', auth()->user()->username)"
                    /> -->

                    <x-input
                        name="email"
                        label="Email"
                        type="email"
                        placeholder="Masukkan email"
                        :value="old('email', auth()->user()->email)"
                    />

                </div>

                <div class="mt-6 rounded-2xl bg-slate-50 p-4">
                    <p class="text-sm leading-relaxed text-slate-500">
                        Nama, NIS/NIP, kelas, jurusan, dan identitas sekolah
                        berasal dari sistem sekolah sehingga tidak dapat
                        diubah secara bebas melalui marketplace.
                    </p>
                </div>

            </div>

            {{-- Actions --}}
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                <a
                    href="{{ route('profile.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Batal
                </a>

                <x-button
                    type="submit"
                >
                    Simpan Perubahan
                </x-button>

            </div>

        </form>

    </div>
</x-layouts.app>