<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0">Créer un nouveau prospect</h2>
            <a href="{{ route('prospects.index') }}" class="btn btn-secondary">Retour</a>
        </div>
    </x-slot>

     <div class="container py-4">
        <!-- Ajout de l'option rapide -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Enregistrement rapide par téléphone</h5>
                
                <!-- Onglets pour les différentes méthodes -->
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#manual-entry">
                            <i class="fas fa-keyboard me-2"></i>Saisie manuelle
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#file-import">
                            <i class="fas fa-file-import me-2"></i>Import fichier
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#bulk-paste">
                            <i class="fas fa-paste me-2"></i>Copier-coller
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Saisie manuelle -->
                    <div class="tab-pane fade show active" id="manual-entry">
                        <div class="mb-3">
                            <button type="button" class="btn btn-success btn-sm" id="addPhoneField">+ Ajouter un autre numéro</button>
                        </div>

                        <form method="POST" action="{{ route('prospects.store-multiple') }}" id="quickForm">
                            @csrf
                            <div id="phoneFields">
                                <div class="phone-entry mb-2">
                                    <div class="input-group">
                                        <input type="text" name="phones[]" class="form-control @error('phones.0') is-invalid @enderror" 
                                            placeholder="Entrez un numéro de téléphone" required>
                                        <button type="button" class="btn btn-danger btn-sm remove-phone" disabled>X</button>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm mt-2">Enregistrer les numéros</button>
                        </form>
                    </div>

                    <!-- Import de fichier -->
                    <div class="tab-pane fade" id="file-import">
                        <form method="POST" action="{{ route('prospects.import') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Fichier Excel/CSV</label>
                                <input type="file" name="phone_file" class="form-control" accept=".csv,.xlsx,.xls" required>
                                <div class="form-text">
                                    Format accepté : Excel ou CSV avec une colonne "telephone"<br>
                                    <a href="{{ route('prospects.template.download') }}" class="text-primary">
                                        <i class="fas fa-download me-1"></i>Télécharger le modèle
                                    </a>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-upload me-2"></i>Importer
                            </button>
                        </form>
                    </div>

                    <!-- Copier-coller en masse -->
                    <div class="tab-pane fade" id="bulk-paste">
                        <form method="POST" action="{{ route('prospects.store-bulk') }}" id="bulkForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Collez vos numéros (un par ligne)</label>
                                <textarea name="phone_numbers" rows="10" class="form-control" 
                                    placeholder="Exemple:
774567890
775678901
776789012" required></textarea>
                                <div class="form-text">
                                    Collez vos numéros de téléphone, un par ligne
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-save me-2"></i>Enregistrer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('prospects.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">Prénom</label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" class="form-control @error('first_name') is-invalid @enderror">
                            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Nom</label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" class="form-control @error('last_name') is-invalid @enderror">
                            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone_secondary" class="form-label">Téléphone secondaire</label>
                        <input type="text" name="phone_secondary" id="phone_secondary" value="{{ old('phone_secondary') }}" class="form-control @error('phone_secondary') is-invalid @enderror">
                        @error('phone_secondary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Adresse</label>
                        <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address') }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="representative_name" class="form-label">Nom du représentant</label>
                            <input type="text" name="representative_name" id="representative_name" value="{{ old('representative_name') }}" class="form-control @error('representative_name') is-invalid @enderror">
                            @error('representative_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="representative_phone" class="form-label">Téléphone du représentant</label>
                            <input type="text" name="representative_phone" id="representative_phone" value="{{ old('representative_phone') }}" class="form-control @error('representative_phone') is-invalid @enderror">
                            @error('representative_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="representative_address" class="form-label">Adresse du représentant</label>
                        <textarea name="representative_address" id="representative_address" class="form-control @error('representative_address') is-invalid @enderror" rows="2">{{ old('representative_address') }}</textarea>
                        @error('representative_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="interested_site_id" class="form-label">Site d'intérêt</label>
                        <select name="interested_site_id" id="interested_site_id" class="form-select @error('interested_site_id') is-invalid @enderror">
                            <option value="">-- Sélectionner un site --</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}" {{ old('interested_site_id') == $site->id ? 'selected' : '' }}>{{ $site->name }}</option>
                            @endforeach
                        </select>
                        @error('interested_site_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- 💰 Budget : bloc plus lisible --}}
                    <div class="mb-4 p-3 border rounded bg-light">
                        <h6 class="text-primary mb-3">Budget estimé</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="budget_min" class="form-label">Budget minimum (F CFA)</label>
                                <input type="number" name="budget_min" id="budget_min" value="{{ old('budget_min') }}" placeholder="Ex : 1 000 000" class="form-control @error('budget_min') is-invalid @enderror">
                                @error('budget_min')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="budget_max" class="form-label">Budget maximum (F CFA)</label>
                                <input type="number" name="budget_max" id="budget_max" value="{{ old('budget_max') }}" placeholder="Ex : 5 000 000" class="form-control @error('budget_max') is-invalid @enderror">
                                @error('budget_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- 📎 Import pièce d’identité --}}
                    <div class="mb-3">
                        <label for="id_document" class="form-label">Pièce d’identité (scannée ou prise en photo)</label>
                        <input type="file" name="id_document" id="id_document" class="form-control @error('id_document') is-invalid @enderror" accept="image/*,.pdf">
                        @error('id_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Créer le prospect</button>
                </form>
            </div>
        </div>
    </div>
     @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneFields = document.getElementById('phoneFields');
            const addButton = document.getElementById('addPhoneField');

            addButton.addEventListener('click', function() {
                const newField = document.createElement('div');
                newField.className = 'phone-entry mb-2';
                newField.innerHTML = `
                    <div class="input-group">
                        <input type="text" name="phones[]" class="form-control" 
                            placeholder="Entrez un numéro de téléphone" required>
                        <button type="button" class="btn btn-danger btn-sm remove-phone">X</button>
                    </div>
                `;
                phoneFields.appendChild(newField);

                document.querySelectorAll('.remove-phone').forEach(btn => {
                    btn.disabled = document.querySelectorAll('.phone-entry').length <= 1;
                });
            });
             phoneFields.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-phone')) {
                    e.target.closest('.phone-entry').remove();
                    document.querySelectorAll('.remove-phone').forEach(btn => {
                        btn.disabled = document.querySelectorAll('.phone-entry').length <= 1;
                    });
                }
            });
        });
    </script>
    @endpush

</x-app-layout>
