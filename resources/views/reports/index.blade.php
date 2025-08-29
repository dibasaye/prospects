@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Rapports
                    </h5>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-invoice-dollar fa-3x text-primary mb-3"></i>
                                    <h5 class="card-title">Rapport des Paiements</h5>
                                    <p class="card-text">Générez des rapports détaillés sur les paiements.</p>
                                    <a href="#" class="btn btn-primary">
                                        <i class="fas fa-download me-1"></i> Exporter
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-3x text-success mb-3"></i>
                                    <h5 class="card-title">Rapport des Clients</h5>
                                    <p class="card-text">Analysez les données de vos clients.</p>
                                    <a href="#" class="btn btn-success">
                                        <i class="fas fa-download me-1"></i> Exporter
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-home fa-3x text-info mb-3"></i>
                                    <h5 class="card-title">Rapport des Biens Immobiliers</h5>
                                    <p class="card-text">Suivez l'état de votre inventaire.</p>
                                    <a href="#" class="btn btn-info text-white">
                                        <i class="fas fa-download me-1"></i> Exporter
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Générer un rapport personnalisé</h6>
                                </div>
                                <div class="card-body">
                                    <form>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Type de rapport</label>
                                                    <select class="form-select">
                                                        <option>Paiements</option>
                                                        <option>Clients</option>
                                                        <option>Biens immobiliers</option>
                                                        <option>Contrats</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Date de début</label>
                                                    <input type="date" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label class="form-label">Date de fin</label>
                                                    <input type="date" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-file-export me-1"></i> Générer le rapport
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
