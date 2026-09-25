@props([
    'name',
    'id' => null,
    'options' => [], // Array of ['value' => '...', 'label' => '...'] or key => value
    'selected' => null,
    'placeholder' => 'Pilih...',
    'submitOnSelect' => false,
    'icon' => null,
    'class' => '',
])

@php
    $id = $id ?? $name;
    $selectedValue = (string) old($name, $selected ?? '');
    
    // Normalize options into standard [['value' => ..., 'label' => ...]]
    $normalizedOptions = [];

    // If placeholder is provided, add it as first option if not empty
    if ($placeholder) {
        $normalizedOptions[] = [
            'value' => '',
            'label' => $placeholder,
        ];
    }

    foreach ($options as $key => $val) {
        if (is_array($val) && isset($val['value'])) {
            $normalizedOptions[] = [
                'value' => (string)$val['value'],
                'label' => (string)($val['label'] ?? $val['value']),
            ];
        } else {
            $normalizedOptions[] = [
                'value' => (string)$key,
                'label' => (string)$val,
            ];
        }
    }

    // Find label for initial selected value
    $initialLabel = $placeholder ?: ($normalizedOptions[0]['label'] ?? 'Pilih...');
    foreach ($normalizedOptions as $opt) {
        if ((string)$opt['value'] === $selectedValue && $selectedValue !== '') {
            $initialLabel = $opt['label'];
            break;
        }
    }
@endphp

<div
    x-data="{
        open: false,
        value: @js($selectedValue),
        label: @js($initialLabel),
        options: @js($normalizedOptions),
        select(opt) {
            this.value = opt.value;
            this.label = opt.label;
            this.open = false;
            $nextTick(() => {
                if ($refs.hiddenInput) {
                    $refs.hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                @if($submitOnSelect)
                    if ($refs.hiddenInput && $refs.hiddenInput.form) {
                        $refs.hiddenInput.form.submit();
                    }
                @endif
            });
        }
    }"
    @click.away="open = false"
    @keydown.escape.window="open = false"
    class="relative inline-block w-full text-left"
>
    <!-- Hidden input for standard form submission -->
    <input
        type="hidden"
        name="{{ $name }}"
        id="{{ $id }}"
        x-ref="hiddenInput"
        :value="value"
    >

    <!-- Dropdown Trigger Button -->
    <button
        type="button"
        @click="open = !open"
        class="flex w-full items-center justify-between gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 shadow-2xs outline-none transition hover:border-emerald-500/60 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:hover:border-emerald-500/60 {{ $class }}"
        aria-haspopup="listbox"
        :aria-expanded="open"
    >
        <span class="truncate flex items-center gap-2">
            @if($icon)
                <i class="{{ $icon }} text-slate-400"></i>
            @endif
            <span x-text="label"></span>
        </span>
        <i
            class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200 shrink-0"
            :class="{ 'rotate-180 text-emerald-600': open }"
        ></i>
    </button>

    <!-- Custom Dropdown Menu Container -->
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
        class="absolute left-0 right-0 z-50 mt-2 max-h-60 overflow-y-auto rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl ring-1 ring-slate-950/5 dark:border-slate-800 dark:bg-slate-900 dark:ring-white/10"
        style="display: none;"
    >
        <template x-for="opt in options" :key="opt.value">
            <button
                type="button"
                @click="select(opt)"
                class="flex w-full items-center justify-between rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-semibold transition cursor-pointer"
                :class="String(value) === String(opt.value)
                    ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 font-bold'
                    : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800/80'"
            >
                <span x-text="opt.label" class="truncate"></span>
                <i
                    x-show="String(value) === String(opt.value)"
                    class="fa-solid fa-check text-emerald-600 text-xs shrink-0 ml-2"
                ></i>
            </button>
        </template>
    </div>
</div>
