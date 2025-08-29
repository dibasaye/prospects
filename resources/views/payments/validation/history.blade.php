<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                <i class="fas fa-history me-2"></i>Historique des Paiements Validés
            </h2>
            <a href="{{ route('payments.validation.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light py-3">
            <h6 class="mb-0 text-gray-800">
                <i class="fas fa-filter me-2"></i>Filtres
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('payments.validation.history') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date de début</label>
                    <input type="date" name="date_from" id="date_from" 
                           class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date de fin</label>
                    <input type="date" name="date_to" id="date_to" 
                           class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label">Type de paiement</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="adhesion" {{ request('type') == 'adhesion' ? 'selected' : '' }}>Adhésion</option>
                        <option value="reservation" {{ request('type') == 'reservation' ? 'selected' : '' }}>Réservation</option>
                        <option value="mensualite" {{ request('type') == 'mensualite' ? 'selected' : '' }}>Mensualité</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Filtrer
                        </button>
                        <a href="{{ route('payments.validation.history') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-4 border-success shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Total validés</div>
                            <div class="h3 mb-0">{{ $validatedPayments->total() }}</div>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x opacity-50"></i>
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
                            <div class="text-muted small">Montant total</div>
                            <div class="h3 mb-0">{{ number_format($validatedPayments->sum('amount'), 0, ',', ' ') }}</div>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-money-bill-wave fa-2x opacity-50"></i>
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
                            <div class="text-muted small">Moyenne/jour</div>
                            <div class="h3 mb-0">{{ number_format($validatedPayments->avg('amount'), 0, ',', ' ') }}</div>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-chart-line fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-4 border-warning shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Validés aujourd'hui</div>
                            <div class="h3 mb-0">{{ $validatedPayments->where('confirmed_at', '>=', today())->count() }}</div>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-calendar-day fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des paiements validés -->
    <div class="card shadow-sm">
        <div class="card-header bg-light py-3">
            <h6 class="mb-0 text-gray-800">
                <i class="fas fa-list me-2"></i>Paiements validés
            </h6>
        </div>
        <div class="card-body p-0">
            @if($validatedPayments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Référence</th>
                                <th>Client</th>
                                <th>Type</th>
                                <th>Montant</th>
                                <th>Validé par</th>
                                <th>Date validation</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($validatedPayments as $payment)
                            <tr>
                                <td>
                                    <span class="badge bg-success">{{ $payment->reference_number }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $payment->client->full_name }}</div>
                                    <small class="text-muted">{{ $payment->client->phone }}</small>
                                </td>
                                <td>
                                    @switch($payment->type)
                                        @case('adhesion')
                                            <span class="badge bg-primary">Adhésion</span>
                                            @break
                                        @case('reservation')
                                            <span class="badge bg-warning text-dark">Réservation</span>
                                            @break
                                        @case('mensualite')
                                            <span class="badge bg-info">Mensualité</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $payment->type }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <div class="fw-bold text-success">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $payment->confirmedBy->full_name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $payment->confirmedBy->role ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <div>{{ $payment->confirmed_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $payment->confirmed_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('payments.validation.show', $payment) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>Voir
                                        </a>
                                        <a href="{{ route('payments.invoice', $payment) }}" 
                                           class="btn btn-sm btn-outline-info" target="_blank">
                                            <i class="fas fa-file-pdf me-1"></i>Facture
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer bg-light">
                    {{ $validatedPayments->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun paiement validé</h5>
                    <p class="text-muted">Aucun paiement n'a été validé pour cette période.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout> 