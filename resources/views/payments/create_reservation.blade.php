<x-app-layout>
    <x-slot name="header">
        <h2 class="h5">Paiement de Réservation - {{ $prospect->full_name }}</h2>
    </x-slot>

    <div class="container py-4" style="max-width: 600px;">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('payments.reservation.store', $prospect) }}" method="POST" novalidate>
            @csrf

            <div class="mb-3">
                <label for="amount" class="form-label">Montant (FCFA)</label>
                <input type="number" name="amount" id="amount" value="{{ old('amount', $amount) }}" class="form-control" required readonly>
            </div>

            <div class="mb-3">
                <label for="payment_method" class="form-label">Mode de paiement</label>
                <select name="payment_method" id="payment_method" class="form-select" required>
                    <option value="" disabled selected>Choisir un mode</option>
                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Espèces</option>
                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Virement</option>
                    <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="payment_date" class="form-label">Date de paiement</label>
                <input type="date" name="payment_date" id="payment_date" class="form-control" required value="{{ old('payment_date', date('Y-m-d')) }}">
            </div>

            @error('amount')
                <div class="text-danger mb-2">{{ $message }}</div>
            @enderror
            @error('payment_method')
                <div class="text-danger mb-2">{{ $message }}</div>
            @enderror
            @error('payment_date')
                <div class="text-danger mb-2">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn btn-primary">Valider le paiement</button>
        </form>
    </div>
</x-app-layout>
