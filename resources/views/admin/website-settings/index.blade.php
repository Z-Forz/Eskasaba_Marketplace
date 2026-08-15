<x-layouts.admin>
    <div class="mx-auto max-w-5xl space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Pengaturan Website
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Kelola informasi dan tampilan utama Eskasaba Market.
            </p>
        </div>

        @if (session('success'))
            <x-alert type="success">
                {{ session('success') }}
            </x-alert>
        @endif

        <form
            action="{{ route('admin.website-settings.update') }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            {{-- Website --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    Identitas Website
                </h2>

                <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">

                    <x-input
                        name="website_name"
                        label="Nama Website"
                        value="{{ old('website_name', $settings->website_name) }}"
                        required
                    />

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Logo
                        </label>

                        <input
                            type="file"
                            name="logo"
                            accept="image/*"
                            class="block w-full rounded-xl border border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                        >

                        @if ($settings->logo)
                            <img
                                src="{{ asset('storage/' . $settings->logo) }}"
                                class="mt-3 h-16 w-auto rounded-lg object-contain"
                                alt="Logo"
                            >
                        @endif
                    </div>

                </div>
            </div>

            {{-- Hero --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    Beranda
                </h2>

                <div class="mt-5 space-y-5">

                    <x-input
                        name="hero_title"
                        label="Judul Hero"
                        value="{{ old('hero_title', $settings->hero_title) }}"
                        required
                    />

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Deskripsi Hero
                        </label>

                        <textarea
                            name="hero_description"
                            rows="4"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >{{ old('hero_description', $settings->hero_description) }}</textarea>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Hero Image
                        </label>

                        <input
                            type="file"
                            name="hero_image"
                            accept="image/*"
                            class="block w-full rounded-xl border border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                        >

                        @if ($settings->hero_image)
                            <img
                                src="{{ asset('storage/' . $settings->hero_image) }}"
                                class="mt-3 h-40 w-full rounded-xl object-cover"
                                alt="Hero"
                            >
                        @endif
                    </div>

                </div>
            </div>

            {{-- About --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    Tentang Website
                </h2>

                <div class="mt-5 space-y-5">

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Tentang
                        </label>

                        <textarea
                            name="about"
                            rows="5"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >{{ old('about', $settings->about) }}</textarea>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Visi
                        </label>

                        <textarea
                            name="vision"
                            rows="3"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >{{ old('vision', $settings->vision) }}</textarea>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Misi
                        </label>

                        <textarea
                            name="mission"
                            rows="3"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >{{ old('mission', $settings->mission) }}</textarea>
                    </div>

                </div>
            </div>

            {{-- Contact --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    Kontak
                </h2>

                <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">

                    <x-input
                        name="address"
                        label="Alamat"
                        value="{{ old('address', $settings->address) }}"
                    />

                    <x-input
                        name="email"
                        label="Email"
                        type="email"
                        value="{{ old('email', $settings->email) }}"
                    />

                    <x-input
                        name="instagram"
                        label="Instagram"
                        value="{{ old('instagram', $settings->instagram) }}"
                    />

                    <x-input
                        name="facebook"
                        label="Facebook"
                        value="{{ old('facebook', $settings->facebook) }}"
                    />

                    <x-input
                        name="tiktok"
                        label="TikTok"
                        value="{{ old('tiktok', $settings->tiktok) }}"
                    />

                </div>
            </div>

            {{-- Footer --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">

                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    Footer
                </h2>

                <div class="mt-5">
                    <x-input
                        name="copyright"
                        label="Copyright"
                        value="{{ old('copyright', $settings->copyright) }}"
                    />
                </div>

            </div>

            {{-- Submit --}}
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="w-full rounded-xl bg-indigo-600 px-6 py-3 font-semibold text-white shadow-sm transition hover:bg-indigo-700 sm:w-auto"
                >
                    Simpan Pengaturan
                </button>
            </div>

        </form>

    </div>
</x-layouts.admin>