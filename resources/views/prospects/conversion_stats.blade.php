<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                <i class="fas fa-chart-pie me-2"></i>Statistiques de Conversion
            </h2>
        </div>
    </x-slot>

    <div class="container-fluid py-4">
        <!-- Statistiques globales -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $totalProspects }}</h3>
                        <small>Total Prospects</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $convertedProspects }}</h3>
                        <small>Prospects Convertis</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ $interestedProspects }}</h3>
                        <small>Prospects Intéressés</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h3 class="mb-0">{{ number_format($conversionRate, 1) }}%</h3>
                        <small>Taux de Conversion</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique de conversion -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Évolution des Conversions</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="conversionChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Répartition par Statut</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des conversions par commercial -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Performance par Commercial</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Commercial</th>
                                <th>Total Prospects</th>
                                <th>Convertis</th>
                                <th>Intéressés</th>
                                <th>Taux Conversion</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commercialStats as $stat)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 40px; height: 40px;">
                                                {{ substr($stat['name'], 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $stat['name'] }}</div>
                                                <small class="text-muted">{{ $stat['email'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $stat['total'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $stat['converted'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $stat['interested'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" 
                                                 style="width: {{ $stat['conversion_rate'] }}%">
                                                {{ number_format($stat['conversion_rate'], 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('prospects.index', ['assigned_to' => $stat['id']]) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Voir
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Graphique d'évolution des conversions
        const conversionCtx = document.getElementById('conversionChart').getContext('2d');
        new Chart(conversionCtx, {
            type: 'line',
            data: {
                labels: @json($monthlyData->pluck('month')),
                datasets: [{
                    label: 'Nouveaux Prospects',
                    data: @json($monthlyData->pluck('new_prospects')),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Prospects Convertis',
                    data: @json($monthlyData->pluck('converted_prospects')),
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

        // Graphique de répartition par statut
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Nouveau', 'En relance', 'Intéressé', 'Converti', 'Abandonné'],
                datasets: [{
                    data: [
                        {{ $statusCounts['nouveau'] ?? 0 }},
                        {{ $statusCounts['en_relance'] ?? 0 }},
                        {{ $statusCounts['interesse'] ?? 0 }},
                        {{ $statusCounts['converti'] ?? 0 }},
                        {{ $statusCounts['abandonne'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#6c757d',
                        '#ffc107',
                        '#17a2b8',
                        '#28a745',
                        '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
</x-app-layout> 