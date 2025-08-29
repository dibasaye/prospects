<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Prospect;
use App\Http\Controllers\CommercialPerformanceController;

// Get prospects for the logged in user
Route::middleware('auth')->get('/my-prospects', function (Request $request) {
    // Récupère les prospects, tu peux filtrer par utilisateur si besoin (exemple : assigned_to_id = utilisateur connecté)
    $prospects = Prospect::select('id', 'first_name', 'last_name', 'phone')->get();

    // Ajout de full_name dans chaque prospect avant retour JSON
    $prospects->transform(function ($prospect) {
        $prospect->full_name = $prospect->full_name; // appel de l'accessor
        return $prospect;
    });

    return response()->json($prospects);
});

// Get commercial details
Route::middleware('auth')->get('/commercial-details', [CommercialPerformanceController::class, 'getCommercialDetails']);

// Get payments list
Route::middleware('auth')->get('/payments-list', [CommercialPerformanceController::class, 'getPaymentsList']);

// Get prospects list
Route::middleware('auth')->get('/prospects-list', [CommercialPerformanceController::class, 'getProspectsList']);

// Get commercials list
Route::middleware('auth')->get('/commercials-list', [CommercialPerformanceController::class, 'getCommercialsList']);
