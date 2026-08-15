{{-- resources/views/components/form/file.blade.php --}}
{{-- Usage: <x-form.file name="image" label="Foto Produk" accept="image/*" preview /> --}}

@props([
    'label'    => null,
    'name',
    'accept'   => null,
    'required' => false,
    'preview'  => false,   // tampilkan preview gambar setelah dipilih
    'hint'     => 'Format yang didukung: JPG, PNG, WebP. Maksimal 2MB.',
    'current'  => null,    // URL gambar yang sudah ada (edit mode)
])

<div class="space-y-1.5">

    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    {{-- Preview Gambar yang sudah ada (edit mode) --}}
    @if ($current)
        <div class="mb-2">
            <img
                src="{{ $current }}"
                alt="Gambar saat ini"
                class="h-24 w-24 rounded-xl object-cover border border-slate-200"
            >
            <p class="mt-1 text-xs text-slate-400">Gambar saat ini. Upload baru untuk menggantinya.</p>
        </div>
    @endif

    {{-- File Input --}}
    <label
        for="{{ $name }}"
        class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 px-5 py-8 text-center transition hover:border-slate-400 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-slate-500"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
        </svg>
        <div>
            <p class="text-sm font-medium text-slate-600 dark:text-slate-300">
                Klik untuk memilih file
            </p>
            @if ($hint)
                <p class="mt-0.5 text-xs text-slate-400">{{ $hint }}</p>
            @endif
        </div>

        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="file"
            @if($accept) accept="{{ $accept }}" @endif
            @required($required)
            class="sr-only"
            @if($preview) onchange="__previewFile(this, '{{ $name }}_preview')" @endif
            {{ $attributes }}
        >
    </label>

    {{-- Preview setelah pilih (jika prop preview=true) --}}
    @if ($preview)
        <img
            id="{{ $name }}_preview"
            src=""
            alt="Preview"
            class="mt-2 hidden h-24 w-24 rounded-xl object-cover border border-slate-200"
        >

        @once
            <script>
                function __previewFile(input, previewId) {
                    const preview = document.getElementById(previewId);
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = e => {
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');
                        };
                        reader.readAsDataURL(input.files[0]);
                    }
                }
            </script>
        @endonce
    @endif

    @error($name)
        <p class="text-xs font-medium text-red-500">{{ $message }}</p>
    @enderror

</div>
