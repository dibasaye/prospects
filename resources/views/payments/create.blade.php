<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 fw-bold">Ajouter un paiement d'adhésion pour {{ $prospect->full_name }}</h2>
    </x-slot>

    <div class="container py-4">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('payments.store', $prospect) }}">
            @csrf

            <div class="mb-3">
                <label for="site_id" class="form-label">Site concerné</label>
                <select id="site_id" name="site_id" class="form-select @error('site_id') is-invalid @enderror" required>
                    <option value="">-- Choisir un site --</option>
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}" {{ old('site_id') == $site->id ? 'selected' : '' }}>
                            {{ $site->name }}
                        </option>
                    @endforeach
                </select>
                @error('site_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <input type="hidden" name="type" value="adhesion">

            <div class="mb-3">
                <label for="amount" class="form-label">Montant payé (FCFA)</label>
                <input type="number" step="100" id="amount" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', 50000) }}" required>
                @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="payment_method" class="form-label">Mode de paiement</label>
                <select id="payment_method" name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                    <option value="">-- Choisir un mode --</option>
                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Espèces</option>
                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Virement bancaire</option>
                    <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                </select>
                @error('payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label for="payment_date" class="form-label">Date du paiement</label>
                <input type="date" id="payment_date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                @error('payment_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer le paiement</button>
        </form>
    </div>
</x-app-layout>
