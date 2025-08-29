<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0 text-gray-800">
                <i class="fas fa-users-gear me-2"></i>Gestion des Prospects
            </h2>
            <a href="{{ route('prospects.create') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-plus me-2"></i>Nouveau Prospect
            </a>
        </div>
    </x-slot>

    <!-- <div class="container-fluid py-4">
        z Statistiques rapides 
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-start border-4 border-warning shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small">Non assignés</div>
                                <div class="h3 mb-0">{{ $prospects->where('assigned_to_id', null)->count() }}</div>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-user-clock fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-4 border-info shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small">En cours</div>
                                <div class="h3 mb-0">{{ $prospects->where('status', 'en_relance')->count() }}</div>
                            </div>
                            <div class="text-info">
                                <i class="fas fa-sync-alt fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-4 border-success shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small">Convertis</div>
                                <div class="h3 mb-0">{{ $prospects->where('status', 'converti')->count() }}</div>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-check-circle fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-4 border-primary shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted small">Total</div>
                                <div class="h3 mb-0">{{ $prospects->count() }}</div>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-users fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Filtres -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 text-gray-800"><i class="fas fa-filter me-2"></i>Filtres</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('prospects.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               class="form-control" placeholder="Nom, téléphone, email...">
                    </div>

                    <div class="col-md-3">
                        <label for="status" class="form-label">Statut</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Tous les statuts</option>
                            @foreach(['nouveau','en_relance','interesse','converti','abandonne'] as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                        <div class="col-md-3">
                            <label for="assigned_to" class="form-label">Assigné à</label>
                            <select name="assigned_to" id="assigned_to" class="form-select">
                                <option value="">Tous les agents</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ request('assigned_to') == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="col-12 text-end">
                        <button type="reset" class="btn btn-light btn-sm me-2">Réinitialiser</button>
                        <button type="submit" class="btn btn-secondary btn-sm">
                            <i class="fas fa-search me-2"></i>Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Prospects non assignés - visible uniquement pour admin et manager -->
        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
            <div class="card shadow-sm mb-4 border-warning border-top border-2">
                <div class="card-header bg-light py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Prospects non assignés
                        </h6>
                        <div>
                            <button type="button" id="assignSelected" class="btn btn-success btn-sm me-2" style="display: none;">
                                <i class="fas fa-user-check me-2"></i>Assigner la sélection
                            </button>
                            <span class="badge bg-warning text-dark">{{ $prospects->where('assigned_to_id', null)->count() }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($prospects->where('assigned_to_id', null)->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-0">
                                <thead class="table-light text-center align-middle">
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </th>
                                        <th>Prospect</th>
                                        <th>Contact</th>
                                        <th>Statut</th>
                                        <th>Site d'intérêt</th>
                                        <th>Date de contact</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prospects->where('assigned_to_id', null) as $prospect)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input prospect-checkbox" 
                           value="{{ $prospect->id }}">
                                            </td>
                                            <td>
                                                <strong>{{ $prospect->full_name }}</strong><br>
                                                @if($prospect->budget_min || $prospect->budget_max)
                                                    <small class="text-muted">
                                                        Budget : {{ number_format($prospect->budget_min ?? 0, 0, ',', ' ') }} - {{ number_format($prospect->budget_max ?? 0, 0, ',', ' ') }} F
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $prospect->phone }}<br>
                                                @if($prospect->email)
                                                    <small class="text-muted">{{ $prospect->email }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info text-dark">
                                                    {{ ucfirst(str_replace('_', ' ', $prospect->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $prospect->interestedSite->name ?? '-' }}</td>
                                            <td class="text-center">{{ $prospect->contact_date?->format('d/m/Y') ?? $prospect->created_at->format('d/m/Y') }}</td>
                                            <td class="text-end">
                                                <div class="d-flex flex-wrap justify-content-end gap-1">
                                                    <a href="{{ route('prospects.show', $prospect) }}" class="btn btn-sm btn-outline-primary">Voir</a>
                                                    <a href="{{ route('prospects.edit', $prospect) }}" class="btn btn-sm btn-outline-warning">Modifier</a>

                                                    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                                                        <a href="{{ route('prospects.assign.form', $prospect) }}" class="btn btn-sm btn-outline-success">
                                                            Assigner
                                                        </a>
                                                    @endif

                                                    @php
                                                        $hasReservation = $prospect->reservations()->where('expires_at', '>', now())->exists();
                                                        $adhesionPaid = $prospect->payments()->byType('adhesion')->exists();
                                                        $reservationPaid = $prospect->payments()->byType('reservation')->exists();
                                                    @endphp

                                                    @if(!$hasReservation)
                                                        <a href="{{ route('reservations.create', $prospect) }}" class="btn btn-sm btn-outline-success"> Réserver</a>
                                                    @elseif($hasReservation && !$adhesionPaid)
                                                        <a href="{{ route('payments.create', $prospect) }}" class="btn btn-sm btn-outline-dark"> Adhésion</a>
                                                    @elseif($adhesionPaid && !$reservationPaid)
                                                        <a href="{{ route('payments.reservation.create', $prospect) }}" class="btn btn-sm btn-outline-primary"> Réservation</a>
                                               @elseif($reservationPaid && !$prospect->contract)
    <a href="{{ route('contracts.generate', $prospect) }}" class="btn btn-sm btn-primary">📝 Contrat</a>
@elseif($prospect->contract)
    <a href="{{ route('contracts.show', $prospect->contract) }}" class="btn btn-sm btn-outline-primary">📄 Voir Contrat</a>
@endif

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <p class="mb-0">Aucun prospect en attente d'assignation</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Prospects assignés -->
        <div class="card shadow-sm border-success border-top border-2">
            <div class="card-header bg-light py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-success">
                        <i class="fas fa-check-circle me-2"></i>
                        @if(auth()->user()->isAgent())
                            Mes Prospects
                        @else
                            Prospects assignés
                        @endif
                    </h6>
                    <span class="badge bg-success">
                        @if(auth()->user()->isAgent())
                            {{ $prospects->where('assigned_to_id', auth()->id())->count() }}
                        @else
                            {{ $prospects->whereNotNull('assigned_to_id')->count() }}
                        @endif
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                @php
                    $displayedProspects = auth()->user()->isAgent() 
                        ? $prospects->where('assigned_to_id', auth()->id())
                        : $prospects->whereNotNull('assigned_to_id');
                @endphp

                @if($displayedProspects->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>Prospect</th>
                                    <th>Contact</th>
                                    <th>Statut</th>
                                    <th>Assigné à</th>
                                    <th>Site d'intérêt</th>
                                    <th>Date de contact</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($displayedProspects as $prospect)
                                    <tr>
                                        <td>
                                            <strong>{{ $prospect->full_name }}</strong><br>
                                            @if($prospect->budget_min || $prospect->budget_max)
                                                <small class="text-muted">
                                                    Budget : {{ number_format($prospect->budget_min ?? 0, 0, ',', ' ') }} - {{ number_format($prospect->budget_max ?? 0, 0, ',', ' ') }} F
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $prospect->phone }}<br>
                                            @if($prospect->email)
                                                <small class="text-muted">{{ $prospect->email }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info text-dark">
                                                {{ ucfirst(str_replace('_', ' ', $prospect->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $prospect->assignedTo->full_name ?? 'Non assigné' }}</td>
                                        <td>{{ $prospect->interestedSite->name ?? '-' }}</td>
                                        <td class="text-center">{{ $prospect->contact_date?->format('d/m/Y') ?? $prospect->created_at->format('d/m/Y') }}</td>
                                        <td class="text-end">
                                            <div class="d-flex flex-wrap justify-content-end gap-1">
                                                <a href="{{ route('prospects.show', $prospect) }}" class="btn btn-sm btn-outline-primary">Voir</a>
                                                <a href="{{ route('prospects.edit', $prospect) }}" class="btn btn-sm btn-outline-warning">Modifier</a>

                                                @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                                                    <a href="{{ route('prospects.assign.form', $prospect) }}" class="btn btn-sm btn-outline-success">
                                                        Assigner
                                                    </a>
                                                @endif

                                                @php
                                                    $hasReservation = $prospect->reservations()->where('expires_at', '>', now())->exists();
                                                    $adhesionPaid = $prospect->payments()->byType('adhesion')->exists();
                                                    $reservationPaid = $prospect->payments()->byType('reservation')->exists();
                                                @endphp

                                                @if(!$hasReservation)
                                                    <a href="{{ route('reservations.create', $prospect) }}" class="btn btn-sm btn-outline-success"> Réserver</a>
                                                @elseif($hasReservation && !$adhesionPaid)
                                                    <a href="{{ route('payments.create', $prospect) }}" class="btn btn-sm btn-outline-dark"> Adhésion</a>
                                                @elseif($adhesionPaid && !$reservationPaid)
                                                    <a href="{{ route('payments.reservation.create', $prospect) }}" class="btn btn-sm btn-outline-primary"> Réservation</a>
                                               @elseif($reservationPaid && !$prospect->contract)
    <a href="{{ route('contracts.generate', $prospect) }}" class="btn btn-sm btn-primary">📝 Contrat</a>
@elseif($prospect->contract)
    <a href="{{ route('contracts.show', $prospect->contract) }}" class="btn btn-sm btn-outline-primary">📄 Voir Contrat</a>
@endif

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <p class="mb-0">
                            @if(auth()->user()->isAgent())
                                Aucun prospect ne vous est assigné
                            @else
                                Aucun prospect assigné
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $prospects->withQueryString()->links() }}
        </div>
    </div>

    <!-- Modal d'assignation multiple -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('prospects.assign.bulk') }}" method="POST" id="bulkAssignForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assigner les prospects sélectionnés</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="prospect_ids" id="selectedProspectIds">
                    <div class="mb-3">
                        <label for="bulk_commercial_id" class="form-label">Commercial</label>
                        <select name="commercial_id" id="bulk_commercial_id" class="form-select" required>
                            <option value="">Sélectionner un commercial</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Assigner</button>
                </div>
            </form>
        </div>
    </div>
</div>

    @push('styles')
    <style>
        .table th { 
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 0.875rem;
        }
        .badge {
            font-weight: 500;
        }
        .btn-sm {
            padding: 0.4rem 0.8rem;
        }
    </style>
    @endpush

    @push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const prospectCheckboxes = document.querySelectorAll('.prospect-checkbox');
    const assignSelectedBtn = document.getElementById('assignSelected');
    const bulkAssignModal = new bootstrap.Modal(document.getElementById('bulkAssignModal'));
    const selectedProspectIds = document.getElementById('selectedProspectIds');

    // Gérer la sélection/désélection de tous les prospects
    selectAll.addEventListener('change', function() {
        prospectCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateAssignButton();
    });

    // Gérer les sélections individuelles
    prospectCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateAssignButton();
        });
    });

    // Mettre à jour le bouton d'assignation
    function updateAssignButton() {
        const checkedCount = document.querySelectorAll('.prospect-checkbox:checked').length;
        assignSelectedBtn.style.display = checkedCount > 0 ? 'inline-block' : 'none';
    }

    // Ouvrir le modal d'assignation
    assignSelectedBtn.addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.prospect-checkbox:checked'))
            .map(cb => cb.value);
        selectedProspectIds.value = selectedIds.join(',');
        bulkAssignModal.show();
    });
});
</script>
@endpush
</x-app-layout>
