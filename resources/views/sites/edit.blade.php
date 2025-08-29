<x-app-layout> 
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fas fa-edit me-2"></i>Modifier le Site : {{ $site->name }}
        </h2>
    </x-slot>

    <div class="container py-4">
        <form method="POST" action="{{ route('sites.update', $site->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Nom du site</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $site->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="location" class="form-label">Localisation</label>
                    <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $site->location) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $site->description) }}</textarea>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="total_area" class="form-label">Superficie Totale (m²)</label>
                    <input type="number" name="total_area" id="total_area" class="form-control" value="{{ old('total_area', $site->total_area) }}">
                </div>
                <div class="col-md-4">
                    <label for="base_price_per_sqm" class="form-label">Prix / m²</label>
                    <input type="number" name="base_price_per_sqm" id="base_price_per_sqm" class="form-control" value="{{ old('base_price_per_sqm', $site->base_price_per_sqm) }}" required>
                </div>
                <div class="col-md-4">
                    <label for="payment_plan" class="form-label">Plan de paiement</label>
                    <select name="payment_plan" id="payment_plan" class="form-select" required>
                        <option value="12_months" {{ old('payment_plan', $site->payment_plan) == '12_months' ? 'selected' : '' }}>12 mois</option>
                        <option value="24_months" {{ old('payment_plan', $site->payment_plan) == '24_months' ? 'selected' : '' }}>24 mois</option>
                        <option value="36_months" {{ old('payment_plan', $site->payment_plan) == '36_months' ? 'selected' : '' }}>36 mois</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="reservation_fee" class="form-label">Frais de réservation</label>
                    <input type="number" name="reservation_fee" id="reservation_fee" class="form-control" value="{{ old('reservation_fee', $site->reservation_fee) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="membership_fee" class="form-label">Frais d'adhésion</label>
                    <input type="number" name="membership_fee" id="membership_fee" class="form-control" value="{{ old('membership_fee', $site->membership_fee) }}" required>
                </div>
            </div>

            {{-- Upload plan de lotissement --}}
            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Plan de lotissement (PDF/Image)</label>
                    <input type="file" class="form-control" name="image_file" accept=".pdf,image/*">
                </div>
                <div class="col-md-6">
                    @if($site->image_url)
                        @php
                            $isPdf = Str::endsWith($site->image_url, '.pdf');
                        @endphp

                        <p class="mt-2 mb-1"><strong>Fichier actuel :</strong></p>
                        @if($isPdf)
                            <a href="{{ asset('storage/' . $site->image_url) }}" target="_blank" class="btn btn-outline-primary btn-sm">Voir le PDF</a>
                        @else
                            <img src="{{ asset('storage/' . $site->image_url) }}" alt="Plan du lotissement" style="max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.2);">
                        @endif
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('sites.show', $site->id) }}" class="btn btn-secondary me-2">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</x-app-layout>
