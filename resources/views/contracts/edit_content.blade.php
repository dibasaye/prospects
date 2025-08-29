@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Modifier le contenu du contrat #{{ $contract->contract_number }}
                    </h4>
                </div>
                
                <div class="card-body">
                    <form id="contractContentForm" action="{{ route('contracts.update-content', $contract) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Contenu du contrat</label>
                            <textarea name="content" id="content" class="form-control" rows="20">{{ old('content', $contract->content) }}</textarea>
                            <div class="form-text">Modifiez le contenu du contrat ci-dessus. Utilisez la barre d'outils pour formater le texte.</div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('contracts.preview', $contract) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Retour
                            </a>
                            
                            <div class="d-flex align-items-center">
                                <div id="saveStatus" class="text-muted me-3" style="display: none;">
                                    <i class="fas fa-spinner fa-spin"></i> Enregistrement en cours...
                                </div>
                                <div id="saveSuccess" class="text-success me-3" style="display: none;">
                                    <i class="fas fa-check-circle"></i> Enregistré avec succès!
                                </div>
                                <button type="submit" class="btn btn-primary" id="saveButton">
                                    <i class="fas fa-save me-1"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<!-- Font Awesome pour les icônes -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<style>
    .note-editor.note-frame {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
    .note-editor.note-frame .note-toolbar {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .note-editor.note-frame .note-statusbar {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }
</style>
@endpush

@push('scripts')
<!-- Inclure jQuery d'abord -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Puis les dépendances de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<!-- Ensuite Summernote -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<!-- SweetAlert2 pour de belles alertes -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Assurez-vous que jQuery est chargé
try {
    if (typeof jQuery == 'undefined') {
        throw new Error('jQuery n\'est pas chargé');
    }
    
    $(document).ready(function() {
        // Initialisation de Summernote
        if (typeof $.fn.summernote === 'function') {
            var $editor = $('#content');
            
            $editor.summernote({
                height: 600,
                minHeight: 300,
                focus: true,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['codeview', 'help']]
                ],
                callbacks: {
                    onInit: function() {
                        console.log('Éditeur Summernote initialisé');
                    },
                    onChange: function(contents) {
                        // Réinitialiser le message de succès lors d'une modification
                        $('#saveSuccess').fadeOut();
                    }
                }
            });
            
            // Gestion de la soumission du formulaire
            $('#contractContentForm').on('submit', function(e) {
                e.preventDefault();
                
                var $form = $(this);
                var $saveButton = $('#saveButton');
                var $saveStatus = $('#saveStatus');
                var $saveSuccess = $('#saveSuccess');
                
                // Désactiver le bouton et afficher le statut
                $saveButton.prop('disabled', true);
                $saveStatus.fadeIn();
                $saveSuccess.hide();
                
                // Récupérer le contenu HTML de l'éditeur
                var content = $editor.summernote('code');
                
                // Envoyer les données via AJAX
                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        _method: 'PUT',
                        content: content
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Réponse du serveur:', response);
                        
                        if (response.success) {
                            // Afficher le message de succès
                            $saveStatus.hide();
                            $saveSuccess.fadeIn();
                            
                            // Cacher le message de succès après 3 secondes
                            setTimeout(function() {
                                $saveSuccess.fadeOut();
                            }, 3000);
                            
                            // Mettre à jour le contenu dans l'éditeur avec la réponse du serveur si nécessaire
                            if (response.content) {
                                $editor.summernote('code', response.content);
                            }
                        } else {
                            throw new Error(response.message || 'Une erreur est survenue');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur AJAX:', status, error);
                        var errorMessage = 'Une erreur est survenue lors de l\'enregistrement';
                        
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            } else if (response.error) {
                                errorMessage = response.error;
                            }
                        } catch (e) {
                            console.error('Erreur lors de l\'analyse de la réponse:', e);
                        }
                        
                        // Afficher une alerte d'erreur
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: errorMessage,
                            confirmButtonText: 'OK'
                        });
                    },
                    complete: function() {
                        // Réactiver le bouton
                        $saveButton.prop('disabled', false);
                        $saveStatus.hide();
                    }
                });
            });
            
        } else {
            console.error('Summernote n\'est pas chargé correctement');
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'L\'éditeur de texte n\'a pas pu être chargé. Veuillez recharger la page.',
                confirmButtonText: 'OK'
            });
        }
        
        // Gestion des erreurs globales non capturées
        $(window).on('error', function(msg, url, line, col, error) {
            console.error('Erreur non capturée:', msg, url, line, col, error);
            
            // Afficher une alerte utilisateur
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur JavaScript',
                    html: 'Une erreur est survenue dans l\'application. Veuillez rafraîchir la page.<br><br>Détails: ' + (error ? error.message : 'Erreur inconnue'),
                    confirmButtonText: 'Rafraîchir',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            } else {
                alert('Une erreur est survenue. Veuillez rafraîchir la page.');
            }
            
            return false;
        });
    });
} catch (e) {
    console.error('Erreur lors de l\'initialisation de l\'éditeur :', e);
    
    // Afficher une alerte d'erreur si possible
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Une erreur est survenue lors du chargement de l\'éditeur. Veuillez recharger la page.',
            confirmButtonText: 'OK'
        });
    } else {
        alert('Une erreur est survenue lors du chargement de l\'éditeur. Veuillez recharger la page.');
    }
}
</script>
@endpush
@endsection
