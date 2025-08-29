@props(['href'])

<li>
    <a class="dropdown-item" href="{{ $href }}">
        {{ $slot }}
    </a>
</li>
