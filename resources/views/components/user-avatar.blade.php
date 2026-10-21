@props([
    'user' => null,
    'size' => 'md',
    'class' => '',
])

@php
    $user     = $user ?? auth()->user();
    $initial  = $user ? strtoupper(substr($user->username, 0, 1)) : '?';
    $photoUrl = $user->profile_photo_url ?? null;

    $sizeClasses = match($size) {
        'sm'  => 'h-8 w-8 text-xs',
        'lg'  => 'h-12 w-12 text-lg',
        'xl'  => 'h-20 w-20 text-2xl',
        default => 'h-9 w-9 text-sm', // md
    };
@endphp

@if($photoUrl)
    <img
        src="{{ $photoUrl }}"
        alt="{{ $user?->username ?? 'Profil' }}"
        class="{{ $sizeClasses }} shrink-0 rounded-full object-cover {{ $class }}"
        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
        loading="lazy"
    >
    {{-- Fallback inisial jika gambar gagal dimuat --}}
    <div style="display:none;" class="{{ $sizeClasses }} shrink-0 items-center justify-center rounded-full bg-emerald-700 font-bold text-white {{ $class }}">
        {{ $initial }}
    </div>
@else
    <div class="{{ $sizeClasses }} shrink-0 flex items-center justify-center rounded-full bg-emerald-700 font-bold text-white {{ $class }}">
        {{ $initial }}
    </div>
@endif
