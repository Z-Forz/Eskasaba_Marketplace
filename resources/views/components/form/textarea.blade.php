{{-- resources/views/components/form/textarea.blade.php --}}
{{-- Usage: <x-form.textarea name="description" label="Deskripsi" :value="$item->description" /> --}}

@props([
    'label'       => null,
    'name',
    'rows'        => 5,
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

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @required($required)
        {{ $attributes->merge([
            'class' => 'block w-full resize-none rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-slate-400 focus:ring-2 focus:ring-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500'
        ]) }}
    >{{ old($name, $value) }}</textarea>

    @if ($hint)
        <p class="text-xs text-slate-400">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-xs font-medium text-red-500">{{ $message }}</p>
    @enderror

</div>
