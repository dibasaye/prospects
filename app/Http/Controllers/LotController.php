<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Site;
use App\Models\Prospect;
use Illuminate\Http\Request;



class LotController extends Controller
{
    public function index(Site $site)
{
    $lots = $site->lots()
        ->with(['reservation.client', 'contract.client', 'site']) // 👈 relations nécessaires
        ->orderBy('lot_number')
        ->paginate(20);

    $prospects = Prospect::where('assigned_to_id', auth()->id())->get();

    return view('sites.lots', compact('site', 'lots', 'prospects'));
}

    public function create(Site $site)
    {
        // Formulaire pour créer un nouveau lot pour ce site
        return view('lots.create', compact('site'));
    }

   public function store(Request $request, Site $site)
{
    $validated = $request->validate([
        'lot_number' => 'required|string|max:50',
        'area' => 'required|numeric|min:0',
        'position' => 'required|in:angle,facade,interieur',
        'status' => 'required|in:disponible,reserve_temporaire,reserve,vendu',
        'base_price' => 'required|numeric|min:0',
        'description' => 'nullable|string',
        // ajoute d'autres validations si besoin
    ]);

    // Calcul du supplément de position
    $position_supplement = 0;
    if (in_array($validated['position'], ['facade', 'angle'])) {
        $position_supplement = $validated['base_price'] * 0.10; // +10%
    }

    $final_price = $validated['base_price'] + $position_supplement;

    $validated['site_id'] = $site->id;
    $validated['position_supplement'] = $position_supplement;
    $validated['final_price'] = $final_price;

    Lot::create($validated);

    return redirect()->route('sites.lots', $site)->with('success', 'Lot créé avec succès.');
}

public function release(Request $request, $siteId, $lotId)
{
    $lot = Lot::where('site_id', $siteId)->findOrFail($lotId);

    // On vérifie que le lot est bien réservé avant de le libérer
    if ($lot->status === 'reserve') {
        $lot->status = 'disponible';
        
        $lot->reserved_until = null;
        $lot->save();

        return redirect()
            ->back()
            ->with('success', 'Le lot a été libéré avec succès.');
    }

    return redirect()
        ->back()
        ->with('warning', 'Ce lot n\'est pas actuellement réservé.');
}

public function reserve(Request $request, Site $site, Lot $lot)
{
    if ($lot->site_id !== $site->id) {
        abort(404, 'Ce lot ne correspond pas au site.');
    }

    if ($lot->status !== 'disponible') {
        return redirect()->back()->with('error', 'Ce lot n’est pas disponible.');
    }

    // Validation du prospect sélectionné dans le formulaire
    $request->validate([
        'client_id' => 'required|exists:prospects,id',
    ]);

    $clientId = $request->input('client_id');

    // Vérifier si ce client a déjà une réservation active (statut reserve ou reserve_temporaire)
    $existingReservation = Lot::where('client_id', $clientId)
        ->whereIn('status', ['reserve'])
        ->first();

    if ($existingReservation) {
        return redirect()->back()->with('error', 'Ce client a déjà une réservation active.');
    }

    // Attribuer le client au lot et changer son statut
    $lot->client_id = $clientId;
    $lot->status = 'reserve';
    $lot->reserved_until = now()->addHours(48); // Optionnel : expiration de la réservation
    $lot->save();

    return redirect()->route('sites.lots', $site)->with('success', 'Lot réservé avec succès.');
}

    /**
     * Réserver un lot par numéro - crée le lot s'il n'existe pas
     */
    public function reserveByNumber(Request $request, Site $site)
    {
        $request->validate([
            'lot_number' => 'required|string|max:50',
            'client_id' => 'required|exists:prospects,id',
            'area' => 'required|numeric|min:0',
            'base_price' => 'required|numeric|min:0',
            'position' => 'required|in:angle,facade,interieur',
            'description' => 'nullable|string',
        ]);

        $lotNumber = $request->input('lot_number');
        $clientId = $request->input('client_id');

        // Vérifier si le lot existe déjà
        $existingLot = $site->lots()->where('lot_number', $lotNumber)->first();

        if ($existingLot) {
            if ($existingLot->status !== 'disponible') {
                return redirect()->back()->with('error', "Le lot {$lotNumber} existe déjà mais n'est pas disponible (statut: {$existingLot->status}).");
            }

            // Vérifier si ce client a déjà une réservation active
            $existingReservation = Lot::where('client_id', $clientId)
                ->whereIn('status', ['reserve'])
                ->first();

            if ($existingReservation) {
                return redirect()->back()->with('error', 'Ce client a déjà une réservation active.');
            }

            // Réserver le lot existant
            $existingLot->client_id = $clientId;
            $existingLot->status = 'reserve';
            $existingLot->reserved_until = now()->addHours(48);
            $existingLot->save();

            return redirect()->route('sites.lots', $site)->with('success', "Le lot {$lotNumber} a été réservé avec succès.");
        }

        // Vérifier si ce client a déjà une réservation active
        $existingReservation = Lot::where('client_id', $clientId)
            ->whereIn('status', ['reserve'])
            ->first();

        if ($existingReservation) {
            return redirect()->back()->with('error', 'Ce client a déjà une réservation active.');
        }

        // Calcul du supplément de position
        $position_supplement = 0;
        if (in_array($request->input('position'), ['facade', 'angle'])) {
            $position_supplement = $request->input('base_price') * 0.10; // +10%
        }

        $final_price = $request->input('base_price') + $position_supplement;

        // Créer le nouveau lot
        $lot = Lot::create([
            'site_id' => $site->id,
            'lot_number' => $lotNumber,
            'area' => $request->input('area'),
            'position' => $request->input('position'),
            'status' => 'reserve',
            'base_price' => $request->input('base_price'),
            'position_supplement' => $position_supplement,
            'final_price' => $final_price,
            'description' => $request->input('description'),
            'client_id' => $clientId,
            'reserved_until' => now()->addHours(48),
        ]);

        return redirect()->route('sites.lots', $site)->with('success', "Le lot {$lotNumber} a été créé et réservé avec succès.");
    }
}