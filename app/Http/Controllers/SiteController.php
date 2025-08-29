<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Lot;
use App\Models\Prospect; // Assurez-vous que le modèle Prospect est correctement importé
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        // 'payment_plan' => 'required|in:12_months,24_months,36_months',
        'total_lots' => 'required|integer|min:0',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
        // 'launch_date' => 'nullable|date',
        'image_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',

        // Plans de paiement
        'price_12_months' => 'nullable|numeric',
        'price_24_months' => 'nullable|numeric',
        'price_cash' => 'nullable|numeric',
        'price_36_months' => 'nullable|numeric',
    ]);

    // Enregistrer le plan de lotissement si présent
    if ($request->hasFile('image_file')) {
        $path = $request->file('image_file')->store('sites', 'public');
        $validated['image_url'] = $path;
    }

    // ✅ Gérer les cases cochées
    $validated['enable_12'] = $request->has('enable_12');
    $validated['enable_24'] = $request->has('enable_24');
    $validated['enable_cash'] = $request->has('enable_cash');
    $validated['enable_36'] = $request->has('enable_36');

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

    // ✅ Déterminer si le fichier est un PDF
    $isPdf = $site->image_url ? Str::endsWith($site->image_url, '.pdf') : false;

    return view('sites.show', compact('site', 'stats', 'isPdf'));
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
            'image_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            // Plans de paiement
            'price_12_months' => 'nullable|numeric',
            'price_24_months' => 'nullable|numeric',
            'price_cash' => 'nullable|numeric',
            'price_36_months' => 'nullable|numeric',

        ]);
        
        if ($request->has('amenities')) {
            $validated['amenities'] = $request->amenities;
        }
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('sites', 'public');
            $validated['image_url'] = $path;
        }
        // ✅ Gérer les cases cochées
        $validated['enable_12'] = $request->has('enable_12');
        $validated['enable_24'] = $request->has('enable_24');
        $validated['enable_cash'] = $request->has('enable_cash');
        $validated['enable_36'] = $request->has('enable_36');
        
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
    // 🔁 Charger les réservations + prospects liés à chaque lot
    $lots = $site->lots()
        ->with(['reservation.prospect', 'contract.client']) // ← important
        ->orderBy('lot_number')
        ->paginate(20);

    $statusColors = [
        'available' => '#28a745',      // vert
        'temp_reserved' => '#ffc107',  // jaune/orange clair
        'reserved' => '#fd7e14',       // orange foncé
        'sold' => '#dc3545',           // rouge
    ];

    $statusLabels = [
        'available' => 'Disponible',
        'temp_reserved' => 'Réservation temporaire',
        'reserved' => 'Réservé',
        'sold' => 'Vendu',
    ];

    $lots->getCollection()->transform(function ($lot) use ($statusColors, $statusLabels) {
        $lot->status_color = $statusColors[$lot->status] ?? '#6c757d'; // gris par défaut
        $lot->status_label = $statusLabels[$lot->status] ?? ucfirst($lot->status);
        return $lot;
    });

    $prospects = Prospect::orderBy('last_name')->get();

    return view('sites.lots', compact('site', 'lots', 'prospects'));
}


}