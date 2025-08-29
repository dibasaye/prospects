<x-app-layout>
    <x-slot name="header">
        <h2 class="h5 fw-bold">
            <i class="fas fa-edit me-2"></i>Modifier le Prospect : {{ $prospect->full_name }}
        </h2>
    </x-slot>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('prospects.update', $prospect) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <!-- Informations personnelles -->
                        <div class="col-md-6">
                            <label for="first_name" class="form-label fw-bold">Prénom *</label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" name="first_name" value="{{ old('first_name', $prospect->first_name) }}" required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="last_name" class="form-label fw-bold">Nom *</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" name="last_name" value="{{ old('last_name', $prospect->last_name) }}" required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Contact -->
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-bold">Téléphone *</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $prospect->phone) }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="phone_secondary" class="form-label">Téléphone secondaire</label>
                            <input type="tel" class="form-control @error('phone_secondary') is-invalid @enderror" 
                                   id="phone_secondary" name="phone_secondary" value="{{ old('phone_secondary', $prospect->phone_secondary) }}">
                            @error('phone_secondary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $prospect->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="address" class="form-label">Adresse</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="2">{{ old('address', $prospect->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Représentant légal -->
                        <div class="col-md-4">
                            <label for="representative_name" class="form-label">Nom du représentant légal</label>
                            <input type="text" class="form-control @error('representative_name') is-invalid @enderror" 
                                   id="representative_name" name="representative_name" value="{{ old('representative_name', $prospect->representative_name) }}">
                            @error('representative_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="representative_phone" class="form-label">Téléphone du représentant</label>
                            <input type="tel" class="form-control @error('representative_phone') is-invalid @enderror" 
                                   id="representative_phone" name="representative_phone" value="{{ old('representative_phone', $prospect->representative_phone) }}">
                            @error('representative_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="representative_address" class="form-label">Adresse du représentant</label>
                            <textarea class="form-control @error('representative_address') is-invalid @enderror" 
                                      id="representative_address" name="representative_address" rows="2">{{ old('representative_address', $prospect->representative_address) }}</textarea>
                            @error('representative_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Statut et assignation -->
                        <div class="col-md-4">
                            <label for="status" class="form-label fw-bold">Statut *</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="nouveau" {{ old('status', $prospect->status) == 'nouveau' ? 'selected' : '' }}>Nouveau</option>
                                <option value="en_relance" {{ old('status', $prospect->status) == 'en_relance' ? 'selected' : '' }}>En relance</option>
                                <option value="interesse" {{ old('status', $prospect->status) == 'interesse' ? 'selected' : '' }}>Intéressé</option>
                                <option value="converti" {{ old('status', $prospect->status) == 'converti' ? 'selected' : '' }}>Converti</option>
                                <option value="abandonne" {{ old('status', $prospect->status) == 'abandonne' ? 'selected' : '' }}>Abandonné</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="assigned_to_id" class="form-label">Assigné à</label>
                            <select class="form-select @error('assigned_to_id') is-invalid @enderror" id="assigned_to_id" name="assigned_to_id">
                                <option value="">Non assigné</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ old('assigned_to_id', $prospect->assigned_to_id) == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->first_name }} {{ $agent->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="interested_site_id" class="form-label fw-bold">Site d'intérêt *</label>
                            <select class="form-select @error('interested_site_id') is-invalid @enderror" id="interested_site_id" name="interested_site_id" required>
                                <option value="">Choisir un site...</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}" {{ old('interested_site_id', $prospect->interested_site_id) == $site->id ? 'selected' : '' }}>
                                        {{ $site->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('interested_site_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Budget -->
                        <div class="col-md-6">
                            <label for="budget_min" class="form-label">Budget minimum (FCFA)</label>
                            <input type="number" class="form-control @error('budget_min') is-invalid @enderror" 
                                   id="budget_min" name="budget_min" value="{{ old('budget_min', $prospect->budget_min) }}" min="0">
                            @error('budget_min')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="budget_max" class="form-label">Budget maximum (FCFA)</label>
                            <input type="number" class="form-control @error('budget_max') is-invalid @enderror" 
                                   id="budget_max" name="budget_max" value="{{ old('budget_max', $prospect->budget_max) }}" min="0">
                            @error('budget_max')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Suivi -->
                        <div class="col-md-6">
                            <label for="next_follow_up" class="form-label">Prochain suivi</label>
                            <input type="date" class="form-control @error('next_follow_up') is-invalid @enderror" 
                                   id="next_follow_up" name="next_follow_up" value="{{ old('next_follow_up', $prospect->next_follow_up?->format('Y-m-d')) }}">
                            @error('next_follow_up')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="4">{{ old('notes', $prospect->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('prospects.show', $prospect) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout> 