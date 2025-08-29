<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fas fa-plus me-2"></i>Créer un Nouveau Lot - {{ $site->name }}
        </h2>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('lots.store', ['site' => $site->id]) }}" method="POST">
                        @csrf

                        <!-- Numéro du lot -->
                        <div class="mb-3">
                            <label for="lot_number" class="form-label">Numéro du lot</label>
                            <input type="text" name="lot_number" id="lot_number" class="form-control" value="{{ old('lot_number') }}" required>
                            @error('lot_number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Superficie -->
                        <div class="mb-3">
                            <label for="area" class="form-label">Superficie (m²)</label>
                            <input type="number" step="0.01" name="area" id="area" class="form-control" value="{{ old('area') }}" required>
                            @error('area')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Position -->
                        <div class="mb-3">
                            <label for="position" class="form-label">Position</label>
                            <select name="position" id="position" class="form-select" required>
                                <option value="">-- Choisir --</option>
                                <option value="interieur" {{ old('position') == 'interieur' ? 'selected' : '' }}>Intérieur</option>
                                <option value="facade" {{ old('position') == 'facade' ? 'selected' : '' }}>Façade (+10%)</option>
                                <option value="angle" {{ old('position') == 'angle' ? 'selected' : '' }}>Angle (+10%)</option>
                            </select>
                            @error('position')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Prix de base -->
                        <div class="mb-3">
                            <label for="base_price" class="form-label">Prix de base (FCFA)</label>
                            <input type="number" step="0.01" name="base_price" id="base_price" class="form-control" value="{{ old('base_price') }}" required>
                            @error('base_price')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut du lot</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="disponible" {{ old('status') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                                <option value="reserve_temporaire" {{ old('status') == 'reserve_temporaire' ? 'selected' : '' }}>Réservation temporaire</option>
                                <option value="reserve" {{ old('status') == 'reserve' ? 'selected' : '' }}>Réservé</option>
                                <option value="vendu" {{ old('status') == 'vendu' ? 'selected' : '' }}>Vendu</option>
                            </select>
                            @error('status')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description (optionnelle)</label>
                            <textarea name="description" id="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('sites.show', $site) }}" class="btn btn-secondary me-2">Annuler</a>
                            <button type="submit" class="btn btn-primary">Créer le lot</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
