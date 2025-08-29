<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                <i class="fas fa-calendar-alt me-2"></i>Échéancier de Paiement
            </h2>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>Imprimer
                </button>
                <a href="{{ route('payment-schedules.export') }}" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i>Exporter
                </a>
            </div>
        </div>
    </x-slot>

    <div class="container-fluid py-4">
        <!-- Alertes importantes -->
        @if(auth()->user()->isAgent())
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Information :</strong> Vous ne voyez que les échéances de vos prospects assignés. 
            Les échéances en rouge sont en retard et nécessitent une action immédiate.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Filtres simplifiés -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-filter me-2"></i>Filtres de Recherche
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Statut des Échéances</label>
                        <select name="status" class="form-select">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Toutes les échéances</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>En attente de paiement</option>
                            <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Déjà payées</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Mois d'Échéance</label>
                        <input type="month" name="month" class="form-control" value="{{ $month }}">
                    </div>
                    @if(auth()->user()->isManager() || auth()->user()->isAdmin())
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Commercial</label>
                        <select name="commercial" class="form-select">
                            <option value="all" {{ $commercial === 'all' ? 'selected' : '' }}>Tous les commerciaux</option>
                            @foreach($commercials as $com)
                                <option value="{{ $com->id }}" {{ $commercial == $com->id ? 'selected' : '' }}>
                                    {{ $com->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i>Appliquer les filtres
                        </button>
                        <a href="{{ route('payment-schedules.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistiques claires -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-check fa-2x text-primary mb-2"></i>
                        <h4 class="mb-0 text-primary">{{ $stats['total_installments'] }}</h4>
                        <small class="text-muted">Total Échéances</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <h4 class="mb-0 text-success">{{ $stats['paid_installments'] }}</h4>
                        <small class="text-muted">Payées</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                        <h4 class="mb-0 text-warning">{{ $stats['pending_installments'] }}</h4>
                        <small class="text-muted">En attente</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                        <h4 class="mb-0 text-danger">{{ number_format($stats['pending_amount'], 0, ',', ' ') }} F</h4>
                        <small class="text-muted">Montant en attente</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique d'évolution -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>Évolution des Paiements (6 derniers mois)
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="paymentChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau organisé par priorités -->
        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-list me-2"></i>Liste des Échéances
                </h6>
                <div class="d-flex gap-2">
                    <span class="badge bg-success">Payé</span>
                    <span class="badge bg-warning text-dark">En attente</span>
                    <span class="badge bg-danger">En retard</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if($schedules->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 25%">Client & Contrat</th>
                                    <th style="width: 15%">Site/Lot</th>
                                    <th style="width: 15%">Échéance</th>
                                    <th style="width: 10%">Montant</th>
                                    <th style="width: 15%">Statut</th>
                                    <th style="width: 10%">Date Paiement</th>
                                    <th style="width: 10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedules as $schedule)
                                    <tr class="{{ $schedule->is_paid ? 'table-success' : ($schedule->due_date->isPast() ? 'table-danger' : 'table-warning') }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 40px; height: 40px;">
                                                    {{ substr($schedule->contract->client->full_name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $schedule->contract->client->full_name }}</div>
                                                    <small class="text-muted">{{ $schedule->contract->client->phone }}</small>
                                                    <br>
                                                    <small class="text-muted">
                                                        <strong>Contrat:</strong> {{ $schedule->contract->contract_number }}
                                                    </small>
                                                    <br>
                                                    <button type="button" class="btn btn-sm btn-outline-info mt-1" 
                                                            onclick="viewClientSchedules({{ $schedule->contract->client->id }}, '{{ $schedule->contract->client->full_name }}')"
                                                            title="Voir toutes les échéances de ce client">
                                                        <i class="fas fa-eye me-1"></i>Voir échéances
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $schedule->contract->site->name ?? 'Site N/A' }}</div>
                                            <small class="text-muted">
                                                @if($schedule->contract->lot)
                                                    {{ $schedule->contract->lot->lot_number ?? 'Lot sans référence' }}
                                                @else
                                                    <span class="text-warning">Lot non assigné</span>
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $schedule->due_date->format('d/m/Y') }}</div>
                                            <small class="text-muted">
                                                <strong>Échéance N°{{ $schedule->installment_number }}</strong>
                                                <br>
                                                @if($schedule->due_date->isPast() && !$schedule->is_paid)
                                                    <span class="text-danger">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        Retard de {{ $schedule->due_date->diffForHumans() }}
                                                    </span>
                                                @elseif($schedule->due_date->isToday())
                                                    <span class="text-warning">
                                                        <i class="fas fa-clock me-1"></i>
                                                        Aujourd'hui
                                                    </span>
                                                @else
                                                    <span class="text-info">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        {{ $schedule->due_date->diffForHumans() }}
                                                    </span>
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-primary fs-5">
                                                {{ number_format($schedule->amount, 0, ',', ' ') }} F
                                            </div>
                                        </td>
                                        <td>
                                            @if($schedule->is_paid)
                                                <span class="badge bg-success fs-6">
                                                    <i class="fas fa-check me-1"></i>Payé
                                                </span>
                                            @elseif($schedule->due_date->isPast())
                                                <span class="badge bg-danger fs-6">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>En retard
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark fs-6">
                                                    <i class="fas fa-clock me-1"></i>En attente
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($schedule->is_paid && $schedule->paid_date)
                                                <div class="text-success fw-bold">
                                                    {{ $schedule->paid_date->format('d/m/Y') }}
                                                </div>
                                                <small class="text-muted">{{ $schedule->paid_date->format('H:i') }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group-vertical" role="group">
                                                @if(!$schedule->is_paid)
                                                    <button type="button" class="btn btn-sm btn-success mb-1" 
                                                            onclick="markAsPaid({{ $schedule->id }})"
                                                            title="Marquer comme payé">
                                                        <i class="fas fa-check"></i> Payé
                                                    </button>
                                                @endif
                                                <a href="{{ route('schedules.receipt', $schedule) }}" 
                                                   class="btn btn-sm btn-outline-primary mb-1" 
                                                   title="Télécharger le reçu">
                                                    <i class="fas fa-download"></i> Reçu
                                                </a>
                                                <a href="{{ route('contracts.show', $schedule->contract) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   title="Voir le contrat">
                                                    <i class="fas fa-eye"></i> Contrat
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    Affichage de {{ $schedules->firstItem() ?? 0 }} à {{ $schedules->lastItem() ?? 0 }} 
                                    sur {{ $schedules->total() }} échéances
                                </small>
                            </div>
                            <div>
                                {{ $schedules->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune échéance trouvée</h5>
                        <p class="text-muted">Aucune échéance ne correspond aux critères de recherche.</p>
                        <a href="{{ route('payment-schedules.index') }}" class="btn btn-primary">
                            <i class="fas fa-refresh me-1"></i>Voir toutes les échéances
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal pour marquer comme payé -->
    <div class="modal fade" id="markAsPaidModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle me-2"></i>Marquer comme payé
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="markAsPaidForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Important :</strong> Cette action marquera définitivement cette échéance comme payée.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Montant reçu (FCFA)</label>
                            <input type="number" name="amount" class="form-control" required min="0" step="100" 
                                   placeholder="Ex: 500000">
                            <div class="form-text">Confirmez le montant exact reçu</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Méthode de paiement</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="">Sélectionner la méthode...</option>
                                <option value="especes">Espèces</option>
                                <option value="cheque">Chèque</option>
                                <option value="virement">Virement bancaire</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="carte">Carte bancaire</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Notes/Observations</label>
                            <textarea name="notes" class="form-control" rows="3" 
                                      placeholder="Observations, commentaires, références..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Annuler
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-1"></i>Confirmer le paiement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Graphique des paiements
        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        new Chart(paymentCtx, {
            type: 'line',
            data: {
                labels: @json($monthlyData->pluck('month')),
                datasets: [{
                    label: 'Montants dus',
                    data: @json($monthlyData->pluck('due_amount')),
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Montants payés',
                    data: @json($monthlyData->pluck('paid_amount')),
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        function markAsPaid(scheduleId) {
            const modal = new bootstrap.Modal(document.getElementById('markAsPaidModal'));
            const form = document.getElementById('markAsPaidForm');
            form.action = `/payment-schedules/${scheduleId}/pay`;
            modal.show();
        }

        function viewClientSchedules(clientId, clientName) {
            // Rediriger vers la page détaillée des échéances du client
            window.location.href = `/clients/${clientId}/payment-schedules`;
        }
    </script>
</x-app-layout>