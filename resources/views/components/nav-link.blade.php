@props(['href', 'active'])

@php
    $classes = 'nav-link';
    if ($active) {
        $classes .= ' active fw-bold';
    }
@endphp

<li class="nav-item">
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
</li>
