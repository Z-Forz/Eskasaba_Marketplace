{{-- resources/views/components/form.blade.php --}}

@props([
    'action',                  // URL tujuan form
    'method'  => 'POST',       // GET | POST
    'hasFile' => false,        // set true jika ada upload file
    'id'      => null,         // id form (opsional)
])

@php
    $isGet       = strtoupper($method) === 'GET';
    $httpMethod  = $isGet ? 'GET' : 'POST';
    $spoofMethod = in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']) ? strtoupper($method) : null;
@endphp

<form
    @if($id) id="{{ $id }}" @endif
    action="{{ $action }}"
    method="{{ $httpMethod }}"
    @if($hasFile) enctype="multipart/form-data" @endif
    {{ $attributes->merge(['class' => 'space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900 sm:p-6']) }}
>

    @if (!$isGet)
        @csrf
    @endif

    @if ($spoofMethod)
        @method($spoofMethod)
    @endif

    {{-- Form Fields (default slot) --}}
    {{ $slot }}

    {{-- Form Actions (footer slot) --}}
    @if (isset($actions))
        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end dark:border-slate-800">
            {{ $actions }}
        </div>
    @endif

</form>


{{-- =====================================================
    FORM FIELD COMPONENTS (sub-components)
====================================================== --}}

{{--
    Usage examples:

    Text / Email / Password / Number input:
    <x-form.field name="name" label="Nama" required />
    <x-form.field name="email" label="Email" type="email" required />
    <x-form.field name="price" label="Harga" type="number" :value="$product->price" />

    Textarea:
    <x-form.textarea name="description" label="Deskripsi" :value="$category->description" />

    Select:
    <x-form.select name="status" label="Status" :options="['active'=>'Aktif','inactive'=>'Nonaktif']" :selected="$user->status" />

    File:
    <x-form.file name="image" label="Foto Produk" accept="image/*" />

    Actions:
    <x-slot name="actions">
        <a href="{{ route('...') }}" class="...">Batal</a>
        <x-button type="submit">Simpan</x-button>
    </x-slot>
--}}
