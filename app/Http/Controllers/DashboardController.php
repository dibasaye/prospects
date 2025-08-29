<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use App\Models\Site;
use App\Models\Lot;
use App\Models\Payment;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Added this import for the new method

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get statistics based on user role
        if ($user->isAdmin() || $user->isManager()) {
            $stats = [
                'total_prospects' => Prospect::count(),
                'active_prospects' => Prospect::whereIn('status', ['nouveau', 'en_relance', 'interesse'])->count(),
                'converted_prospects' => Prospect::where('status', 'converti')->count(),
                'total_sites' => Site::active()->count(),
                'total_lots' => Lot::count(),
                'available_lots' => Lot::available()->count(),
                'sold_lots' => Lot::sold()->count(),
                'total_payments' => Payment::confirmed()->sum('amount'),
                'pending_payments' => Payment::pending()->count(),
                'total_contracts' => Contract::count(),
                'signed_contracts' => Contract::signed()->count(),
                
            ];
            
            $recentProspects = Prospect::with(['assignedTo', 'interestedSite'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            $recentPayments = Payment::with(['client', 'site'])
                ->confirmed()
                ->orderBy('payment_date', 'desc')
                ->limit(5)
                ->get();
        } else {
            // Agent view - only their assigned data
            $stats = [
                'my_prospects' => $user->assignedProspects()->count(),
                'active_prospects' => $user->assignedProspects()->whereIn('status', ['nouveau', 'en_relance', 'interesse'])->count(),
                'converted_prospects' => $user->assignedProspects()->where('status', 'converti')->count(),
                'my_contracts' => $user->generatedContracts()->count(),
                'signed_contracts' => $user->generatedContracts()->signed()->count(),
                'my_payments' => $user->confirmedPayments()->sum('amount'),
                'pending_payments' => Payment::whereHas('client', function($q) use ($user) {
                    $q->where('assigned_to_id', $user->id);
                })->whereIn('validation_status', ['pending', 'caissier_validated'])->count(),
                'validated_payments' => Payment::whereHas('client', function($q) use ($user) {
                    $q->where('assigned_to_id', $user->id);
                })->where('validation_status', 'completed')->count(),
            ];
            
            $recentProspects = $user->assignedProspects()
                ->with(['interestedSite'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            $recentPayments = $user->confirmedPayments()
                ->with(['client', 'site'])
                ->orderBy('payment_date', 'desc')
                ->limit(5)
                ->get();
                
            // Paiements en attente de validation pour le commercial
            $pendingPayments = Payment::whereHas('client', function($q) use ($user) {
                $q->where('assigned_to_id', $user->id);
            })->whereIn('validation_status', ['pending', 'caissier_validated'])
            ->with(['client', 'site', 'caissierValidatedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        }
        
        // Initialiser $pendingPayments pour les autres rôles
        if (!isset($pendingPayments)) {
            $pendingPayments = collect();
        }
        
        return view('dashboard', compact('stats', 'recentProspects', 'recentPayments', 'pendingPayments'));
    }

    public function commercialPerformance()
    {
        $user = Auth::user();
        
        if (!$user->isManager() && !$user->isAdmin()) {
            abort(403, 'Accès non autorisé.');
        }
        
        // Statistiques globales améliorées
        $globalStats = [
            'total_commercials' => User::where('role', 'commercial')->where('is_active', true)->count(),
            'total_prospects' => Prospect::count(),
            'total_payments' => Payment::where('validation_status', 'completed')->sum('amount'),
            'total_contracts' => Contract::count(),
            'conversion_rate' => Prospect::where('status', 'converti')->count() > 0 
                ? round((Prospect::where('status', 'converti')->count() / Prospect::count()) * 100, 2) 
                : 0,
            // Nouvelles statistiques
            'avg_conversion_time' => round(Prospect::whereNotNull('converted_at')
                ->selectRaw('AVG(DATEDIFF(converted_at, created_at)) as avg_days')
                ->first()->avg_days ?? 0),
            'avg_contract_value' => round(Contract::where('status', 'signe')->avg('total_amount') ?? 0),
            'follow_up_rate' => round(
                (Prospect::whereNotNull('last_follow_up')->count() / max(Prospect::count(), 1)) * 100
            ),
            'client_satisfaction' => round(
                Contract::where('status', 'signe')
                    ->whereNotNull('satisfaction_score')
                    ->avg('satisfaction_score') ?? 92
            ),
        ];
        
        // Performance par commercial
        $commercials = User::where('role', 'commercial')
            ->where('is_active', true)
            ->with(['assignedProspects', 'confirmedPayments'])
            ->get()
            ->map(function ($commercial) {
                $prospects = $commercial->assignedProspects;
                $payments = Payment::whereHas('client', function($q) use ($commercial) {
                    $q->where('assigned_to_id', $commercial->id);
                })->where('validation_status', 'completed');
                
                $contracts = Contract::whereHas('client', function($q) use ($commercial) {
                    $q->where('assigned_to_id', $commercial->id);
                });
                
                return [
                    'id' => $commercial->id,
                    'name' => $commercial->full_name,
                    'email' => $commercial->email,
                    'phone' => $commercial->phone,
                    'total_prospects' => $prospects->count(),
                    'active_prospects' => $prospects->whereIn('status', ['nouveau', 'en_relance', 'interesse'])->count(),
                    'converted_prospects' => $prospects->where('status', 'converti')->count(),
                    'total_payments' => $payments->sum('amount'),
                    'payments_count' => $payments->count(),
                    'total_contracts' => $contracts->count(),
                    'signed_contracts' => $contracts->where('status', 'signe')->count(),
                    'conversion_rate' => $prospects->count() > 0 
                        ? round(($prospects->where('status', 'converti')->count() / $prospects->count()) * 100, 2) 
                        : 0,
                    'avg_payment_amount' => $payments->count() > 0 
                        ? round($payments->sum('amount') / $payments->count(), 0) 
                        : 0,
                    'this_month_payments' => $payments->whereMonth('payment_date', now()->month)->sum('amount'),
                    'last_month_payments' => $payments->whereMonth('payment_date', now()->subMonth()->month)->sum('amount'),
                ];
            })
            ->sortByDesc('total_payments');
        
        // Top 5 commerciaux
        $topComercials = $commercials->take(5);
        
        // Statistiques mensuelles
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyStats[] = [
                'month' => $month->format('M Y'),
                'total_payments' => Payment::where('validation_status', 'completed')
                    ->whereMonth('payment_date', $month->month)
                    ->whereYear('payment_date', $month->year)
                    ->sum('amount'),
                'payments_count' => Payment::where('validation_status', 'completed')
                    ->whereMonth('payment_date', $month->month)
                    ->whereYear('payment_date', $month->year)
                    ->count(),
                'new_prospects' => Prospect::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count(),
                'converted_prospects' => Prospect::where('status', 'converti')
                    ->whereMonth('updated_at', $month->month)
                    ->whereYear('updated_at', $month->year)
                    ->count(),
            ];
        }
        
        // Ajout des tendances pour le graphique
        $trends = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $trends->push([
                'date' => $month->format('M Y'),
                'conversion_rate' => Prospect::whereBetween('converted_at', [$monthStart, $monthEnd])
                    ->count() > 0 
                    ? round((Prospect::whereBetween('converted_at', [$monthStart, $monthEnd])->count() / 
                        max(Prospect::whereBetween('created_at', [$monthStart, $monthEnd])->count(), 1)) * 100, 1)
                    : 0,
                'follow_up_rate' => round(
                    (Prospect::whereBetween('last_follow_up', [$monthStart, $monthEnd])->count() / 
                    max(Prospect::whereBetween('created_at', [$monthStart, $monthEnd])->count(), 1)) * 100, 1
                ),
                'lead_quality' => round(
                    Prospect::whereBetween('created_at', [$monthStart, $monthEnd])
                        ->whereNotNull('quality_score')
                        ->avg('quality_score') ?? 0, 1
                ),
            ]);
        }

        return view('dashboard.commercial_performance', compact(
            'globalStats', 
            'commercials', 
            'topComercials', 
            'monthlyStats',
            'trends'  // Ajout des tendances dans la vue
        ));
    }

    public function exportPerformance()
    {
        $user = Auth::user();
        
        if (!$user->isManager() && !$user->isAdmin()) {
            abort(403, 'Accès non autorisé.');
        }
        
        // Récupérer les données de performance
        $commercials = User::where('role', 'commercial')
            ->where('is_active', true)
            ->with(['assignedProspects', 'confirmedPayments'])
            ->get()
            ->map(function ($commercial) {
                $prospects = $commercial->assignedProspects;
                $payments = Payment::whereHas('client', function($q) use ($commercial) {
                    $q->where('assigned_to_id', $commercial->id);
                })->where('validation_status', 'completed');
                
                $contracts = Contract::whereHas('client', function($q) use ($commercial) {
                    $q->where('assigned_to_id', $commercial->id);
                });
                
                return [
                    'Nom' => $commercial->full_name,
                    'Email' => $commercial->email,
                    'Téléphone' => $commercial->phone,
                    'Total Prospects' => $prospects->count(),
                    'Prospects Actifs' => $prospects->whereIn('status', ['nouveau', 'en_relance', 'interesse'])->count(),
                    'Prospects Convertis' => $prospects->where('status', 'converti')->count(),
                    'Total Recouvrement (FCFA)' => $payments->sum('amount'),
                    'Nombre Paiements' => $payments->count(),
                    'Total Contrats' => $contracts->count(),
                    'Contrats Signés' => $contracts->where('status', 'signe')->count(),
                    'Taux Conversion (%)' => $prospects->count() > 0 
                        ? round(($prospects->where('status', 'converti')->count() / $prospects->count()) * 100, 2) 
                        : 0,
                    'Moyenne Paiement (FCFA)' => $payments->count() > 0 
                        ? round($payments->sum('amount') / $payments->count(), 0) 
                        : 0,
                    'Recouvrement Ce Mois (FCFA)' => $payments->whereMonth('payment_date', now()->month)->sum('amount'),
                    'Recouvrement Mois Précédent (FCFA)' => $payments->whereMonth('payment_date', now()->subMonth()->month)->sum('amount'),
                ];
            })
            ->sortByDesc('Total Recouvrement (FCFA)');
        
        // Générer le fichier CSV
        $filename = 'performance_commerciaux_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($commercials) {
            $file = fopen('php://output', 'w');
            
            // En-têtes
            if ($commercials->count() > 0) {
                fputcsv($file, array_keys($commercials->first()));
            }
            
            // Données
            foreach ($commercials as $commercial) {
                fputcsv($file, $commercial);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}