<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 fw-bold"><i class="fas fa-plus me-2"></i>Créer un Site</h2>
    </x-slot>

    <div class="container py-4">
        <form action="{{ route('sites.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Infos générales -->
            <div class="mb-3">
                <label class="form-label">Nom du site</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Localisation</label>
                <input type="text" class="form-control @error('location') is-invalid @enderror" name="location" value="{{ old('location') }}" required>
                @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Superficie totale (m²)</label>
                    <input type="number" class="form-control @error('total_area') is-invalid @enderror" name="total_area" value="{{ old('total_area') }}" required>
                    @error('total_area')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Prix de base au m² (FCFA)</label>
                    <input type="number" class="form-control @error('base_price_per_sqm') is-invalid @enderror" name="base_price_per_sqm" value="{{ old('base_price_per_sqm') }}" required>
                    @error('base_price_per_sqm')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Frais de réservation (FCFA)</label>
                    <input type="number" class="form-control @error('reservation_fee') is-invalid @enderror" name="reservation_fee" value="{{ old('reservation_fee') }}" required>
                    @error('reservation_fee')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-4">
                    <label class="form-label">Frais d'adhésion (FCFA)</label>
                    <input type="number" class="form-control @error('membership_fee') is-invalid @enderror" name="membership_fee" value="{{ old('membership_fee') }}" required>
                    @error('membership_fee')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nombre total de lots</label>
                    <input type="number" class="form-control @error('total_lots') is-invalid @enderror" name="total_lots" value="{{ old('total_lots') }}" required>
                    @error('total_lots')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date de lancement</label>
                    <input type="date" class="form-control @error('launch_date') is-invalid @enderror" name="launch_date" value="{{ old('launch_date') }}">
                    @error('launch_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Latitude</label>
                    <input type="text" class="form-control @error('latitude') is-invalid @enderror" name="latitude" value="{{ old('latitude') }}" placeholder="Ex : 14.6928">
                    @error('latitude')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Longitude</label>
                    <input type="text" class="form-control @error('longitude') is-invalid @enderror" name="longitude" value="{{ old('longitude') }}" placeholder="Ex : -17.4467">
                    @error('longitude')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Plan de lotissement (PDF/Image, max 2MB)</label>
                    <input type="file" class="form-control @error('image_file') is-invalid @enderror" name="image_file" accept=".pdf,image/*">
                    @error('image_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Formats acceptés : JPG, PNG, PDF (max 2MB)</small>
                </div>
            </div>

            <!-- Plan de paiement avec options à cocher -->
            <div class="mt-4 p-3 border rounded bg-light">
                <h5 class="mb-3">Plan de paiement</h5>

                <!-- 12 mois -->
                <div class="form-check mb-2">
                    <input class="form-check-input toggle-price" type="checkbox" id="chk12" data-target="#price12" name="enable_12" {{ old('enable_12') ? 'checked' : '' }}>
                    <label class="form-check-label" for="chk12">Option 12 mois</label>
                </div>
                <div class="mb-3 d-none @error('price_12_months') is-invalid @enderror" id="price12">
                    <label class="form-label">Prix total (12 mois)</label>
                    <input type="number" class="form-control @error('price_12_months') is-invalid @enderror" name="price_12_months" value="{{ old('price_12_months') }}" placeholder="Ex : 5000000">
                    @error('price_12_months')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 24 mois -->
                <div class="form-check mb-2">
                    <input class="form-check-input toggle-price" type="checkbox" id="chk24" data-target="#price24" name="enable_24" {{ old('enable_24') ? 'checked' : '' }}>
                    <label class="form-check-label" for="chk24">Option 24 mois</label>
                </div>
                <div class="mb-3 d-none @error('price_24_months') is-invalid @enderror" id="price24">
                    <label class="form-label">Prix total (24 mois)</label>
                    <input type="number" class="form-control @error('price_24_months') is-invalid @enderror" name="price_24_months" value="{{ old('price_24_months') }}" placeholder="Ex : 5500000">
                    @error('price_24_months')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- 36 mois -->
                <div class="form-check mb-2">
                    <input class="form-check-input toggle-price" type="checkbox" id="chk36" data-target="#price36" name="enable_36" {{ old('enable_36') ? 'checked' : '' }}>
                    <label class="form-check-label" for="chk36">Option 36 mois</label>
                </div>
                <div class="mb-3 d-none @error('price_36_months') is-invalid @enderror" id="price36">
                    <label class="form-label">Prix total (36 mois)</label>
                    <input type="number" class="form-control" name="price_36_months" placeholder="Ex : 6 000 000">
                </div>

                <!-- Cash -->
                <div class="form-check mb-2">
                    <input class="form-check-input toggle-price" type="checkbox" id="chkCash" data-target="#priceCash" name="enable_cash" {{ old('enable_cash') ? 'checked' : '' }}>
                    <label class="form-check-label" for="chkCash">Option Paiement cash</label>
                </div>
                <div class="mb-3 d-none @error('price_cash') is-invalid @enderror" id="priceCash">
                    <label class="form-label">Prix total (Paiement cash)</label>
                    <input type="number" class="form-control @error('price_cash') is-invalid @enderror" name="price_cash" value="{{ old('price_cash') }}" placeholder="Ex : 4500000">
                    @error('price_cash')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <button class="btn btn-primary">✅ Enregistrer</button>
                <a href="{{ route('sites.index') }}" class="btn btn-secondary">❌ Annuler</a>
            </div>
        </form>
    </div>

    <!-- Script pour afficher/masquer les champs prix -->
    <script>
        document.querySelectorAll('.toggle-price').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const target = document.querySelector(this.dataset.target);
                if (this.checked) {
                    target.classList.remove('d-none');
                } else {
                    target.classList.add('d-none');
                    target.querySelector('input').value = ''; // réinitialiser si décoché
                }
            });
        });
    </script>
</x-app-layout>
