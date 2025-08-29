@if(auth()->user()->isCaissier())
    <!-- Redirection ou message pour le caissier -->
    <x-app-layout>
        <x-slot name="header">
            <h2 class="header-title">
                {{ __('Accès non autorisé') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="mb-4">
                            <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Accès restreint</h3>
                        <p class="text-gray-600 mb-6">En tant que caissier, vous n'avez pas accès au tableau de bord. Votre rôle se limite à la validation des paiements.</p>
                        <a href="{{ route('payments.validation.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Aller à la validation des paiements
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>

    <script>
        // Redirection automatique après 3 secondes
        setTimeout(function() {
            window.location.href = "{{ route('payments.validation.index') }}";
        }, 3000);
    </script>
@else
    <!-- Tableau de bord normal pour les autres rôles -->
    <x-app-layout>
        <x-slot name="header">
            <h2 class="header-title">
                {{ __('Tableau de bord') }}
            </h2>
        </x-slot>

        <style>
            /* Reset minimal */
            * {
                box-sizing: border-box;
            }
            body, h2, h3, h4, p, div {
                margin: 0;
                padding: 0;
            }

            /* Header title */
            .header-title {
                font-weight: 600;
                font-size: 1.25rem; /* ~text-xl */
                color: #1f2937; /* gray-800 */
                line-height: 1.25;
                padding-bottom: 1rem;
            }

            /* Container padding */
            .content-padding {
                padding-top: 3rem;
                padding-bottom: 3rem;
            }

            /* Max width container */
            .max-container {
                max-width: 112rem; /* ~7xl = 1120px */
                margin-left: auto;
                margin-right: auto;
                padding-left: 1.5rem;  /* sm:px-6 */
                padding-right: 1.5rem;
            }
            @media(min-width: 1024px) {
                .max-container {
                    padding-left: 2rem; /* lg:px-8 */
                    padding-right: 2rem;
                }
            }

            /* Grid containers */
            .grid {
                display: grid;
                gap: 1.5rem; /* gap-6 */
            }
            /* 1 col default */
            .grid-cols-1 {
                grid-template-columns: 1fr;
            }
            /* md: 2 columns */
            @media(min-width: 768px) {
                .md-grid-cols-2 {
                    grid-template-columns: repeat(2, 1fr);
                }
            }
            /* lg: 4 columns */
            @media(min-width: 1024px) {
                .lg-grid-cols-4 {
                    grid-template-columns: repeat(4, 1fr);
                }
                .lg-grid-cols-2 {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            /* Margin bottom */
            .mb-8 {
                margin-bottom: 2rem;
            }

            /* Card */
            .card {
                background: white;
                border-radius: 0.5rem;
                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                overflow: hidden;
                display: flex;
                flex-direction: column;
                transition: box-shadow 0.3s ease;
            }
            .card:hover {
                box-shadow: 0 6px 15px rgba(0,0,0,0.15);
            }

            .card-body {
                padding: 1rem 1.5rem;
            }

            /* Flex container */
            .flex {
                display: flex;
                align-items: center;
            }
            .items-center {
                align-items: center;
            }
            .justify-between {
                justify-content: space-between;
            }

            /* Flexible grow */
            .flex-1 {
                flex: 1 1 auto;
            }

            /* Text styles */
            .text-lg {
                font-size: 1.125rem;
                line-height: 1.5rem;
            }
            .font-semibold {
                font-weight: 600;
            }
            .font-bold {
                font-weight: 700;
            }
            .text-gray-900 {
                color: #111827;
            }
            .text-gray-500 {
                color: #6b7280;
            }
            .text-primary-600 {
                color: #2563eb; /* blue-600 */
            }
            .text-green-600 {
                color: #16a34a; /* green-600 */
            }
            .text-yellow-600 {
                color: #ca8a04; /* yellow-600 */
            }
            .text-purple-600 {
                color: #7c3aed; /* purple-600 */
            }
            .text-sm {
                font-size: 0.875rem;
            }
            .text-3xl {
                font-size: 1.875rem;
                line-height: 2.25rem;
            }

            /* SVG icon size */
            svg {
                width: 2rem;
                height: 2rem;
            }

            /* Recent lists */
            .space-y-4 > * + * {
                margin-top: 1rem;
            }

            /* Cards for recent activities */
            .card-header {
                padding: 1rem 1.5rem;
                border-bottom: 1px solid #e5e7eb;
            }
            .card-header h3 {
                font-size: 1.125rem;
                font-weight: 600;
                color: #111827;
            }

            /* Recent item */
            .recent-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                background-color: #f9fafb; /* gray-50 */
                border-radius: 0.5rem;
                padding: 0.75rem 1rem;
            }
            .recent-item > div {
                flex: 1;
            }
            .recent-item h4 {
                font-weight: 500;
                color: #111827;
                font-size: 1rem;
                margin-bottom: 0.25rem;
            }
            .recent-item p {
                font-size: 0.875rem;
                color: #6b7280;
                margin: 0;
            }
            .recent-item .text-right {
                text-align: right;
                min-width: 4.5rem;
            }

            /* Status badges */
            .status-active {
                color: #16a34a;
                font-weight: 600;
            }
            .status-pending {
                color: #ca8a04;
                font-weight: 600;
            }
            .status-converted {
                color: #2563eb;
                font-weight: 600;
            }

            /* Link styling for clickable cards */
            a.card {
                text-decoration: none;
                cursor: pointer;
                color: inherit;
                display: flex;
                flex-direction: column;
                transition: box-shadow 0.3s ease;
            }
            a.card:hover {
                box-shadow: 0 6px 15px rgba(0,0,0,0.15);
            }

            /* Utilities */
            .mt-4 {
                margin-top: 1rem;
            }
            .py-12 {
                padding-top: 3rem;
                padding-bottom: 3rem;
            }
            .text-xs {
                font-size: 0.75rem;
                color: #6b7280;
            }
        </style>

        <div class="py-12">
            <div class="max-container content-padding">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md-grid-cols-2 lg-grid-cols-4 mb-8">
                    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                        <div class="card">
                            <div class="card-body">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">Prospects Total</h3>
                                        <p class="text-3xl font-bold text-primary-600">{{ $stats['total_prospects'] }}</p>
                                        <p class="text-sm text-gray-500">Actifs: {{ $stats['active_prospects'] }}</p>
                                    </div>
                                    <div class="text-primary-600">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">Sites Actifs</h3>
                                        <p class="text-3xl font-bold text-green-600">{{ $stats['total_sites'] }}</p>
                                        <p class="text-sm text-gray-500">Lots vendus: {{ $stats['sold_lots'] }}</p>
                                    </div>
                                    <div class="text-green-600">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">Paiements</h3>
                                        <p class="text-3xl font-bold text-yellow-600">{{ number_format($stats['total_payments'], 0, ',', ' ') }} F</p>
                                        <p class="text-sm text-gray-500">En attente: {{ $stats['pending_payments'] }}</p>
                                    </div>
                                    <div class="text-yellow-600">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('contracts.index') }}" class="card">
                            <div class="card-body">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">Contrats</h3>
                                        <p class="text-3xl font-bold text-purple-600">{{ $stats['total_contracts'] }}</p>
                                        <p class="text-sm text-gray-500">Signés: {{ $stats['signed_contracts'] }}</p>
                                    </div>
                                    <div class="text-purple-600">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @else
                        <div class="card">
                            <div class="card-body">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">Mes Prospects</h3>
                                        <p class="text-3xl font-bold text-primary-600">{{ $stats['my_prospects'] }}</p>
                                        <p class="text-sm text-gray-500">Actifs: {{ $stats['active_prospects'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">Convertis</h3>
                                        <p class="text-3xl font-bold text-green-600">{{ $stats['converted_prospects'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">Mes Contrats</h3>
                                        <p class="text-3xl font-bold text-purple-600">{{ $stats['my_contracts'] }}</p>
                                        <p class="text-sm text-gray-500">Signés: {{ $stats['signed_contracts'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">Commissions</h3>
                                        <p class="text-3xl font-bold text-yellow-600">{{ number_format($stats['my_payments'], 0, ',', ' ') }} F</p>
                                        <p class="text-sm text-gray-500">Validés: {{ $stats['validated_payments'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="flex items-center">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">Paiements en attente</h3>
                                        <p class="text-3xl font-bold text-orange-600">{{ $stats['pending_payments'] }}</p>
                                        <p class="text-sm text-gray-500">En cours de validation</p>
                                    </div>
                                    <div class="text-orange-600">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Recent Activities -->
                <div class="grid grid-cols-1 lg-grid-cols-2 gap-6">
                    <!-- Recent Prospects -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Prospects Récents</h3>
                        </div>
                        <div class="card-body">
                            @if($recentProspects->count() > 0)
                                <div class="space-y-4">
                                    @foreach($recentProspects as $prospect)
                                        <div class="recent-item">
                                           <div>
        <h4>{{ $prospect->full_name }}</h4>
        <p>
            {{ $prospect->phone }}
            <a href="tel:{{ $prospect->phone }}" style="margin-left: 8px; color: #2563eb;" title="Appeler">
                <svg xmlns="http://www.w3.org/2000/svg" class="inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5a2 2 0 012-2h1.586a1 1 0 01.707.293l2.414 2.414a1 1 0 010 1.414L8.414 9a16.001 16.001 0 006.586 6.586l1.879-1.879a1 1 0 011.414 0l2.414 2.414a1 1 0 01.293.707V19a2 2 0 01-2 2h-1c-9.941 0-18-8.059-18-18V5z" />
                </svg>
            </a>
        </p>
        @if($prospect->interestedSite)
            <p>Intéressé par: {{ $prospect->interestedSite->name }}</p>
        @endif
    </div>

                                            <div class="text-right">
                                                <span class="status-{{ $prospect->status }}">{{ ucfirst($prospect->status) }}</span>
                                                <p class="text-xs">{{ $prospect->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('prospects.index') }}" class="text-primary-600-hover">Voir tous les prospects →</a>
                                </div>
                            @else
                                <p class="text-gray-500" style="text-align:center; padding:1rem 0;">Aucun prospect récent.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Payments -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Paiements Récents</h3>
                        </div>
                        <div class="card-body">
                            @if($recentPayments->count() > 0)
                                <div class="space-y-4">
                                    @foreach($recentPayments as $payment)
                                        <div class="recent-item">
                                            <div>
                                                <h4>{{ $payment->client->full_name }}</h4>
                                                <p>{{ $payment->site->name }}</p>
                                                <p>{{ ucfirst($payment->type) }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-medium text-green-600">{{ number_format($payment->amount, 0, ',', ' ') }} F</p>
                                                <p class="text-xs">{{ $payment->payment_date->format('d/m/Y') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500" style="text-align:center; padding:1rem 0;">Aucun paiement récent.</p>
                            @endif
                        </div>
                    </div>

                    @if(auth()->user()->isAgent() && isset($pendingPayments))
                    <!-- Paiements en attente de validation -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Paiements en attente de validation</h3>
                        </div>
                        <div class="card-body">
                            @if($pendingPayments->count() > 0)
                                <div class="space-y-4">
                                    @foreach($pendingPayments as $payment)
                                        <div class="recent-item">
                                            <div>
                                                <h4>{{ $payment->client->full_name }}</h4>
                                                <p>{{ $payment->site->name }} - {{ ucfirst($payment->type) }}</p>
                                                <p class="text-sm">
                                                    @switch($payment->validation_status)
                                                        @case('pending')
                                                            <span class="status-pending">En attente de validation caissier</span>
                                                            @break
                                                        @case('caissier_validated')
                                                            <span class="status-active">Validé par le caissier</span>
                                                            @if($payment->caissierValidatedBy)
                                                                <br><small>Par: {{ $payment->caissierValidatedBy->full_name }}</small>
                                                            @endif
                                                            @break
                                                    @endswitch
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-medium text-yellow-600">{{ number_format($payment->amount, 0, ',', ' ') }} F</p>
                                                <p class="text-xs">{{ $payment->created_at->format('d/m/Y') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500" style="text-align:center; padding:1rem 0;">Aucun paiement en attente.</p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </x-app-layout>
@endif