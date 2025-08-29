<x-app-layout>
    <x-slot name="header">
        <h2 class="h5">🧾 Génération d’un contrat - {{ $prospect->full_name }}</h2>
    </x-slot>

    <div class="container py-4">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('contracts.store') }}" method="POST">
            @csrf

            <input type="hidden" name="client_id" value="{{ $prospect->id }}">
            <input type="hidden" name="site_id" value="{{ $prospect->interested_site_id }}">
            <input type="hidden" name="lot_id" value="{{ $lot->id }}">

            <div class="mb-3">
                <label for="total_amount" class="form-label">Montant total</label>
                <input type="number" name="total_amount" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="payment_duration_months" class="form-label">Durée de paiement (en mois)</label>
                <select name="payment_duration_months" class="form-select" required>
                    <option value="12">12 mois</option>
                    <option value="24">24 mois</option>
                    <option value="36">36 mois</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="start_date" class="form-label">Date de début</label>
                <input type="date" name="start_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="terms_and_conditions" class="form-label">Conditions générales</label>
                <textarea name="terms_and_conditions[]" rows="4" class="form-control" placeholder="Écrire les conditions ligne par ligne"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">📄 Générer le contrat</button>
        </form>
    </div>
</x-app-layout>
