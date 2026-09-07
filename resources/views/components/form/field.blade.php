{{-- resources/views/components/form/field.blade.php --}}
{{-- Usage: <x-form.field name="name" label="Nama" required /> --}}

@props([
    'label'       => null,
    'name',
    'type'        => 'text',
    'placeholder' => null,
    'value'       => null,
    'required'    => false,
    'hint'        => null,
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

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @required($required)
        {{ $attributes->merge([
            'class' => 'block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500'
        ]) }}
    >

    @if ($hint)
        <p class="text-xs text-slate-400">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-xs font-medium text-red-500">{{ $message }}</p>
    @enderror

</div>
