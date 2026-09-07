@props([
    'label' => null,
    'name',
    'type' => 'text',
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'toggleable' => true,
])

@php
    $isPassword = ($type === 'password');
@endphp

<div class="space-y-1.5">

    @if($label)
        <label
            for="{{ $name }}"
            class="block text-sm font-medium text-slate-700"
        >
            {{ $label }}

            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <div
        class="relative"
        @if($isPassword && $toggleable) x-data="{ showPassword: false }" @endif
    >
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            @if($isPassword && $toggleable)
                :type="showPassword ? 'text' : 'password'"
            @endif
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @required($required)
            {{ $attributes->merge([
                'class' => 'block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100' . ($isPassword && $toggleable ? ' pr-11' : '')
            ]) }}
        >

        @if($isPassword && $toggleable)
            <button
                type="button"
                @if(isset($attributes['x-data']) || true)
                    @click="showPassword = !showPassword"
                @endif
                onclick="togglePasswordInputVisibility(this)"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-slate-600 focus:outline-none"
                tabindex="-1"
                aria-label="Toggle password visibility"
            >
                <!-- Eye Icon (password hidden) -->
                <svg x-show="!showPassword" class="h-5 w-5 eye-icon-show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12c.074-.154.152-.307.234-.457C3.498 9.345 7.16 6.75 12 6.75c4.84 0 8.502 2.595 9.73 4.793.082.15.16.303.234.457m-19.964 0c.074.154.152.307.234.457C3.498 14.655 7.16 17.25 12 17.25c4.84 0 8.502-2.595 9.73-4.793.082-.15.16-.303.234-.457M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>

                <!-- Eye Slash Icon (password visible) -->
                <svg x-show="showPassword" class="h-5 w-5 eye-icon-hide hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
            </button>
        @endif
    </div>

    @error($name)
        <p class="text-xs font-medium text-red-600">
            {{ $message }}
        </p>
    @enderror

</div>

@once
    @push('scripts')
        <script>
            function togglePasswordInputVisibility(btn) {
                const container = btn.closest('.relative');
                const input = container.querySelector('input');
                const eyeShow = container.querySelector('.eye-icon-show');
                const eyeHide = container.querySelector('.eye-icon-hide');

                if (input.type === 'password') {
                    input.type = 'text';
                    if (eyeShow) eyeShow.classList.add('hidden');
                    if (eyeHide) eyeHide.classList.remove('hidden');
                } else {
                    input.type = 'password';
                    if (eyeShow) eyeShow.classList.remove('hidden');
                    if (eyeHide) eyeHide.classList.add('hidden');
                }
            }
        </script>
    @endpush
@endonce