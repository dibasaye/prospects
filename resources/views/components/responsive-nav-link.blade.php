@props(['href', 'active'])

@php
    $classes = $active ? 'list-group-item list-group-item-action active' : 'list-group-item list-group-item-action';
@endphp

<a href="{{ $href }}" class="{{ $classes }}">
    {{ $slot }}
</a>
