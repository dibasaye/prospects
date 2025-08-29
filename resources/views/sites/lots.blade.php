<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h4 fw-bold">
                    <i class="fas fa-th me-2"></i>Lots - {{ $site->name }}
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('sites.index') }}">Sites</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('sites.show', $site) }}">{{ $site->name }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Lots</li>
                    </ol>
                </nav>
            </div>
            @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                <a href="{{ route('sites.lots.create', $site) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nouveau Lot
                </a>
            @endif
        </div>
    </x-slot>

    <!-- Section Réservation Rapide par Numéro -->
    @if(auth()->check() && (auth()->user()->isAgent() || auth()->user()->isAdmin()))
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Réservation Rapide par Numéro
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('sites.lots.reserve-by-number', $site) }}" method="POST" id="quickReserveForm">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label for="lot_number" class="form-label fw-bold">Numéro de Lot</label>
                            <input type="text" class="form-control" id="lot_number" name="lot_number" required 
                                   placeholder="Ex: A1, B2..." maxlength="10">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="client_id" class="form-label fw-bold">Client</label>
                            <select class="form-select" id="client_id" name="client_id" required>
                                <option value="">Choisir un client...</option>
                                @foreach($prospects as $prospect)
                                    <option value="{{ $prospect->id }}">
                                        {{ $prospect->first_name }} {{ $prospect->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="area" class="form-label fw-bold">Surface (m²)</label>
                            <input type="number" class="form-control" id="area" name="area" required 
                                   min="0" step="0.01" placeholder="Ex: 150">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="base_price" class="form-label fw-bold">Prix de Base</label>
                            <input type="number" class="form-control" id="base_price" name="base_price" required 
                                   min="0" placeholder="Ex: 5000000">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="position" class="form-label fw-bold">Position</label>
                            <select class="form-select" id="position" name="position" required>
                                <option value="">Choisir...</option>
                                <option value="interieur">Intérieur</option>
                                <option value="facade">Façade</option>
                                <option value="angle">Angle</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check me-1"></i>Réserver
                            </button>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="description" class="form-label">Description (optionnel)</label>
                            <textarea class="form-control" id="description" name="description" rows="2" 
                                      placeholder="Description du lot..."></textarea>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="alert alert-info mb-0 w-100">
                                <small>
                                    <i class="fas fa-info-circle me-1"></i>
                                    <strong>Info :</strong> Si le lot existe déjà et est disponible, il sera réservé. 
                                    Sinon, un nouveau lot sera créé et réservé automatiquement.
                                </small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="row mb-4">
        <!-- FILTRES -->
        <div class="col-md-3 mb-3">
            <label for="statusFilter" class="form-label fw-bold">Filtrer par statut</label>
            <select id="statusFilter" class="form-select" aria-label="Filtrer par statut">
                <option value="">Tous</option>
                <option value="disponible">Disponible</option>
                <option value="reserve">Réservé</option>
                <option value="vendu">Vendu</option>
            </select>
        </div>

        <div class="col-md-3 mb-3">
            <label for="positionFilter" class="form-label fw-bold">Filtrer par position</label>
            <select id="positionFilter" class="form-select" aria-label="Filtrer par position">
                <option value="">Toutes</option>
                <option value="angle">Angle</option>
                <option value="facade">Façade</option>
                <option value="interieur">Intérieur</option>
            </select>
        </div>

        <div class="col-md-4 mb-3">
            <label for="searchLot" class="form-label fw-bold">Rechercher un lot</label>
            <input type="search" id="searchLot" class="form-control" placeholder="Par numéro de lot...">
        </div>

        <div class="col-md-2 d-flex align-items-end mb-3">
            <button id="clearFilters" class="btn btn-secondary w-100">Réinitialiser</button>
        </div>
    </div>

    @if($lots->count())
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-3" id="lotsGrid">
                    @foreach($lots as $lot)
                        <div class="col-xl-2 col-lg-3 col-md-4 col-6 lot-item" 
                             data-status="{{ $lot->status }}" 
                             data-position="{{ $lot->position }}" 
                             data-number="{{ strtolower($lot->lot_number) }}">
                            <div class="card h-100 lot-card" style="border: 2px solid {{ $lot->status_color }} !important;">
                                <div class="card-body p-3 text-center">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">{{ $lot->lot_number }}</h6>
                                        @if($lot->position === 'angle')
                                            <i class="fas fa-crown text-warning" title="Lot en angle"></i>
                                        @elseif($lot->position === 'facade')
                                            <i class="fas fa-star text-info" title="Lot en façade"></i>
                                        @endif
                                    </div>

                                    <div class="mb-2">
                                        <small class="text-muted">{{ $lot->area }} m²</small>
                                    </div>

                                    <div class="mb-2">
                                        <div class="fw-bold text-primary">{{ number_format($lot->final_price, 0, ',', ' ') }} FCFA</div>
                                        @if($lot->position !== 'interieur')
                                            <small class="text-muted">(Base : {{ number_format($lot->base_price, 0, ',', ' ') }} FCFA)</small>
                                        @endif
                                    </div>

                                    <div class="mb-2">
                                        <span class="badge w-100" style="background-color: {{ $lot->status_color }}; color: white;">
                                            {{ $lot->status_label }}
                                        </span>
                                    </div>

                                    @if($lot->reserved_until && $lot->status === 'reserve_temporaire')
                                        <div class="mb-2">
                                            <small class="text-warning">
                                                <i class="fas fa-clock me-1"></i>
                                                Expire : {{ $lot->reserved_until->format('d/m H:i') }}
                                            </small>
                                        </div>
                                    @endif

                                  {{-- Affichage du client (vente ou réservation) --}}
@if($lot->contract && $lot->contract->client)
    <div class="mb-2">
        <small class="text-muted">
            <i class="fas fa-user me-1"></i>Vendu à {{ $lot->contract->client->full_name }}
        </small>
    </div>
@elseif($lot->reservation && $lot->reservation->prospect)
    <div class="mb-2">
        <small class="text-muted">
            <i class="fas fa-user me-1"></i>
            Réservé par {{ $lot->reservation->prospect->first_name }} {{ $lot->reservation->prospect->last_name }}
        </small>
        @if($lot->reservation->expires_at)
            <br>
            <small class="text-warning">
                <i class="fas fa-clock me-1"></i>
                Expire le {{ $lot->reservation->expires_at->format('d/m/Y H:i') }}
            </small>
        @endif
    </div>
@endif

                                    <div class="d-grid gap-1">
                                        {{-- Bouton Réserver (uniquement pour agents et admin) --}}
                                        @if(auth()->check() && (auth()->user()->isAgent() || auth()->user()->isAdmin()))
                                            @if($lot->status === 'disponible')
                                                <button class="btn btn-sm btn-success reserve-lot-btn" 
                                                        data-lot-id="{{ $lot->id }}" 
                                                        data-site-id="{{ $site->id }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#reserveLotModal">
                                                    <i class="fas fa-lock me-1"></i>Réserver
                                                </button>
                                            @endif
                                        @endif

                                        {{-- Bouton Libérer (admin ou manager) --}}
                                        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isManager()))
                                            @if($lot->status === 'reserve')
                                                <button class="btn btn-sm btn-warning release-lot-btn" 
                                                        data-lot-id="{{ $lot->id }}" 
                                                        data-site-id="{{ $site->id }}">
                                                    <i class="fas fa-unlock me-1"></i>Libérer
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-home fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Aucun lot créé</h5>
            <p class="text-muted">Commencez par créer les lots de ce site</p>
            @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                <a href="{{ route('sites.lots.create', $site) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Créer un lot
                </a>
            @endif
        </div>
    @endif

    {{-- Modal Réserver --}}
    @if(auth()->check() && (auth()->user()->isAgent() || auth()->user()->isAdmin()))
        <div class="modal fade" id="reserveLotModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form id="reserveLotForm" method="POST" action="">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Réserver un Lot</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <label for="prospectSelect" class="form-label">Sélectionnez un prospect :</label>
                            <select id="prospectSelect" name="client_id" class="form-select" required>
                                <option value="">Choisir un prospect...</option>
                                @foreach($prospects as $prospect)
                                    <option value="{{ $prospect->id }}">
                                        {{ $prospect->first_name }} {{ $prospect->last_name }} - {{ $prospect->phone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Réserver</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Éléments filtres
                const statusFilter = document.getElementById('statusFilter');
                const positionFilter = document.getElementById('positionFilter');
                const searchLot = document.getElementById('searchLot');
                const clearFilters = document.getElementById('clearFilters');
                const lotItems = document.querySelectorAll('.lot-item');

                function filterLots() {
                    const statusValue = statusFilter.value;
                    const positionValue = positionFilter.value;
                    const searchValue = searchLot.value.toLowerCase();

                    lotItems.forEach(item => {
                        const status = item.dataset.status;
                        const position = item.dataset.position;
                        const number = item.dataset.number.toLowerCase();

                        const statusMatch = !statusValue || status === statusValue;
                        const positionMatch = !positionValue || position === positionValue;
                        const searchMatch = !searchValue || number.includes(searchValue);

                        item.style.display = (statusMatch && positionMatch && searchMatch) ? 'block' : 'none';
                    });
                }

                statusFilter.addEventListener('change', filterLots);
                positionFilter.addEventListener('change', filterLots);
                searchLot.addEventListener('input', filterLots);
                clearFilters.addEventListener('click', () => {
                    statusFilter.value = '';
                    positionFilter.value = '';
                    searchLot.value = '';
                    filterLots();
                });

                // Gérer bouton Réserver
                @if(auth()->check() && (auth()->user()->isAgent() || auth()->user()->isAdmin()))
                    const reserveLotBtns = document.querySelectorAll('.reserve-lot-btn');
                    const prospectSelect = document.getElementById('prospectSelect');
                    const reserveLotForm = document.getElementById('reserveLotForm');

                    reserveLotBtns.forEach(btn => {
                        btn.addEventListener('click', () => {
                            const lotId = btn.dataset.lotId;
                            const siteId = btn.dataset.siteId;
                            reserveLotForm.action = `/sites/${siteId}/lots/${lotId}/reserve`;
                        });
                    });
                @endif

                // Gérer bouton Libérer
                @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isManager()))
                    const releaseLotBtns = document.querySelectorAll('.release-lot-btn');
                    releaseLotBtns.forEach(btn => {
                        btn.addEventListener('click', () => {
                            if (confirm('Êtes-vous sûr de vouloir libérer ce lot ?')) {
                                const form = document.createElement('form');
                                form.method = 'POST';
                                const siteId = btn.dataset.siteId;
                                const lotId = btn.dataset.lotId;
                                form.action = `/sites/${siteId}/lots/${lotId}/release`;
                                form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
                                document.body.appendChild(form);
                                form.submit();
                            }
                        });
                    });
                @endif

                // Gérer le formulaire de réservation rapide
                @if(auth()->check() && (auth()->user()->isAgent() || auth()->user()->isAdmin()))
                    const quickReserveForm = document.getElementById('quickReserveForm');
                    const lotNumberInput = document.getElementById('lot_number');
                    const basePriceInput = document.getElementById('base_price');
                    const positionSelect = document.getElementById('position');

                    // Auto-calcul du prix final basé sur la position
                    function updateFinalPrice() {
                        const basePrice = parseFloat(basePriceInput.value) || 0;
                        const position = positionSelect.value;
                        let finalPrice = basePrice;

                        if (position === 'facade' || position === 'angle') {
                            finalPrice = basePrice * 1.10; // +10% pour façade et angle
                        }

                        // Afficher le prix calculé (optionnel)
                        const priceDisplay = document.getElementById('priceDisplay');
                        if (priceDisplay) {
                            priceDisplay.textContent = `Prix final estimé: ${finalPrice.toLocaleString()} FCFA`;
                        }
                    }

                    basePriceInput.addEventListener('input', updateFinalPrice);
                    positionSelect.addEventListener('change', updateFinalPrice);

                    // Validation du formulaire
                    quickReserveForm.addEventListener('submit', function(e) {
                        const lotNumber = lotNumberInput.value.trim();
                        const clientId = document.getElementById('client_id').value;
                        const area = document.getElementById('area').value;
                        const basePrice = basePriceInput.value;
                        const position = positionSelect.value;

                        if (!lotNumber || !clientId || !area || !basePrice || !position) {
                            e.preventDefault();
                            alert('Veuillez remplir tous les champs obligatoires.');
                            return;
                        }

                        // Confirmation avant soumission
                        if (!confirm(`Êtes-vous sûr de vouloir réserver le lot ${lotNumber} ?`)) {
                            e.preventDefault();
                        }
                    });

                    // Focus automatique sur le numéro de lot
                    lotNumberInput.focus();
                @endif
            });
        </script>
    @endpush

</x-app-layout>
