@extends('layouts.app')

@push('styles')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<!-- Font Awesome pour les icônes -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<style>
    #contractContent {
        min-height: 500px;
    }
    #contentEditor {
        min-height: 500px;
        font-family: 'Times New Roman', Times, serif;
        font-size: 12.2pt;
        line-height: 1.45;
    }
    .contract-header {
        margin-bottom: 20px;
    }
    .contract-footer {
        margin-top: 40px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-contract mr-2"></i>
                        Prévisualisation du contrat N°{{ $contract->contract_number }}
                    </h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-light text-warning mr-2" id="editContentBtn">
                            <i class="fas fa-edit fa-fw"></i> Modifier le contenu
                        </button>
                        <a href="{{ route('contracts.export.pdf', $contract) }}" class="btn btn-light text-danger" target="_blank" id="exportPdfBtn">
                            <i class="fas fa-file-pdf fa-fw"></i> Exporter en PDF
                        </a>
                        <a href="{{ route('contracts.export.word', $contract) }}" class="btn btn-light text-primary ml-2" id="exportWordBtn">
                            <i class="fas fa-file-word fa-fw"></i> Exporter en Word
                        </a>
                        <button onclick="window.print()" class="btn btn-light text-success ml-2">
                            <i class="fas fa-print fa-fw"></i> Imprimer
                        </button>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <!-- Informations du contrat -->
                    <div class="bg-light p-3 border-bottom">
                        <div class="row">
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Client :</strong> <span id="clientNameDisplay">{{ $contract->client->full_name }}</span></p>
                                <p class="mb-1"><strong>Site :</strong> {{ $contract->site->name }}</p>
                                <p class="mb-1"><strong>Lot :</strong> {{ $contract->lot->reference ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Montant total :</strong> {{ number_format($contract->total_amount, 0, ',', ' ') }} FCFA</p>
                                <p class="mb-1"><strong>Montant payé :</strong> {{ number_format($totalPaid, 0, ',', ' ') }} FCFA</p>
                                <p class="mb-1"><strong>Reste à payer :</strong> {{ number_format($totalDue, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <p class="mb-1"><strong>Date du contrat :</strong> <span id="contractDateDisplay">{{ now()->format('d/m/Y') }}</span></p>
                                <p class="mb-1"><strong>Statut :</strong>
                                    @if($contract->is_signed)
                                        <span class="badge badge-success">Signé</span>
                                    @else
                                        <span class="badge badge-warning">En attente de signature</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Contenu du contrat -->
                    <div id="contractContent" class="p-4">
                        @include('contracts.pdf')
                    </div>
                    
                    <!-- Éditeur de contenu (caché par défaut) -->
                    <div id="contractEditor" class="p-4 d-none">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Important :</strong> Vous ne pouvez modifier que les informations du client. Les articles du contrat (Article 1, Article 2, etc.) sont fixes et ne peuvent pas être modifiés dans cette section.
                        </div>
                        <div class="form-group">
                            <label>Informations personnalisées du client</label>
                            <textarea id="contentEditor" class="form-control" rows="20"></textarea>
                        </div>
                        <div class="text-right mt-3">
                            <button type="button" class="btn btn-secondary mr-2" id="cancelEditBtn">Annuler</button>
                            <button type="button" class="btn btn-primary" id="saveContentBtn">Enregistrer les modifications</button>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-light d-flex justify-content-between">
                    <a href="{{ route('contracts.show', $contract) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Retour au contrat
                    </a>
                    
                    <div>
                        @if(!$contract->is_signed)
                            <a href="#" class="btn btn-warning mr-2" data-toggle="modal" data-target="#editContractModal">
                                <i class="fas fa-edit mr-1"></i> Modifier
                            </a>
                            
                            <form action="{{ route('contracts.sign', $contract) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir marquer ce contrat comme signé ?')">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-signature mr-1"></i> Marquer comme signé
                                </button>
                            </form>
                        @else
                            <span class="text-success">
                                <i class="fas fa-check-circle"></i> Contrat signé le {{ $contract->signed_at->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Style de base pour la prévisualisation */
.contract-preview {
    background-color: white;
    padding: 2rem;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    font-family: 'Times New Roman', serif;
    font-size: 12pt;
    line-height: 1.6;
}

/* Style pour les pages */
.contract-preview .page {
    background: white;
    padding: 2cm;
    margin: 1rem 0;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    position: relative;
}

/* Style pour l'impression */
@media print {
    .no-print, .card-header, .card-footer, .btn {
        display: none !important;
    }
    
    body, .contract-preview, .contract-preview .page {
        background: white !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
    }
    
    .contract-preview {
        font-size: 12pt !important;
        line-height: 1.6 !important;
    }
}

/* Style pour les tableaux */
.contract-preview table {
    width: 100%;
    margin: 1rem 0;
    border-collapse: collapse;
}

.contract-preview th, 
.contract-preview td {
    border: 1px solid #dee2e6;
    padding: 0.5rem;
    text-align: left;
}

.contract-preview th {
    background-color: #f8f9fa;
    font-weight: 600;
}

/* Style pour les titres */
.contract-preview h1 { font-size: 1.8rem; }
.contract-preview h2 { font-size: 1.5rem; }
.contract-preview h3 { font-size: 1.3rem; }
.contract-preview h4 { font-size: 1.1rem; }
.contract-preview h5 { font-size: 1rem; }
.contract-preview h6 { font-size: 0.9rem; }

/* Style pour les signatures */
.signature-section {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #dee2e6;
}

.signature-line {
    border-top: 1px solid #000;
    width: 200px;
    margin: 40px 0 5px;
    display: inline-block;
}

/* Style pour les boutons d'action */
.action-buttons {
    margin: 1.5rem 0;
    padding-top: 1rem;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.5rem;
}

/* Style pour les badges */
.badge {
    font-size: 0.8rem;
    font-weight: 500;
    padding: 0.35em 0.65em;
    border-radius: 0.25rem;
}

/* Style pour les alertes */
.alert-box {
    padding: 1rem;
    margin: 1rem 0;
    border-left: 4px solid #007bff;
    background-color: #f8f9fa;
}

/* Style pour les listes */
.contract-preview ul, 
.contract-preview ol {
    padding-left: 2rem;
    margin-bottom: 1rem;
}

.contract-preview li {
    margin-bottom: 0.5rem;
}

/* Style pour les images */
.contract-preview img {
    max-width: 100%;
    height: auto;
}

/* Style pour les citations */
.contract-preview blockquote {
    border-left: 4px solid #dee2e6;
    padding: 0.5rem 1rem;
    margin: 1rem 0;
    color: #6c757d;
}

/* Style pour le code */
.contract-preview code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-family: 'Courier New', monospace;
    font-size: 0.9em;
}

/* Style pour les tableaux réactifs */
.table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
</style>
<!-- Modal d'édition du contenu -->
<div class="modal fade" id="editContractModal" tabindex="-1" role="dialog" aria-labelledby="editContractModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Modifier le contenu du contrat</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Contenu du contrat</label>
                    <div id="contractContentEditor"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="saveChangesBtn">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Inclure jQuery d'abord -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Puis les dépendances de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<!-- Ensuite Summernote -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
// Vérifier les dépendances
if (typeof jQuery === 'undefined') {
    console.error('jQuery n\'est pas chargé');
} else if (typeof $.fn.summernote !== 'function') {
    console.error('Summernote n\'est pas chargé correctement');
} else {
    $(document).ready(function() {
        // Fonction pour mettre à jour les liens d'export
        function updateExportLinks() {
            // Extraire seulement le contenu personnalisé (section client)
            var customContentElement = $('#contractContent').find('.parties');
            var customContent = '';
            
            if (customContentElement.length > 0) {
                customContent = customContentElement.parent().html() || customContentElement.html();
            }
            
            const clientName = $('#clientNameDisplay').text();
            const contractDate = $('#contractDateDisplay').text();
            
            // Mettre à jour le lien d'export PDF (seulement si il y a du contenu personnalisé)
            var pdfUrl = '{{ route("contracts.export.pdf", $contract) }}';
            if (customContent.trim() !== '') {
                pdfUrl += '?content=' + encodeURIComponent(customContent) +
                         '&client_name=' + encodeURIComponent(clientName) +
                         '&contract_date=' + encodeURIComponent(contractDate);
            }
            $('#exportPdfBtn').attr('href', pdfUrl);
            
            // Mettre à jour le lien d'export Word
            var wordUrl = '{{ route("contracts.export.word", $contract) }}';
            if (customContent.trim() !== '') {
                wordUrl += '?content=' + encodeURIComponent(customContent) +
                          '&client_name=' + encodeURIComponent(clientName) +
                          '&contract_date=' + encodeURIComponent(contractDate);
            }
            $('#exportWordBtn').attr('href', wordUrl);
        }
        
        // Initialiser l'éditeur Summernote
        function initSummernote() {
            $('#contentEditor').summernote({
                height: 500,
                minHeight: 300,
                focus: true,
                disableDragAndDrop: true,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['codeview', 'help']]
                ],
                callbacks: {
                    onInit: function() {
                        // Charger le contenu initial
                        $('#contentEditor').summernote('code', $('#contractContent').html());
                    }
                }
            });
        }
        
        // Initialiser l'éditeur
        initSummernote();
        
        // Afficher l'éditeur
        $('#editContentBtn').on('click', function() {
            $('#contractContent').addClass('d-none');
            $('#contractEditor').removeClass('d-none');
            
            // Extraire seulement la section personnalisable (contenu client)
            var customContentElement = $('#contractContent').find('.parties');
            var existingContent = '';
            
            if (customContentElement.length > 0) {
                // Si on trouve la section client, l'extraire
                existingContent = customContentElement.parent().html() || customContentElement.html();
            } else {
                // Sinon, chercher tout contenu après "Et" et avant "Article 1"
                var fullContent = $('#contractContent').html();
                var startMarker = fullContent.indexOf('<p class="center"><b>Et</b></p>');
                var endMarker = fullContent.indexOf('Article 1 : Objet du contrat');
                
                if (startMarker !== -1 && endMarker !== -1) {
                    existingContent = fullContent.substring(startMarker + '<p class="center"><b>Et</b></p>'.length, endMarker);
                } else if (startMarker !== -1) {
                    // Si on ne trouve pas "Article 1", prendre jusqu'à la fin de la première page
                    var endPage1 = fullContent.indexOf('<!-- ========================= PAGE 2');
                    if (endPage1 !== -1) {
                        existingContent = fullContent.substring(startMarker + '<p class="center"><b>Et</b></p>'.length, endPage1);
                    }
                }
            }
            
            $('#contentEditor').summernote('code', existingContent.trim());
        });

        // Annuler les modifications
        $('#cancelEditBtn').on('click', function() {
            $('#contentEditor').summernote('destroy');
            $('#contractEditor').addClass('d-none');
            $('#contractContent').removeClass('d-none');
            
            // Réinitialiser l'éditeur
            initSummernote();
        });

        // Sauvegarder les modifications
        $('#saveContentBtn').on('click', function() {
            try {
                // Récupérer le contenu modifié
                var modifiedContent = $('#contentEditor').summernote('code');
                
                // Vérifier que le contenu ne contient pas les articles fixes
                if (modifiedContent.indexOf('Article 1 : Objet du contrat') !== -1 || 
                    modifiedContent.indexOf('Article 2 : Désignation du terrain') !== -1) {
                    alert('Erreur : Le contenu ne doit contenir que les informations du client, pas les articles du contrat. Les articles sont déjà inclus dans le template.');
                    return;
                }
                
                // Mettre à jour l'affichage en remplaçant seulement la section personnalisable
                var contractContent = $('#contractContent');
                var currentHtml = contractContent.html();
                
                // Trouver et remplacer la section client
                var startMarker = currentHtml.indexOf('<!-- Affichage du contenu du contrat personnalisé');
                var endMarker = currentHtml.indexOf('<!-- ========================= PAGE 2');
                
                if (startMarker !== -1 && endMarker !== -1) {
                    var beforeCustom = currentHtml.substring(0, startMarker);
                    var afterCustom = currentHtml.substring(endMarker);
                    var newCustomSection = '<!-- Affichage du contenu du contrat personnalisé (section client) -->\n  ' + modifiedContent + '\n\n\n\n\n\n  ';
                    contractContent.html(beforeCustom + newCustomSection + afterCustom);
                } else {
                    // Fallback : remplacer tout le contenu (pas idéal mais fonctionnel)
                    contractContent.html(modifiedContent);
                }
                
                // Cacher l'éditeur et afficher le contenu
                $('#contractEditor').addClass('d-none');
                $('#contractContent').removeClass('d-none');
                
                // Envoyer les modifications au serveur via AJAX
                $.ajax({
                    url: '{{ route("contracts.update-content", $contract) }}',
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({
                        content: modifiedContent
                    }),
                    success: function(response) {
                        if (response.success) {
                            // Mettre à jour les liens d'export
                            updateExportLinks();
                            
                            // Afficher un message de succès
                            alert(response.message || 'Les modifications ont été enregistrées avec succès !');
                        } else {
                            alert(response.message || 'Une erreur est survenue lors de la sauvegarde.');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Une erreur est survenue lors de la sauvegarde des modifications.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            console.error('Erreur lors de l\'analyse de la réponse:', e);
                        }
                        console.error('Erreur lors de la sauvegarde:', xhr.status, xhr.statusText, xhr.responseText);
                        alert(errorMessage);
                    }
                });
            } catch (e) {
                console.error('Erreur lors de la sauvegarde :', e);
                alert('Une erreur est survenue lors de la sauvegarde.');
            }
        });
        
        // Initialiser les liens d'export
        updateExportLinks();
    });
}
</script>
@endpush

@endsection
