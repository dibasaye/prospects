<x-app-layout>
    <x-slot name="header">
        <h2 class="h5">📄 Liste des Contrats</h2>
    </x-slot>

    <div class="container py-4">
        <form method="GET" action="{{ route('contracts.index') }}" class="mb-4 row">
            <div class="col-md-4">
                <input type="text" name="client" value="{{ request('client') }}" class="form-control" placeholder="🔎 Rechercher un client...">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Statut --</option>
                    <option value="brouillon" {{ request('status') == 'brouillon' ? 'selected' : '' }}>📝 Brouillon</option>
                    <option value="signe" {{ request('status') == 'signe' ? 'selected' : '' }}> Signé</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary" type="submit">Filtrer</button>
                <a href="{{ route('contracts.index') }}" class="btn btn-secondary">Réinitialiser</a>
            </div>
            <div class="col-md-2 text-end">
                <a href="{{ route('contracts.export') }}" class="btn btn-outline-success">
                    📤 Export CSV
                </a>
            </div>
        </form>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Lot</th>
                    <th>Durée</th>
                    <th>Montant Total</th>
                    <th>Statut</th>
                    <th>Signature</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($contracts as $contract)
                    <tr>
                        <td>{{ $contract->contract_number }}</td>
                        <td>{{ $contract->client->full_name }}</td>
                         <td>{{ optional($contract->lot)->lot_number ?? '-' }}</td>

                        <td>{{ $contract->payment_duration_months }} mois</td>
                        <td>{{ number_format($contract->total_amount, 0, ',', ' ') }} F</td>
                        <td>
                            @if($contract->status === 'signe')
                                <span class="badge bg-success">Signé</span>
                            @else
                                <span class="badge bg-warning text-dark">Brouillon</span>
                            @endif
                        </td>
                        <td>
                            @if ($contract->signed_by_client && $contract->signed_by_agent)
                                <span class="text-success">🖊️ Double Signature</span>
                            @elseif ($contract->signed_by_client)
                                <span class="text-primary"> Client</span>
                            @else
                                <span class="text-muted"> En attente</span>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            <a href="{{ route('contracts.show', $contract) }}" class="btn btn-sm btn-info" title="Voir les détails">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('contracts.preview', $contract) }}" class="btn btn-sm btn-warning" title="Prévisualiser le contrat">
                                <i class="fas fa-search"></i> Prévisualiser
                            </a>
                            <a href="{{ route('contracts.export.pdf', $contract) }}" class="btn btn-sm btn-danger" title="Télécharger en PDF" target="_blank">
                                <i class="fas fa-file-pdf"></i> PDF
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">Aucun contrat trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $contracts->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
