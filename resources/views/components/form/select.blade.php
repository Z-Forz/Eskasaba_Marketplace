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

    <x-custom-select
        :name="$name"
        :options="$options"
        :selected="old($name, $selected)"
        :placeholder="$placeholder"
    />

    @if ($hint)
        <p class="text-xs text-slate-400">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-xs font-medium text-red-500">{{ $message }}</p>
    @enderror

</div>
