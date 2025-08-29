<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h5 mb-0">
                <i class="fas fa-map me-2"></i>Gestion des Sites
            </h1>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('sites.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Site
            </a>
            @endif
        </div>
    </x-slot>

    <div class="container py-4">
        @if($sites->count() > 0)
        <div class="row g-4">
            @foreach($sites as $site)
            <div class="col-lg-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">{{ $site->name }}</h5>
                            <span class="badge bg-primary">{{ $site->lots->count() }} lots</span>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                <small class="text-muted">{{ $site->location }}</small>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-calendar text-muted me-2"></i>
                                <small class="text-muted">Lancé le {{ $site->created_at->format('d/m/Y') }}</small>
                            </div>
                        </div>

                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <div class="border-end">
                                    <div class="fs-5 fw-bold text-success">{{ $site->availableLots->count() }}</div>
                                    <small class="text-muted">Disponibles</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="border-end">
                                    <div class="fs-5 fw-bold text-warning">{{ $site->reservedLots->count() }}</div>
                                    <small class="text-muted">Réservés</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="fs-5 fw-bold text-danger">{{ $site->soldLots->count() }}</div>
                                <small class="text-muted">Vendus</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Frais adhésion</small>
                                    <div class="fw-medium">{{ number_format($site->membership_fee, 0, ',', ' ') }} FCFA</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Frais réservation</small>
                                    <div class="fw-medium">{{ number_format($site->reservation_fee, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                        </div>

                        @php
                            $totalLots = $site->lots->count();
                            $availableLots = $site->availableLots->count();
                            $progress = $totalLots > 0 ? (($totalLots - $availableLots) / $totalLots) * 100 : 0;
                        @endphp

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Progression des ventes</small>
                                <small class="text-muted">{{ number_format($progress, 1) }}%</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('sites.show', $site) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>Voir Détails
                            </a>
                            <a href="{{ route('sites.lots', $site) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-th me-2"></i>Gérer Lots
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $sites->links() }}
        </div>

        @else
        <div class="text-center py-5">
            <i class="fas fa-map fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Aucun site disponible</h5>
            <p class="text-muted">Commencez par créer votre premier site immobilier</p>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('sites.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Créer un site
            </a>
            @endif
        </div>
        @endif
    </div>

    @push('scripts')
<script>
    function initMaps() {
        @foreach($sites as $site)
            @if($site->latitude && $site->longitude)
                const map{{ $site->id }} = new google.maps.Map(document.getElementById("map-{{ $site->id }}"), {
                    center: { lat: {{ $site->latitude }}, lng: {{ $site->longitude }} },
                    zoom: 15,
                });
                new google.maps.Marker({
                    position: { lat: {{ $site->latitude }}, lng: {{ $site->longitude }} },
                    map: map{{ $site->id }},
                    title: "{{ $site->name }}",
                });
            @endif
        @endforeach
    }

    window.initMaps = initMaps;
</script>
@endpush

</x-app-layout>
