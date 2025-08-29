<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                <i class="fas fa-credit-card me-2"></i>Mes Paiements
            </h2>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-light py-3">
            <h6 class="mb-0 text-gray-800">
                <i class="fas fa-list me-2"></i>Liste de mes paiements
            </h6>
        </div>
        <div class="card-body">
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Client</th>
                                <th>Site</th>
                                <th>Type</th>
                                <th>Montant</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $payment->client->full_name }}</div>
                                        <small class="text-muted">{{ $payment->client->phone }}</small>
                                    </td>
                                    <td>{{ $payment->site->name ?? 'N/A' }}</td>
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
                                    <td class="fw-bold text-success">
                                        {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td>{{ $payment->payment_date->format('d/m/Y') }}</td>
                                    <td>
                                        @switch($payment->validation_status)
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-clock me-1"></i>En attente caissier
                                                </span>
                                                @break
                                            @case('caissier_validated')
                                                <span class="badge bg-info">
                                                    <i class="fas fa-check me-1"></i>Validé caissier
                                                </span>
                                                @if($payment->caissierValidatedBy)
                                                    <br><small class="text-muted">Par: {{ $payment->caissierValidatedBy->full_name }}</small>
                                                @endif
                                                @break
                                            @case('fully_validated')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-double me-1"></i>Complètement validé
                                                </span>
                                                @if($payment->managerValidatedBy)
                                                    <br><small class="text-muted">Par: {{ $payment->managerValidatedBy->full_name }}</small>
                                                @endif
                                                @break
                                            @case('rejected')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i>Rejeté
                                                </span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $payment->getValidationStatusText() }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('prospects.show', $payment->client) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Voir le prospect">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($payment->validation_status === 'fully_validated')
                                                <a href="{{ route('payments.invoice', $payment) }}" 
                                                   class="btn btn-sm btn-outline-success" 
                                                   title="Télécharger la facture">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $payments->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun paiement trouvé</h5>
                    <p class="text-muted">Vous n'avez pas encore de paiements enregistrés.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout> 