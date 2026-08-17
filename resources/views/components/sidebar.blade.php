@props([
    'type' => 'admin',
])

@if($type === 'admin')
    <x-sidebar-admin />
@else
    <x-sidebar-seller />
@endif
