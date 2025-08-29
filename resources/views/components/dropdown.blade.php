@props(['label'])

<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        {{ $label }}
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
        {{ $slot }}
    </ul>
</li>
