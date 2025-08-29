<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fas fa-map me-2"></i>Détails du site : {{ $site->name }}
        </h2>
    </x-slot>

    <div class="container-fluid py-4">
        <div class="row g-4 flex-column-reverse flex-lg-row">

            {{-- Informations principales --}}
            <div class="{{ $site->latitude && $site->longitude ? 'col-12 col-lg-7' : 'col-12' }}">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Informations principales</h5>
                        <div class="row">
                            <div class="col-12 col-md-6"><p><strong>Localisation :</strong> {{ $site->location }}</p></div>
                            <div class="col-12 col-md-6"><p><strong>Superficie :</strong> {{ $site->total_area ?? '-' }} m²</p></div>
                            <div class="col-12 col-md-6"><p><strong>Prix m² :</strong> {{ number_format($site->base_price_per_sqm, 0, ',', ' ') }} FCFA</p></div>
                            <div class="col-12 col-md-6"><p><strong>Frais de réservation :</strong> {{ number_format($site->reservation_fee, 0, ',', ' ') }} FCFA</p></div>
                            <div class="col-12 col-md-6"><p><strong>Frais d’adhésion :</strong> {{ number_format($site->membership_fee, 0, ',', ' ') }} FCFA</p></div>
                            <!-- <div class="col-12 col-md-6"><p><strong>Plan de paiement :</strong> {{ strtoupper(str_replace('_', ' ', $site->payment_plan)) }}</p></div> -->
                             <div class="col-12">
    <h5 class="mt-3">Plans de paiement</h5>
    <ul class="list-group">

        @if($site->enable_12 && $site->price_12_months)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                12 mois
                <span class="badge bg-primary rounded-pill">
                    {{ number_format($site->price_12_months, 0, ',', ' ') }} FCFA
                </span>
            </li>
        @endif

        @if($site->enable_24 && $site->price_24_months)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                24 mois
                <span class="badge bg-primary rounded-pill">
                    {{ number_format($site->price_24_months, 0, ',', ' ') }} FCFA
                </span>
            </li>
        @endif
         @if($site->enable_36 && $site->price_36_months)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                36 mois
                <span class="badge bg-primary rounded-pill">
                    {{ number_format($site->price_36_months, 0, ',', ' ') }} FCFA
                </span>
            </li>
        @endif



        @if($site->enable_cash && $site->price_cash)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                Paiement cash
                <span class="badge bg-success rounded-pill">
                    {{ number_format($site->price_cash, 0, ',', ' ') }} FCFA
                </span>
            </li>
        @endif

        @if(
            !($site->enable_12 && $site->price_12_months) && 
            !($site->enable_24 && $site->price_24_months) &&
            !($site->enable_36 && $site->price_36_months) && 
            !($site->enable_cash && $site->price_cash)
        )
            <li class="list-group-item text-muted">Aucun plan de paiement défini</li>
        @endif
    </ul>
</div>

                        </div>
                        <p><strong>Description :</strong><br>{{ $site->description ?? '-' }}</p>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Statistiques</h5>
                        <div class="row">
                            <div class="col-12 col-md-6"><p>Lots totaux : {{ $stats['total_lots'] }}</p></div>
                            <div class="col-12 col-md-6"><p>Disponibles : {{ $stats['available_lots'] }}</p></div>
                            <div class="col-12 col-md-6"><p>Réservés : {{ $stats['reserved_lots'] }}</p></div>
                            <div class="col-12 col-md-6"><p>Vendus : {{ $stats['sold_lots'] }}</p></div>
                            <div class="col-12 col-md-6"><p>Prospects intéressés : {{ $stats['total_prospects'] }}</p></div>
                            <div class="col-12 col-md-6"><p>Revenus générés : {{ number_format($stats['total_revenue'], 0, ',', ' ') }} FCFA</p></div>
                        </div>
                    </div>
                </div>

                {{-- Affichage du plan du lotissement (image ou PDF) --}}
                @if($site->image_url)
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Plan du lotissement</h5>
                        @if($isPdf)
                            <iframe src="{{ asset('storage/' . $site->image_url) }}" width="100%" height="500px" style="border: none;"></iframe>
                        @else
                            <img 
                                src="{{ asset('storage/' . $site->image_url) }}" 
                                alt="Plan du lotissement" 
                                style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.2);"
                            >
                        @endif
                    </div>
                </div>
                @endif

                <a href="{{ route('sites.edit', $site) }}" class="btn btn-warning mb-4">Modifier le site</a>
            </div>

            {{-- Carte Leaflet --}}
            @if($site->latitude && $site->longitude)
            <div class="col-12 col-lg-5">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column" style="min-height: 350px;">
                        <h5 class="card-title">Carte de localisation</h5>
                        <div id="map" class="rounded shadow-sm flex-grow-1" style="height: 300px; width: 100%;"></div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- Leaflet.js CSS & JS --}}
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        @if($site->latitude && $site->longitude)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const map = L.map('map', {
                    center: [{{ $site->latitude }}, {{ $site->longitude }}],
                    zoom: 15,
                    scrollWheelZoom: false,
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                L.marker([{{ $site->latitude }}, {{ $site->longitude }}])
                    .addTo(map)
                    .bindPopup("<b>{{ addslashes($site->name) }}</b>")
                    .openPopup();
            });
        </script>
        @endif
    @endpush
</x-app-layout>
