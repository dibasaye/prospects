<?php
namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Prospect;
use App\Models\Lot;
use App\Models\Site;
use App\Models\payement;

use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function create(Prospect $prospect, Request $request)
{
    // Récupérer tous les sites pour la sélection
    $sites = Site::all();
    
    // Déterminer le site sélectionné
    $selectedSite = $request->get('site_id');
    
    if (!$selectedSite) {
        // Si le prospect a un site d'intérêt, l'utiliser
        if ($prospect->interested_site_id) {
            $selectedSite = $prospect->interested_site_id;
        } else {
            $selectedSite = $sites->first()?->id; // Premier site par défaut
        }
    } else {
        // Si un nouveau site a été sélectionné, mettre à jour le prospect
        if ($selectedSite != $prospect->interested_site_id) {
            $prospect->update(['interested_site_id' => $selectedSite]);
            // Ajouter un message de succès pour informer l'utilisateur
            session()->flash('success', 'Site d\'intérêt mis à jour avec succès.');
        }
    }

    // IDs des lots déjà réservés (pas encore expirés)
    $reservedLotIds = Reservation::where('expires_at', '>', now())
        ->pluck('lot_id');

    // On récupère les lots disponibles du site sélectionné
    $availableLots = collect();
    if ($selectedSite) {
        $availableLots = Lot::where('site_id', $selectedSite)
            ->with('site') // Charger la relation site
        ->whereNotIn('id', $reservedLotIds)
        ->where('status', 'disponible')
        ->get();
    }

    return view('reservations.create', compact('prospect', 'availableLots', 'sites', 'selectedSite'));
}

    /**
     * Réserver un lot par numéro - crée le lot s'il n'existe pas
     */
    public function reserveByNumber(Request $request, Prospect $prospect)
    {
        $request->validate([
            'lot_number' => 'required|string|max:50',
            'site_id' => 'required|exists:sites,id',
            'area' => 'required|numeric|min:0',
            'base_price' => 'required|numeric|min:0',
            'position' => 'required|in:angle,facade,interieur',
            'description' => 'nullable|string',
        ]);

        $lotNumber = $request->input('lot_number');
        $siteId = $request->input('site_id');

        // Mettre à jour le site d'intérêt du prospect
        if ($siteId != $prospect->interested_site_id) {
            $prospect->update(['interested_site_id' => $siteId]);
        }

        // Vérifier si le lot existe déjà
        $lot = Lot::where('site_id', $siteId)
            ->where('lot_number', $lotNumber)
            ->first();

        // Si le lot n'existe pas, on le crée
        if (!$lot) {
            // Calcul du supplément de position
            $position_supplement = 0;
            if (in_array($request->input('position'), ['facade', 'angle'])) {
                $position_supplement = $request->input('base_price') * 0.10; // +10%
            }

            $final_price = $request->input('base_price') + $position_supplement;

            // Créer le nouveau lot
            $lot = Lot::create([
                'site_id' => $siteId,
                'lot_number' => $lotNumber,
                'area' => $request->input('area'),
                'position' => $request->input('position'),
                'status' => 'disponible', // Le lot est d'abord créé comme disponible
                'base_price' => $request->input('base_price'),
                'position_supplement' => $position_supplement,
                'final_price' => $final_price,
                'description' => $request->input('description'),
            ]);

            // Log de création
            \Log::info("Nouveau lot créé : {$lotNumber} dans le site {$siteId}");
        } 
        // Si le lot existe mais n'est pas disponible
        elseif ($lot->status !== 'disponible') {
            return redirect()->back()
                ->with('error', "Le lot {$lotNumber} existe déjà mais n'est pas disponible (statut: {$lot->status}).");
        }

        // Vérifier si ce prospect a déjà une réservation active
        $existingReservation = Reservation::where('prospect_id', $prospect->id)
            ->where('expires_at', '>', now())
            ->first();

        if ($existingReservation) {
            return redirect()->back()
                ->with('error', 'Ce prospect a déjà une réservation active.');
        }

        // Créer la réservation
        Reservation::create([
            'prospect_id' => $prospect->id,
            'lot_id' => $lot->id,
            'reserved_at' => now(),
            'expires_at' => now()->addDays(3),
        ]);

        // Mettre à jour le statut du lot
        $lot->update(['status' => 'reserve']);

        // Mettre à jour le statut du prospect
        $prospect->update(['status' => 'interesse']);

        return redirect()->route('prospects.show', $prospect)
            ->with('success', "Le lot {$lotNumber} a été " . (!$lot->wasRecentlyCreated ? 'réservé' : 'créé et réservé') . " avec succès.");
}

    public function store(Request $request, Prospect $prospect)
{
    $request->validate([
        'lot_id' => 'required|exists:lots,id',
    ]);

    $alreadyReserved = Reservation::where('lot_id', $request->lot_id)
        ->where('expires_at', '>', now())
        ->exists();

    if ($alreadyReserved) {
        return back()->with('error', 'Ce lot est déjà réservé.');
    }

    $hasActiveReservation = Reservation::where('prospect_id', $prospect->id)
        ->where('expires_at', '>', now())
        ->exists();

    if ($hasActiveReservation) {
        return back()->with('error', 'Ce prospect a déjà une réservation active.');
    }

    Reservation::create([
        'prospect_id' => $prospect->id,
        'lot_id' => $request->lot_id,
        'reserved_at' => now(),
        'expires_at' => now()->addDays(3), // Réservation valable 3 jours
    ]);

    // Mettre à jour le lot
    $lot = Lot::find($request->lot_id);
    $lot->update(['status' => 'reserve']);

    // Mettre à jour le statut du prospect et son site d'intérêt
    $prospect->update([
        'status' => 'interesse',
        'interested_site_id' => $lot->site_id
    ]);

    return redirect()->route('prospects.show', $prospect)->with('success', 'Lot réservé avec succès.');
}

}
