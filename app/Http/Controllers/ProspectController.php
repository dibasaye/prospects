<?php

namespace App\Http\Controllers;

use App\Models\Prospect;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProspectController extends Controller
{
    public function index(Request $request)
    {
        $query = Prospect::with(['assignedTo', 'interestedSite']);
        
        // Filter by assigned user for agents
        if (Auth::user()->isAgent()) {
            $query->where('assigned_to_id', Auth::id());
        }
        
        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('assigned_to') && $request->assigned_to) {
            $query->where('assigned_to_id', $request->assigned_to);
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $prospects = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get filter options
        $agents = User::byRole('commercial')->active()->get();
        $sites = Site::active()->get();
        
        return view('prospects.index', compact('prospects', 'agents', 'sites'));
    }
    
    public function create()
    {
        $sites = Site::active()->get();
        $agents = User::byRole('commercial')->active()->get();
        
        return view('prospects.create', compact('sites', 'agents'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'phone_secondary' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'representative_name' => 'nullable|string|max:255',
            'representative_phone' => 'nullable|string|max:255',
            'representative_address' => 'nullable|string',
            'assigned_to_id' => 'nullable|exists:users,id',
            'interested_site_id' => 'nullable|exists:sites,id',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        $validated['status'] = 'nouveau';
        $validated['contact_date'] = now();
        
        Prospect::create($validated);
        
        return redirect()->route('prospects.index')->with('success', 'Prospect créé avec succès.');
    }
    
    public function show(Prospect $prospect)
    {
        $prospect->load(['assignedTo', 'interestedSite', 'payments', 'contracts', 'lots']);
        
        return view('prospects.show', compact('prospect'));
    }
    
    public function edit(Prospect $prospect)
    {
        $sites = Site::active()->get();
        $agents = User::byRole('commercial')->active()->get();
        
        return view('prospects.edit', compact('prospect', 'sites', 'agents'));
    }
    
    public function update(Request $request, Prospect $prospect)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'phone_secondary' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'representative_name' => 'nullable|string|max:255',
            'representative_phone' => 'nullable|string|max:255',
            'representative_address' => 'nullable|string',
            'status' => 'required|in:nouveau,en_relance,interesse,converti,abandonne',
            'assigned_to_id' => 'nullable|exists:users,id',
            'interested_site_id' => 'nullable|exists:sites,id',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'next_follow_up' => 'nullable|date',
        ]);
        
        $prospect->update($validated);
        
        return redirect()->route('prospects.show', $prospect)->with('success', 'Prospect mis à jour avec succès.');
    }
    
    public function destroy(Prospect $prospect)
    {
        $prospect->delete();
        
        return redirect()->route('prospects.index')->with('success', 'Prospect supprimé avec succès.');
    }
}