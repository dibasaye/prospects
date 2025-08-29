<x-app-layout>
    @php
        // Définition des valeurs par défaut
        $paymentDetails = $paymentDetails ?? collect();
        $prospectDetails = $prospectDetails ?? collect();
        $commercialDetails = $commercialDetails ?? collect();
        $monthlyStats = collect($monthlyStats ?? []);
        $topComercials = $topComercials ?? collect();
        $commercials = $commercials ?? collect();
        
        $globalStats = $globalStats ?? [];
        $globalStats['total_commercials'] = $globalStats['total_commercials'] ?? 0;
        $globalStats['total_prospects'] = $globalStats['total_prospects'] ?? 0;
        $globalStats['total_payments'] = $globalStats['total_payments'] ?? 0;
        $globalStats['conversion_rate'] = $globalStats['conversion_rate'] ?? 0;
    @endphp

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                <i class="fas fa-chart-line me-2"></i>Performance des Commerciaux
            </h2>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>Imprimer
                </button>
                <a href="{{ route('commercial.performance.export') }}" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i>Exporter
                </a>
            </div>
        </div>
    </x-slot>

    <style>
        .performance-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            transition: transform 0.3s ease;
        }
        
        .performance-card:hover {
            transform: translateY(-5px);
        }
        
        .top-performer {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: 3px solid #ffd700;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-3px);
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .commercial-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .trend-up { color: #28a745; }
        .trend-down { color: #dc3545; }
        
        .clickable-card {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        @media print {
            .no-print { display: none !important; }
        }
    </style>

    <div class="container-fluid py-4">
        <!-- Cartes de stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card p-4 text-center clickable-card" data-target="commercials-list">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-users fa-2x text-primary me-3"></i>
                        <div>
                            <h3 class="mb-0 fw-bold">{{ $globalStats['total_commercials'] }}</h3>
                            <small class="text-muted">Commerciaux</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stats-card p-4 text-center clickable-card" data-target="prospects-list">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-user-plus fa-2x text-success me-3"></i>
                        <div>
                            <h3 class="mb-0 fw-bold">{{ $globalStats['total_prospects'] }}</h3>
                            <small class="text-muted">Prospects</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stats-card p-4 text-center clickable-card" data-target="payments-list">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-money-bill-wave fa-2x text-warning me-3"></i>
                        <div>
                            <h3 class="mb-0 fw-bold">{{ number_format($globalStats['total_payments'], 0, ',', ' ') }} F</h3>
                            <small class="text-muted">Recouvrement</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="chart-container position-relative" style="height: 250px;">
                    <h4 class="mb-3">
                        <i class="fas fa-gauge me-2 text-danger"></i>Taux de conversion
                    </h4>
                    <canvas id="gaugeChart"></canvas>
                    <div id="gaugeValue" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); font-size:1.5rem; font-weight:bold;">
                        {{ $globalStats['conversion_rate'] }}%
                    </div>
                </div>
            </div>
        </div>

        <!-- Top commerciaux -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="chart-container">
                    <h4 class="mb-4">
                        <i class="fas fa-trophy me-2 text-warning"></i>Top Performers
                    </h4>
                    <div class="row">
                        @forelse($topComercials as $index => $commercial)
                        <div class="col-md-2 col-sm-4 col-6 mb-3">
                            <div class="performance-card card h-100 clickable-card"
                                data-target="commercial-details"
                                data-commercial="{{ $commercial['id'] }}"
                                @if($index === 0) top-performer @endif>
                                <div class="card-body text-center p-3">
                                    <h6 class="mb-1 fw-bold">{{ $commercial['name'] }}</h6>
                                    <p class="mb-2">{{ number_format($commercial['total_payments'], 0, ',', ' ') }} F</p>
                                    <small>{{ $commercial['conversion_rate'] }}% taux</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center text-muted">
                            Aucun commercial trouvé
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="chart-container">
                    <h4 class="mb-4">
                        <i class="fas fa-chart-line me-2"></i>Évolution mensuelle
                    </h4>
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-container">
                    <h4 class="mb-4">
                        <i class="fas fa-chart-pie me-2"></i>Répartition des performances
                    </h4>
                    <canvas id="repartitionChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Tableau -->
        <div class="row">
            <div class="col-12">
                <div class="commercial-table">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Commercial</th>
                                    <th>Prospects</th>
                                    <th>Contrats</th>
                                    <th>Recouvrement</th>
                                    <th>Taux</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($commercials as $commercial)
                                <tr class="clickable-card" data-target="commercial-details" data-commercial="{{ $commercial['id'] }}">
                                    <td>{{ $commercial['name'] }}</td>
                                    <td>{{ $commercial['total_prospects'] }}</td>
                                    <td>{{ $commercial['total_contracts'] }}</td>
                                    <td>{{ number_format($commercial['total_payments'], 0, ',', ' ') }} F</td>
                                    <td>
                                        <span class="badge bg-{{ $commercial['conversion_rate'] >= 20 ? 'success' : 'warning' }}">
                                            {{ $commercial['conversion_rate'] }}%
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Aucune donnée disponible</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Détails</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p>Chargement en cours...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Graphiques
        document.addEventListener('DOMContentLoaded', function() {
            // Gauge
            const gaugeCtx = document.getElementById('gaugeChart');
            if (gaugeCtx) {
                new Chart(gaugeCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [{{ $globalStats['conversion_rate'] }}, 100 - {{ $globalStats['conversion_rate'] }}],
                            backgroundColor: ['#28a745', '#e9ecef'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        rotation: -90,
                        circumference: 180,
                        cutout: '80%',
                        plugins: { legend: { display: false } }
                    }
                });
            }

            // Graphique d'évolution mensuelle
            const evolutionCtx = document.getElementById('monthlyChart').getContext('2d');
            const monthlyData = @json($monthlyStats);

            new Chart(evolutionCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(item => item.month),
                    datasets: [{
                        label: 'Chiffre d\'affaires',
                        data: monthlyData.map(item => item.total_payments),
                        borderColor: '#4CAF50',
                        tension: 0.3
                    }, {
                        label: 'Conversions',
                        data: monthlyData.map(item => item.conversion_rate),
                        borderColor: '#2196F3',
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Performance sur les 6 derniers mois'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    let value = context.parsed.y;
                                    if (label === 'Chiffre d\'affaires') {
                                        return label + ': ' + value.toLocaleString('fr-FR') + ' F';
                                    }
                                    return label + ': ' + value + '%';
                                }
                            }
                        }
                    }
                }
            });

            // Graphique de répartition
            const repartitionCtx = document.getElementById('repartitionChart').getContext('2d');
            new Chart(repartitionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Prospects convertis', 'En cours', 'Non qualifiés'],
                    datasets: [{
                        data: [
                            {{ $globalStats['converted_prospects'] ?? 0 }},
                            {{ $globalStats['in_progress_prospects'] ?? 0 }},
                            {{ $globalStats['unqualified_prospects'] ?? 0 }}
                        ],
                        backgroundColor: ['#4CAF50', '#FFC107', '#9E9E9E']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.parsed;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = ((value * 100) / total).toFixed(1);
                                    return `${label}: ${percentage}% (${value})`;
                                }
                            }
                        }
                    }
                }
            });

            // Modal
            const modal = new bootstrap.Modal('#detailsModal');
            
            document.querySelectorAll('.clickable-card').forEach(card => {
                card.addEventListener('click', function() {
                    const target = this.dataset.target;
                    const commercialId = this.dataset.commercial;
                    
                    // Titre dynamique
                    document.getElementById('modalTitle').textContent = 
                        target === 'payments-list' ? 'Détails des paiements' : 
                        target === 'commercial-details' ? 'Détails commercial' : 
                        'Détails';
                    
                    // Chargement AJAX
                    // Debug CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    console.log('CSRF Token:', csrfToken);
                    console.log('Target:', target);
                    console.log('Commercial ID:', commercialId);
                    
                    const url = `/api/${target}${commercialId ? '?commercial_id='+commercialId : ''}`;
                    console.log('Request URL:', url);
                    
                    const headers = {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken || ''
                    };
                    console.log('Request Headers:', headers);
                    
                    fetch(url, {
                        headers: headers,
                        credentials: 'same-origin'
                    })
                        .then(response => {
                            // Check if the response is ok and if content type is JSON
                            const contentType = response.headers.get('content-type');
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            if (!contentType || !contentType.includes('application/json')) {
                                throw new Error('La réponse du serveur n\'est pas au format JSON attendu');
                            }
                            return response.json();
                        })
                        .then(data => {
                            let html = '';
                            
                            if (target === 'payments-list') {
                                html = `<table class="table"><thead><tr>
                                    <th>Date</th><th>Montant</th><th>Client</th>
                                </tr></thead><tbody>`;
                                
                                data.forEach(payment => {
                                    html += `<tr>
                                        <td>${new Date(payment.date).toLocaleDateString()}</td>
                                        <td>${payment.amount.toLocaleString()} F</td>
                                        <td>${payment.client_name}</td>
                                    </tr>`;
                                });
                                
                                html += '</tbody></table>';
                            } else if (target === 'commercial-details' && commercialId) {
                                // Handle commercial details
                                html = `
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>${data.name}</h5>
                                            <p>Email: ${data.email}</p>
                                            <p>Téléphone: ${data.phone || 'N/A'}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p>Prospects: ${data.total_prospects}</p>
                                            <p>Prospects convertis: ${data.converted_prospects}</p>
                                            <p>Taux de conversion: ${data.conversion_rate}%</p>
                                            <p>Recouvrement: ${data.total_payments.toLocaleString()} F</p>
                                        </div>
                                    </div>
                                `;
                            } else if (target === 'prospects-list') {
                                // Handle prospects list
                                html = `<table class="table"><thead><tr>
                                    <th>Nom</th><th>Téléphone</th><th>Statut</th><th>Date</th>
                                </tr></thead><tbody>`;
                                
                                data.forEach(prospect => {
                                    html += `<tr>
                                        <td>${prospect.name}</td>
                                        <td>${prospect.phone}</td>
                                        <td>${prospect.status}</td>
                                        <td>${new Date(prospect.created_at).toLocaleDateString()}</td>
                                    </tr>`;
                                });
                                
                                html += '</tbody></table>';
                            } else if (target === 'commercials-list') {
                                // Handle commercials list
                                html = `<table class="table"><thead><tr>
                                    <th>Nom</th><th>Email</th>
                                </tr></thead><tbody>`;
                                
                                data.forEach(commercial => {
                                    html += `<tr>
                                        <td>${commercial.name}</td>
                                        <td>${commercial.email}</td>
                                    </tr>`;
                                });
                                
                                html += '</tbody></table>';
                            } else {
                                html = 'Aucune donnée disponible pour ce type de requête';
                            }
                            
                            document.getElementById('modalContent').innerHTML = html || 'Aucune donnée disponible';
                        })
                        .catch(error => {
                            console.error('Erreur lors du chargement des données:', error);
                            document.getElementById('modalContent').innerHTML = `
                                <div class="alert alert-danger">
                                    Erreur lors du chargement des données: ${error.message}
                                </div>
                            `;
                        });
                    
                    modal.show();
                });
            });
        });
    </script>
</x-app-layout>