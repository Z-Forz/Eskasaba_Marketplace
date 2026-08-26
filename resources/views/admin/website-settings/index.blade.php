<x-layouts.admin title="Pengaturan Website">
    <div class="mx-auto max-w-5xl space-y-6">

        <div class="mb-6">
            <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-sliders text-emerald-600"></i> Pengaturan Website
            </h1>

            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Kelola informasi utama, hero banner, profil sekolah, kontak, dan footer Eskasaba Marketplace.
            </p>
        </div>

        @if (session('success'))
            <x-alert type="success" :message="session('success')" class="mb-4" />
        @endif

        <form
            action="{{ route('admin.website-settings.update') }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            {{-- Identitas Website --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-globe text-emerald-600"></i> Identitas & Logo
                </h2>

                <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                            <i class="fa-solid fa-heading text-slate-400 mr-1"></i> Nama Website <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="website_name"
                            value="{{ old('website_name', $settings->website_name ?? 'Eskasaba Marketplace') }}"
                            required
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                            <i class="fa-solid fa-image text-slate-400 mr-1"></i> Logo Website
                        </label>

                        <input
                            type="file"
                            name="logo"
                            accept="image/*"
                            class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-xs text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-emerald-700 hover:file:bg-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                        >

                        @if (!empty($settings->logo))
                            <div class="mt-3 flex items-center gap-3">
                                <span class="text-xs text-slate-400 font-semibold">Logo saat ini:</span>
                                <img
                                    src="{{ asset('storage/' . $settings->logo) }}"
                                    class="h-12 w-auto rounded-xl object-contain border border-slate-200 p-1 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
                                    alt="Logo"
                                >
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Hero Section --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-feather text-emerald-600"></i>Beranda Utama
                </h2>

                <div class="mt-5 space-y-5">

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                            <i class="fa-solid fa-heading text-slate-400 mr-1"></i> Judul Banner Utama (Hero Title)
                        </label>
                        <input
                            type="text"
                            name="hero_title"
                            value="{{ old('hero_title', $settings->hero_title ?? '') }}"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                            <i class="fa-solid fa-align-left text-slate-400 mr-1"></i> Deskripsi Singkat Hero
                        </label>

                        <textarea
                            name="hero_description"
                            rows="3"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >{{ old('hero_description', $settings->hero_description ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                            <i class="fa-solid fa-file-image text-slate-400 mr-1"></i> Gambar Banner Hero
                        </label>

                        <input
                            type="file"
                            name="hero_image"
                            accept="image/*"
                            class="block w-full rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-xs text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-emerald-700 hover:file:bg-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200"
                        >

                        @if (!empty($settings->hero_image))
                            <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-700 max-w-xs">
                                <img
                                    src="{{ asset('storage/' . $settings->hero_image) }}"
                                    class="h-32 w-full object-cover"
                                    alt="Hero Preview"
                                >
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Tentang & Visi Misi --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-building-columns text-emerald-600"></i> Profil, Visi, & Misi Sekolah
                </h2>

                <div class="mt-5 space-y-5">

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                            <i class="fa-solid fa-circle-info text-slate-400 mr-1"></i> Tentang Eskasaba Marketplace
                        </label>

                        <textarea
                            name="about"
                            rows="4"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >{{ old('about', $settings->about ?? '') }}</textarea>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                                <i class="fa-solid fa-eye text-slate-400 mr-1"></i> Visi Sekolah / Marketplace
                            </label>

                            <textarea
                                name="vision"
                                rows="3"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >{{ old('vision', $settings->vision ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                                <i class="fa-solid fa-bullseye text-slate-400 mr-1"></i> Misi Sekolah / Marketplace
                            </label>

                            <textarea
                                name="mission"
                                rows="3"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-medium text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                            >{{ old('mission', $settings->mission ?? '') }}</textarea>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Contact & Social Media --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-address-book text-emerald-600"></i> Kontak & Media Sosial
                </h2>

                <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                            <i class="fa-solid fa-location-dot text-slate-400 mr-1"></i> Alamat Sekolah / Marketplace
                        </label>
                        <input
                            type="text"
                            name="address"
                            value="{{ old('address', $settings->address ?? '') }}"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                            <i class="fa-solid fa-envelope text-slate-400 mr-1"></i> Email Resmi
                        </label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $settings->email ?? '') }}"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                            <i class="fa-solid fa-phone text-slate-400 mr-1"></i> Nomor Telepon / WhatsApp Sekolah
                        </label>
                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone', $settings->phone ?? '') }}"
                            placeholder="Contoh: 081234567890"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                            <i class="fa-brands fa-instagram text-slate-400 mr-1"></i> Link Instagram
                        </label>
                        <input
                            type="text"
                            name="instagram"
                            value="{{ old('instagram', $settings->instagram ?? '') }}"
                            placeholder="https://instagram.com/eskasaba"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                            <i class="fa-brands fa-facebook text-slate-400 mr-1"></i> Link Facebook
                        </label>
                        <input
                            type="text"
                            name="facebook"
                            value="{{ old('facebook', $settings->facebook ?? '') }}"
                            placeholder="https://facebook.com/eskasaba"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                            <i class="fa-brands fa-tiktok text-slate-400 mr-1"></i> Link TikTok
                        </label>
                        <input
                            type="text"
                            name="tiktok"
                            value="{{ old('tiktok', $settings->tiktok ?? '') }}"
                            placeholder="https://tiktok.com/@eskasaba"
                            class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                        >
                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">

                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-copyright text-emerald-600"></i> Hak Cipta & Footer
                </h2>

                <div class="mt-5">
                    <label class="mb-2 block text-sm font-semibold text-slate-900 dark:text-white">
                        <i class="fa-solid fa-font text-slate-400 mr-1"></i> Teks Copyright Footer
                    </label>
                    <input
                        type="text"
                        name="copyright"
                        value="{{ old('copyright', $settings->copyright ?? '') }}"
                        class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                    >
                </div>

            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-8 py-3.5 text-sm font-bold text-white shadow-md transition hover:bg-emerald-800"
                >
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Seluruh Pengaturan
                </button>
            </div>

        </form>

    </div>
</x-layouts.admin>