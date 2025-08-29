<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                <i class="fas fa-chart-bar me-2"></i>Statistiques des Paiements
            </h2>
            <a href="{{ route('payments.validation.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </x-slot>

    <!-- Statistiques principales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-4 border-warning shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">En attente</div>
                            <div class="h3 mb-0">{{ $stats['pending_count'] }}</div>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-clock fa-2x opacity-50"></i>
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
                            <div class="text-muted small">Validés aujourd'hui</div>
                            <div class="h3 mb-0">{{ $stats['validated_today'] }}</div>
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
                            <div class="h3 mb-0">{{ number_format($stats['total_validated_amount'], 0, ',', ' ') }}</div>
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
                            <div class="text-muted small">Votre rôle</div>
                            <div class="h6 mb-0">{{ auth()->user()->role }}</div>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-user-shield fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et analyses -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 text-gray-800">
                        <i class="fas fa-chart-pie me-2"></i>Répartition par type de paiement
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="paymentTypeChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 text-gray-800">
                        <i class="fas fa-chart-line me-2"></i>Évolution des validations (7 derniers jours)
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="validationTrendChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Validations récentes -->
    <div class="card shadow-sm">
        <div class="card-header bg-light py-3">
            <h6 class="mb-0 text-gray-800">
                <i class="fas fa-history me-2"></i>Validations récentes
            </h6>
        </div>
        <div class="card-body p-0">
            @if($stats['recent_validations']->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Référence</th>
                                <th>Client</th>
                                <th>Type</th>
                                <th>Montant</th>
                                <th>Validé par</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_validations'] as $payment)
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
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune validation récente</h5>
                    <p class="text-muted">Aucun paiement n'a été validé récemment.</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Données pour les graphiques (à adapter selon vos besoins)
        const paymentTypeData = {
            labels: ['Adhésion', 'Réservation', 'Mensualité'],
            datasets: [{
                data: [30, 45, 25],
                backgroundColor: ['#007bff', '#ffc107', '#17a2b8'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        };

        const validationTrendData = {
            labels: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
            datasets: [{
                label: 'Validations',
                data: [12, 19, 15, 25, 22, 18, 14],
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4
            }]
        };

        // Graphique en secteurs
        const ctx1 = document.getElementById('paymentTypeChart').getContext('2d');
        new Chart(ctx1, {
            type: 'doughnut',
            data: paymentTypeData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Graphique linéaire
        const ctx2 = document.getElementById('validationTrendChart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: validationTrendData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout> 