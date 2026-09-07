{{-- resources/views/components/form/select.blade.php --}}
{{-- Usage: <x-form.select name="status" label="Status" :options="['active'=>'Aktif']" :selected="$user->status" /> --}}

@props([
    'label'    => null,
    'name',
    'options'  => [],     // ['value' => 'Label', ...] atau [['value'=>..., 'label'=>...], ...]
    'selected' => null,
    'required' => false,
    'placeholder' => 'Pilih...',
    'hint'     => null,
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

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @required($required)
        {{ $attributes->merge([
            'class' => 'block w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white'
        ]) }}
    >
        @if ($placeholder)
            <option value="" disabled {{ !$selected ? 'selected' : '' }}>{{ $placeholder }}</option>
        @endif

        @foreach ($options as $val => $label)
            @php
                // Support both ['key' => 'label'] and [['value' => ..., 'label' => ...]]
                $optValue = is_array($label) ? $label['value'] : $val;
                $optLabel = is_array($label) ? $label['label'] : $label;
                $isSelected = old($name, $selected) == $optValue;
            @endphp
            <option value="{{ $optValue }}" {{ $isSelected ? 'selected' : '' }}>
                {{ $optLabel }}
            </option>
        @endforeach
    </select>

    @if ($hint)
        <p class="text-xs text-slate-400">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-xs font-medium text-red-500">{{ $message }}</p>
    @enderror

</div>
