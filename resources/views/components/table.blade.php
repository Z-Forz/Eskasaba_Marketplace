{{-- resources/views/components/table.blade.php --}}

@props([
    'headers' => [],       // array string nama kolom
    'striped' => false,    // baris bergantian warna
    'hoverable' => true,   // hover effect pada baris
    'bordered' => false,   // border pada sel
    'compact' => false,    // padding lebih kecil
    'emptyText' => 'Tidak ada data yang ditemukan.',
    'emptyIcon' => '📭',
])

@php
    $cellPad = $compact ? 'px-5 py-3' : 'px-6 py-4';
    $hoverRow = $hoverable ? 'hover:bg-slate-50 dark:hover:bg-slate-800/50' : '';
    $borderCell = $bordered ? 'border border-slate-200 dark:border-slate-700' : '';
@endphp

<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">

    {{-- Desktop Table --}}
    <div class="hidden overflow-x-auto md:block">
        <table class="w-full text-left text-sm">

            @if (count($headers))
                <thead class="border-b border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800">
                    <tr>
                        @foreach ($headers as $header)
                            <th class="{{ $cellPad }} text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
            @endif

            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                {{ $slot }}

                @if ($slot->isEmpty())
                    <tr>
                        <td colspan="{{ count($headers) ?: 99 }}" class="py-14 text-center">
                            <div class="text-4xl">{{ $emptyIcon }}</div>
                            <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">{{ $emptyText }}</p>
                        </td>
                    </tr>
                @endif
            </tbody>

        </table>
    </div>

    {{-- Mobile Card List (slot: $mobile, fallback ke pesan kosong) --}}
    <div class="divide-y divide-slate-100 md:hidden dark:divide-slate-800">
        @if (isset($mobile))
            {{ $mobile }}
        @else
            <div class="p-6 text-center text-sm text-slate-400">Tampilan mobile belum tersedia.</div>
        @endif
    </div>

</div>
