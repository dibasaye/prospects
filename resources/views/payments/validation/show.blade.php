<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                <i class="fas fa-eye me-2"></i>Détails du Paiement
            </h2>
            <a href="{{ route('payments.validation.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informations du paiement -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 text-gray-800">
                        <i class="fas fa-credit-card me-2"></i>Informations du Paiement
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Référence</label>
                                <div class="h5">{{ $payment->reference_number }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Statut</label>
                                <div>
                                    @switch($payment->validation_status)
                                        @case('pending')
                                            <span class="badge bg-warning text-dark">En attente du caissier</span>
                                            @break
                                        @case('caissier_validated')
                                            <span class="badge bg-info">En attente du responsable</span>
                                            @break
                                        @case('responsable_validated')
                                            <span class="badge bg-primary">En attente de l'admin</span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-success">Validation terminée</span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-danger">Rejeté</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $payment->validation_status }}</span>
                                    @endswitch
                                    
                                    @if($payment->caissier_validated && $payment->caissier)
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-user-check"></i> Validé par {{ $payment->caissier->name }} le {{ $payment->caissier_validated_at->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                    
                                    @if($payment->responsable_validated && $payment->responsable)
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-user-tie"></i> Validé par {{ $payment->responsable->name }} le {{ $payment->responsable_validated_at->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                    
                                    @if($payment->admin_validated && $payment->admin)
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-user-shield"></i> Validé par {{ $payment->admin->name }} le {{ $payment->admin_validated_at->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Type de paiement</label>
                                <div>
                                    @switch($payment->type)
                                        @case('adhesion')
                                            <span class="badge bg-primary">Adhésion</span>
                                            @break
                                        @case('reservation')
                                            <span class="badge bg-warning text-dark">Réservation</span>
                                            @break
                                        @case('mensualite')
                                            <span class="badge bg-info">Mensualité</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $payment->type }}</span>
                                    @endswitch
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Méthode de paiement</label>
                                <div class="h6">{{ $payment->payment_method }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Montant déclaré</label>
                                <div class="h4 text-success">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Date de paiement</label>
                                <div class="h6">{{ $payment->payment_date->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    </div>

                    @if($payment->description)
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Description</label>
                        <div class="p-3 bg-light rounded">{{ $payment->description }}</div>
                    </div>
                    @endif

                    @if($payment->notes)
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Notes</label>
                        <div class="p-3 bg-light rounded">{{ $payment->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Informations du client -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 text-gray-800">
                        <i class="fas fa-user me-2"></i>Informations du Client
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Nom complet</label>
                                <div class="h6">{{ $payment->client->full_name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Téléphone</label>
                                <div class="h6">{{ $payment->client->phone }}</div>
                            </div>
                        </div>
                    </div>

                    @if($payment->client->email)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Email</label>
                                <div class="h6">{{ $payment->client->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Statut</label>
                                <div>
                                    @switch($payment->client->status)
                                        @case('nouveau')
                                            <span class="badge bg-primary">Nouveau</span>
                                            @break
                                        @case('en_relance')
                                            <span class="badge bg-warning text-dark">En relance</span>
                                            @break
                                        @case('interesse')
                                            <span class="badge bg-info">Intéressé</span>
                                            @break
                                        @case('converti')
                                            <span class="badge bg-success">Converti</span>
                                            @break
                                        @case('abandonne')
                                            <span class="badge bg-danger">Abandonné</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $payment->client->status }}</span>
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($payment->client->assignedTo)
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Commercial assigné</label>
                        <div class="h6">{{ $payment->client->assignedTo->full_name }}</div>
                    </div>
                    @endif
                </div>
            </div>

            @if($payment->site)
            <!-- Informations du site -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 text-gray-800">
                        <i class="fas fa-map-marker-alt me-2"></i>Informations du Site
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Site</label>
                                <div class="h6">{{ $payment->site->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Localisation</label>
                                <div class="h6">{{ $payment->site->location }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Actions de validation -->
        <div class="col-md-4">
            @if($payment->validation_status === 'pending' && auth()->user()->hasRole('caissier'))
            <!-- Validation par le caissier -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark py-3">
                    <h6 class="mb-0 text-gray-800">
                        <i class="fas fa-check-circle me-2"></i>Validation par le Caissier
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('payments.validate', $payment) }}" method="POST" enctype="multipart/form-data" class="mb-3">
                        @csrf
                        <div class="mb-3">
                            <label for="actual_amount_received" class="form-label">Montant réel reçu (FCFA)</label>
                            <input type="number" name="actual_amount_received" id="actual_amount_received" 
                                   class="form-control" required min="0" step="100" value="{{ $payment->amount }}">
                            <div class="form-text">Confirmez le montant exact reçu</div>
                        </div>
                        <div class="mb-3">
                            <label for="payment_proof" class="form-label">Justificatif de paiement</label>
                            <input type="file" name="payment_proof" id="payment_proof" 
                                   class="form-control" required
                                   accept=".jpg,.jpeg,.png,.pdf">
                            <div class="form-text">Formats acceptés : JPG, PNG, PDF (max 2 Mo)</div>
                        </div>
                        <div class="mb-3">
                            <label for="confirmation_notes" class="form-label">Notes de confirmation</label>
                            <textarea name="confirmation_notes" id="confirmation_notes" 
                                      class="form-control" rows="3" 
                                      placeholder="Observations, commentaires..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check me-1"></i>Valider le Paiement
                        </button>
                    </form>

                    <hr>

                    <form action="{{ route('payments.validation.reject', $payment) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">Raison du rejet</label>
                            <textarea name="rejection_reason" id="rejection_reason" 
                                      class="form-control" rows="3" required
                                      placeholder="Expliquez pourquoi ce paiement est rejeté..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times me-1"></i>Rejeter le Paiement
                        </button>
                    </form>
                </div>
            </div>
            @elseif($payment->validation_status === 'caissier_validated' && auth()->user()->hasRole('responsable'))
            <!-- Validation par le responsable -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="mb-0 text-gray-800">
                        <i class="fas fa-check-circle me-2"></i>Validation par le Manager
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('payments.validate', $payment) }}" method="POST" enctype="multipart/form-data" class="mb-3">
                        @csrf
                        <div class="mb-3">
                            <label for="manager_notes" class="form-label">Notes de validation</label>
                            <textarea name="manager_notes" id="manager_notes" 
                                      class="form-control" rows="3" 
                                      placeholder="Observations, commentaires..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check me-1"></i>Valider définitivement
                        </button>
                    </form>

                    <hr>

                    <form action="{{ route('payments.validation.reject', $payment) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">Raison du rejet</label>
                            <textarea name="rejection_reason" id="rejection_reason" 
                                      class="form-control" rows="3" required
                                      placeholder="Expliquez pourquoi ce paiement est rejeté..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times me-1"></i>Rejeter le Paiement
                        </button>
                    </form>
                </div>
            </div>
            @elseif($payment->validation_status === 'responsable_validated' && auth()->user()->role === 'administrateur')
            <!-- Validation par l'administrateur -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-user-shield me-2"></i>Validation par l'Administrateur
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('payments.validate', $payment) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (optionnel)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Ajoutez des notes si nécessaire">{{ old('notes') }}</textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-circle me-1"></i> Valider le paiement
                            </button>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <form action="{{ route('payments.validation.reject', $payment) }}" method="POST" class="mt-3">
                        @csrf
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">Raison du rejet</label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="2" required placeholder="Veuillez indiquer la raison du rejet"></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir rejeter ce paiement ?')">
                                <i class="fas fa-times-circle me-1"></i> Rejeter le paiement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @elseif($payment->validation_status === 'completed')
            <!-- Paiement complètement validé -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Paiement Complètement Validé
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>Ce paiement a été validé avec succès par tous les intervenants.
                    </div>

                    @if($payment->admin_validated_at)
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Validé par</label>
                        <div class="h6">{{ $payment->admin->name ?? 'Administrateur' }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Date de validation</label>
                        <div class="h6">{{ $payment->admin_validated_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif

                    @if($payment->admin_notes)
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Notes de l'administrateur</label>
                        <div class="p-3 bg-light rounded">{{ $payment->admin_notes }}</div>
                    </div>
                    @endif

                    @if($payment->payment_proof_path)
                    <div class="mt-3">
                        <a href="{{ $payment->payment_proof_url }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-file-invoice me-2"></i>Voir le justificatif
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @elseif($payment->validation_status === 'rejected')
            <!-- Paiement rejeté -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-danger text-white py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-times-circle me-2"></i>Paiement Rejeté
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Raison du rejet</label>
                        <div class="p-3 bg-light rounded">{{ $payment->notes }}</div>
                    </div>
                </div>
            </div>
            @else
            <!-- Aucune action disponible -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 text-gray-800">
                        <i class="fas fa-info-circle me-2"></i>Aucune Action Disponible
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">Ce paiement ne peut pas être validé dans son état actuel.</p>
                </div>
            </div>
            @endif

            <!-- Informations de création -->
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 text-gray-800">
                        <i class="fas fa-info-circle me-2"></i>Informations Système
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Créé le</label>
                        <div class="h6">{{ $payment->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Dernière modification</label>
                        <div class="h6">{{ $payment->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 