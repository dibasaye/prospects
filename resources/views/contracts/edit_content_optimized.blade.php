@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Modifier le contenu du contrat #{{ $contract->contract_number }}
                    </h4>
                    <small class="text-white-50">Client: {{ $contract->client->full_name }}</small>
                </div>
                
                <div class="card-body">
                    @if(!$contract->canEditContent())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Ce contrat ne peut plus être modifié car il a le statut : <strong>{{ $contract->status }}</strong>
                        </div>
                    @endif

                    <form id="contractContentForm" action="{{ route('contracts.update-content', $contract) }}" method="POST" {{ !$contract->canEditContent() ? 'style=pointer-events:none;opacity:0.6;' : '' }}>
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="content" class="form-label fw-bold">Contenu du contrat</label>
                            <textarea 
                                name="content" 
                                id="content" 
                                class="form-control" 
                                rows="20"
                                {{ !$contract->canEditContent() ? 'readonly' : '' }}
                                placeholder="Saisissez le contenu du contrat...">{{ old('content', $contract->content) }}</textarea>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Modifiez le contenu du contrat ci-dessus. Le contenu sera automatiquement sauvegardé.
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex gap-2">
                                <a href="{{ route('contracts.preview', $contract) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Retour à la prévisualisation
                                </a>
                                <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-info">
                                    <i class="fas fa-eye me-1"></i> Voir le contrat
                                </a>
                            </div>
                            
                            <div class="d-flex align-items-center gap-3">
                                <div id="saveStatus" class="text-muted" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i> Enregistrement...
                                </div>
                                <div id="saveSuccess" class="text-success" style="display: none;">
                                    <i class="fas fa-check-circle"></i> Enregistré avec succès
                                </div>
                                <div id="saveError" class="text-danger" style="display: none;">
                                    <i class="fas fa-exclamation-circle"></i> Erreur de sauvegarde
                                </div>
                                
                                @if($contract->canEditContent())
                                    <button type="submit" class="btn btn-primary" id="saveButton">
                                        <i class="fas fa-save me-1"></i> Enregistrer
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>

                    @if($contract->canEditContent())
                        <div class="mt-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="fas fa-download me-1"></i> Aperçu et Export</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-grid gap-2">
                                                <a href="{{ route('contracts.export.pdf', $contract) }}" class="btn btn-outline-danger" target="_blank">
                                                    <i class="fas fa-file-pdf me-1"></i> Aperçu PDF
                                                </a>
                                                <a href="{{ route('contracts.export.word', $contract) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-file-word me-1"></i> Télécharger Word
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0"><i class="fas fa-info-circle me-1"></i> Informations</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-unstyled mb-0">
                                                <li><strong>Statut:</strong> {{ $contract->status }}</li>
                                                <li><strong>Créé le:</strong> {{ $contract->created_at->format('d/m/Y H:i') }}</li>
                                                <li><strong>Modifié le:</strong> {{ $contract->updated_at->format('d/m/Y H:i') }}</li>
                                                <li><strong>Contenu:</strong> {{ $contract->content ? 'Personnalisé' : 'Par défaut' }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor {
        min-height: 400px;
        font-family: 'Times New Roman', serif;
        font-size: 14px;
        line-height: 1.6;
    }
    
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .card {
        border: none;
        border-radius: 10px;
    }
    
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    
    .btn {
        border-radius: 6px;
    }
    
    #saveStatus, #saveSuccess, #saveError {
        transition: all 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration AJAX pour Laravel
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    const form = document.getElementById('contractContentForm');
    const textarea = document.getElementById('content');
    const saveButton = document.getElementById('saveButton');
    const saveStatus = document.getElementById('saveStatus');
    const saveSuccess = document.getElementById('saveSuccess');
    const saveError = document.getElementById('saveError');
    
    let saveTimeout;
    let lastContent = textarea.value;
    
    // Fonction pour afficher les messages de statut
    function showStatus(type) {
        [saveStatus, saveSuccess, saveError].forEach(el => el.style.display = 'none');
        
        if (type === 'saving') {
            saveStatus.style.display = 'inline-block';
        } else if (type === 'success') {
            saveSuccess.style.display = 'inline-block';
            setTimeout(() => saveSuccess.style.display = 'none', 3000);
        } else if (type === 'error') {
            saveError.style.display = 'inline-block';
            setTimeout(() => saveError.style.display = 'none', 5000);
        }
    }
    
    // Sauvegarde automatique
    function autoSave() {
        const currentContent = textarea.value;
        
        if (currentContent === lastContent) {
            return;
        }
        
        showStatus('saving');
        
        $.ajax({
            url: form.action,
            method: 'PUT',
            data: {
                content: currentContent,
                _token: $('input[name="_token"]').val()
            },
            success: function(response) {
                if (response.success) {
                    lastContent = currentContent;
                    showStatus('success');
                    console.log('Contenu sauvegardé automatiquement');
                } else {
                    showStatus('error');
                    console.error('Erreur de sauvegarde:', response.message);
                }
            },
            error: function(xhr, status, error) {
                showStatus('error');
                console.error('Erreur AJAX:', error);
            }
        });
    }
    
    // Écouter les changements avec debounce
    if (textarea && {{ $contract->canEditContent() ? 'true' : 'false' }}) {
        textarea.addEventListener('input', function() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(autoSave, 2000); // Sauvegarde après 2 secondes d'inactivité
        });
    }
    
    // Soumission manuelle du formulaire
    if (form && {{ $contract->canEditContent() ? 'true' : 'false' }}) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            autoSave();
        });
    }
    
    // Initialiser Quill si disponible
    if (typeof Quill !== 'undefined' && {{ $contract->canEditContent() ? 'true' : 'false' }}) {
        const quill = new Quill('#content', {
            theme: 'snow',
            placeholder: 'Saisissez le contenu du contrat...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    ['link'],
                    ['clean']
                ]
            }
        });
        
        // Synchroniser avec le textarea
        quill.on('text-change', function() {
            textarea.value = quill.root.innerHTML;
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(autoSave, 2000);
        });
    }
});
</script>
@endpush
@endsection