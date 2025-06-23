<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Lot;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index()
    {
        $sites = Site::with(['lots'])->orderBy('created_at', 'desc')->paginate(12);
        
        return view('sites.index', compact('sites'));
    }
    
    public function create()
    {
        return view('sites.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_area' => 'nullable|numeric|min:0',
            'base_price_per_sqm' => 'required|numeric|min:0',
            'reservation_fee' => 'required|numeric|min:0',
            'membership_fee' => 'required|numeric|min:0',
            'payment_plan' => 'required|in:12_months,24_months,36_months',
            'amenities' => 'nullable|array',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);
        
        if ($request->has('amenities')) {
            $validated['amenities'] = $request->amenities;
        }
        
        $site = Site::create($validated);
        
        return redirect()->route('sites.show', $site)->with('success', 'Site créé avec succès.');
    }
    
    public function show(Site $site)
    {
        $site->load(['lots', 'prospects', 'contracts']);
        
        $stats = [
            'total_lots' => $site->lots()->count(),
            'available_lots' => $site->availableLots()->count(),
            'reserved_lots' => $site->reservedLots()->count(),
            'sold_lots' => $site->soldLots()->count(),
            'total_prospects' => $site->prospects()->count(),
            'total_revenue' => $site->payments()->confirmed()->sum('amount'),
        ];
        
        return view('sites.show', compact('site', 'stats'));
    }
    
    public function edit(Site $site)
    {
        return view('sites.edit', compact('site'));
    }
    
    public function update(Request $request, Site $site)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_area' => 'nullable|numeric|min:0',
            'base_price_per_sqm' => 'required|numeric|min:0',
            'reservation_fee' => 'required|numeric|min:0',
            'membership_fee' => 'required|numeric|min:0',
            'payment_plan' => 'required|in:12_months,24_months,36_months',
            'amenities' => 'nullable|array',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_active' => 'boolean',
        ]);
        
        if ($request->has('amenities')) {
            $validated['amenities'] = $request->amenities;
        }
        
        $site->update($validated);
        
        return redirect()->route('sites.show', $site)->with('success', 'Site mis à jour avec succès.');
    }
    
    public function destroy(Site $site)
    {
        $site->delete();
        
        return redirect()->route('sites.index')->with('success', 'Site supprimé avec succès.');
    }
    
    public function lots(Site $site)
    {
        $lots = $site->lots()->orderBy('lot_number')->paginate(20);
        
        return view('sites.lots', compact('site', 'lots'));
    }
}