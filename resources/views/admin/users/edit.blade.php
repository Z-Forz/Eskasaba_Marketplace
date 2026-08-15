<x-layouts.admin>

    <div class="mx-auto max-w-3xl space-y-6">

        {{-- Header --}}
        <div>

            <a
                href="{{ route('admin.users.show', $user) }}"
                class="text-sm text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
            >
                ← Kembali ke detail
            </a>

            <h1 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white">
                Edit Pengguna
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Perbarui informasi akun pengguna.
            </p>

        </div>

        {{-- Form --}}
        <form
            action="{{ route('admin.users.update', $user) }}"
            method="POST"
            class="space-y-6 rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 dark:border-gray-700 dark:bg-gray-900"
        >

            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>

                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Nama
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    required
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                >

                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

            {{-- Email --}}
            <div>

                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                >

                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

            {{-- Role --}}
            <div>

                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Role
                </label>

                <select
                    name="role"
                    required
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                >

                    <option value="student" @selected(old('role', $user->role) === 'student')}>
                        Student
                    </option>

                    <option value="teacher" @selected(old('role', $user->role) === 'teacher')}>
                        Teacher
                    </option>

                    <option value="admin" @selected(old('role', $user->role) === 'admin')}>
                        Admin
                    </option>

                </select>

                @error('role')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

            {{-- School Profile --}}
            <div>

                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Profil Sekolah
                </label>

                <select
                    name="school_profile_id"
                    class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                >

                    <option value="">
                        Tidak ada
                    </option>

                    @foreach ($schoolProfiles ?? [] as $profile)

                        <option
                            value="{{ $profile->id }}"
                            @selected(old('school_profile_id', $user->school_profile_id) == $profile->id)
                        >
                            {{ $profile->name }} — {{ $profile->school_number }}
                        </option>

                    @endforeach

                </select>

                @error('school_profile_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror

            </div>

            {{-- Password --}}
            <div class="border-t border-gray-100 pt-5 dark:border-gray-800">

                <h2 class="font-semibold text-gray-900 dark:text-white">
                    Password
                </h2>

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    Kosongkan jika password tidak ingin diubah.
                </p>

                <div class="mt-4">

                    <input
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        placeholder="Password baru"
                        class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm outline-none focus:border-gray-400 focus:ring-2 focus:ring-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    >

                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                </div>

            </div>

            {{-- Actions --}}
            <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end dark:border-gray-800">

                <a
                    href="{{ route('admin.users.show', $user) }}"
                    class="inline-flex justify-center rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="rounded-xl bg-gray-900 px-5 py-3 text-sm font-semibold text-white hover:opacity-90 dark:bg-white dark:text-gray-900"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</x-layouts.admin>
```
