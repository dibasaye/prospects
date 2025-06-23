<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use App\Models\Site;
use App\Models\Lot;
use App\Models\Payment;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        }
        
        return view('dashboard', compact('stats', 'recentProspects', 'recentPayments'));
    }
}