<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tableau de bord') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                    <div class="card">
                        <div class="card-body">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Prospects Total</h3>
                                    <p class="text-3xl font-bold text-primary-600">{{ $stats['total_prospects'] }}</p>
                                    <p class="text-sm text-gray-500">Actifs: {{ $stats['active_prospects'] }}</p>
                                </div>
                                <div class="text-primary-600">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Sites Actifs</h3>
                                    <p class="text-3xl font-bold text-green-600">{{ $stats['total_sites'] }}</p>
                                    <p class="text-sm text-gray-500">Lots vendus: {{ $stats['sold_lots'] }}</p>
                                </div>
                                <div class="text-green-600">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Paiements</h3>
                                    <p class="text-3xl font-bold text-yellow-600">{{ number_format($stats['total_payments'], 0, ',', ' ') }} F</p>
                                    <p class="text-sm text-gray-500">En attente: {{ $stats['pending_payments'] }}</p>
                                </div>
                                <div class="text-yellow-600">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Contrats</h3>
                                    <p class="text-3xl font-bold text-purple-600">{{ $stats['total_contracts'] }}</p>
                                    <p class="text-sm text-gray-500">Signés: {{ $stats['signed_contracts'] }}</p>
                                </div>
                                <div class="text-purple-600">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Mes Prospects</h3>
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
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Convertis</h3>
                                    <p class="text-3xl font-bold text-green-600">{{ $stats['converted_prospects'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <div class="flex items-center">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Mes Contrats</h3>
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
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Commissions</h3>
                                    <p class="text-3xl font-bold text-yellow-600">{{ number_format($stats['my_payments'], 0, ',', ' ') }} F</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Recent Activities -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Prospects -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Prospects Récents</h3>
                    </div>
                    <div class="card-body">
                        @if($recentProspects->count() > 0)
                            <div class="space-y-4">
                                @foreach($recentProspects as $prospect)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $prospect->full_name }}</h4>
                                            <p class="text-sm text-gray-500">{{ $prospect->phone }}</p>
                                            @if($prospect->interestedSite)
                                                <p class="text-sm text-gray-500">Intéressé par: {{ $prospect->interestedSite->name }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <span class="status-{{ $prospect->status }}">{{ ucfirst($prospect->status) }}</span>
                                            <p class="text-xs text-gray-500 mt-1">{{ $prospect->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('prospects.index') }}" class="text-primary-600 hover:text-primary-700 font-medium">Voir tous les prospects →</a>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">Aucun prospect récent.</p>
                        @endif
                    </div>
                </div>

                <!-- Recent Payments -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Paiements Récents</h3>
                    </div>
                    <div class="card-body">
                        @if($recentPayments->count() > 0)
                            <div class="space-y-4">
                                @foreach($recentPayments as $payment)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $payment->client->full_name }}</h4>
                                            <p class="text-sm text-gray-500">{{ $payment->site->name }}</p>
                                            <p class="text-sm text-gray-500">{{ ucfirst($payment->type) }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-green-600">{{ number_format($payment->amount, 0, ',', ' ') }} F</p>
                                            <p class="text-xs text-gray-500">{{ $payment->payment_date->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">Aucun paiement récent.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>