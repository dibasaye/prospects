<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Gestion des Prospects') }}
            </h2>
            <a href="{{ route('prospects.create') }}" class="btn-primary">
                Nouveau Prospect
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="card mb-6">
                <div class="card-body">
                    <form method="GET" action="{{ route('prospects.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recherche</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   placeholder="Nom, téléphone, email..."
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut</label>
                            <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Tous les statuts</option>
                                <option value="nouveau" {{ request('status') == 'nouveau' ? 'selected' : '' }}>Nouveau</option>
                                <option value="en_relance" {{ request('status') == 'en_relance' ? 'selected' : '' }}>En relance</option>
                                <option value="interesse" {{ request('status') == 'interesse' ? 'selected' : '' }}>Intéressé</option>
                                <option value="converti" {{ request('status') == 'converti' ? 'selected' : '' }}>Converti</option>
                                <option value="abandonne" {{ request('status') == 'abandonne' ? 'selected' : '' }}>Abandonné</option>
                            </select>
                        </div>
                        
                        @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                        <div>
                            <label for="assigned_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assigné à</label>
                            <select name="assigned_to" id="assigned_to" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                                <option value="">Tous les agents</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ request('assigned_to') == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        
                        <div class="flex items-end">
                            <button type="submit" class="btn-primary w-full">Filtrer</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Prospects List -->
            <div class="card">
                <div class="card-body">
                    @if($prospects->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Prospect
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Contact
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Statut
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Assigné à
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Site d'intérêt
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Date de contact
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($prospects as $prospect)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $prospect->full_name }}
                                                    </div>
                                                    @if($prospect->budget_min || $prospect->budget_max)
                                                        <div class="text-sm text-gray-500">
                                                            Budget: {{ number_format($prospect->budget_min ?? 0, 0, ',', ' ') }} - {{ number_format($prospect->budget_max ?? 0, 0, ',', ' ') }} F
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $prospect->phone }}</div>
                                                @if($prospect->email)
                                                    <div class="text-sm text-gray-500">{{ $prospect->email }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="status-{{ $prospect->status }}">
                                                    {{ ucfirst(str_replace('_', ' ', $prospect->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $prospect->assignedTo->full_name ?? 'Non assigné' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $prospect->interestedSite->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $prospect->contact_date?->format('d/m/Y') ?? $prospect->created_at->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end space-x-2">
                                                    <a href="{{ route('prospects.show', $prospect) }}" class="text-primary-600 hover:text-primary-900">
                                                        Voir
                                                    </a>
                                                    <a href="{{ route('prospects.edit', $prospect) }}" class="text-yellow-600 hover:text-yellow-900">
                                                        Modifier
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-6">
                            {{ $prospects->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium">Aucun prospect</h3>
                                <p class="mt-1 text-sm">Commencez par créer votre premier prospect.</p>
                                <div class="mt-6">
                                    <a href="{{ route('prospects.create') }}" class="btn-primary">
                                        Nouveau Prospect
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>