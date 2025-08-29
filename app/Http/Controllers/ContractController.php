<?php

namespace App\Http\Controllers;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;

use Illuminate\Http\Request;
use App\Models\Prospect;
use App\Models\Contract;
use App\Models\Reservation;
use App\Models\Lot;
use App\Models\PaymentSchedule;
use App\Services\ContractService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ContractController extends Controller
{
    protected ContractService $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
    }
    public function index(Request $request)
    {
        $contracts = Contract::with(['client', 'lot'])
            ->when($request->client, function ($query, $client) {
                $query->whereHas('client', fn($q) => $q->where('full_name', 'like', "%$client%"));
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('contracts.index', compact('contracts'));
    }

    public function create(Prospect $prospect)
    {
        $lot = optional($prospect->reservation)->lot;

        if (!$lot) {
            return redirect()->back()->with('error', 'Ce prospect n'a pas de lot réservé.');
        }

        return view('contracts.create', compact('prospect', 'lot'));
    }

    public function show(Contract $contract)
    {
        $contract->load(['client', 'lot', 'site', 'paymentSchedules']);

        return view('contracts.show', compact('contract'));
    }

    public function generateFromReservation(Prospect $prospect)
    {
        try {
            $contract = $this->contractService->createFromReservation($prospect);
            
            return redirect()->route('contracts.show', $contract)
                ->with('success', 'Contrat généré avec succès. Le prospect a été marqué comme converti.');
        } catch (\Exception $e) {
            Log::error('Erreur génération contrat', [
                'prospect_id' => $prospect->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', $e->getMessage());
        }
    }

    public function preview(Contract $contract)
    {
        $contract->load([
            'client',
            'site',
            'lot',
            'payments' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'paymentSchedules' => function($query) {
                $query->orderBy('due_date', 'asc');
            }
        ]);
        
        // Calculer les totaux
        $totalPaid = $contract->payments->sum('amount');
        $totalDue = $contract->total_amount - $totalPaid;
        
        return view('contracts.preview', [
            'contract' => $contract,
            'totalPaid' => $totalPaid,
            'totalDue' => $totalDue
        ]);
    }
    
    /**
     * Afficher le formulaire d'édition du contenu du contrat
     */
    public function editContent(Contract $contract)
    {
        $this->authorize('update', $contract);
        
        return view('contracts.edit_content', [
            'contract' => $contract
        ]);
    }
    
    /**
     * Mettre à jour le contenu du contrat - VERSION OPTIMISÉE
     */
    public function updateContent(Request $request, Contract $contract)
    {
        Log::info('Tentative de mise à jour du contrat', [
            'user_id' => auth()->id(),
            'contract_id' => $contract->id,
            'content_length' => $request->has('content') ? strlen($request->content) : 0,
        ]);
        
        // Vérification d'authentification
        if (!auth()->check()) {
            Log::warning('Tentative non autorisée de mise à jour du contenu');
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé. Veuillez vous connecter.'
            ], 403);
        }
        
        try {
            // Validation
            $validated = $request->validate([
                'content' => 'required|string',
            ]);
            
            // Nettoyage du contenu
            $cleanedContent = htmlspecialchars($validated['content'], 
                ENT_QUOTES | ENT_HTML5, 
                'UTF-8', 
                false
            );
            
            // Vérification des changements
            if ($contract->content === $cleanedContent) {
                Log::info('Aucune modification détectée');
                return response()->json([
                    'success' => true,
                    'message' => 'Aucune modification détectée. Le contenu est déjà à jour.',
                    'content' => $contract->content,
                    'updated_at' => $contract->updated_at->format('Y-m-d H:i:s')
                ]);
            }
            
            // Mise à jour
            $contract->update([
                'content' => $cleanedContent,
                'updated_at' => now()
            ]);
            
            Log::info('Contrat mis à jour avec succès', [
                'content_length' => strlen($cleanedContent)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Le contenu du contrat a été mis à jour avec succès.',
                'content' => $contract->content,
                'updated_at' => $contract->updated_at->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $ve) {
            Log::error('Erreur de validation', ['errors' => $ve->validator->errors()->all()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $ve->validator->errors()->all(),
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du contenu', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export PDF - VERSION COMPLÈTEMENT RÉÉCRITE ET OPTIMISÉE
     */
    public function exportPdf(Contract $contract)
    {
        // Configuration des performances depuis le config
        set_time_limit(config('contracts.export.max_generation_time', 300));
        ini_set('memory_limit', config('contracts.export.memory_limit', '512M'));
        
        Log::info('Génération PDF démarrée', [
            'contract_id' => $contract->id,
            'client_id' => $contract->client_id
        ]);
        
        try {
            // Précharger les relations nécessaires
            $contract->load(['client', 'site', 'lot']);
            
            // Préparer les données pour la vue
            $data = $this->preparePdfData($contract);
            
            // Génération du PDF avec configuration optimisée
            $pdf = \PDF::setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'isPhpEnabled' => false,
                'isFontSubsettingEnabled' => true,
                'dpi' => 96,
                'defaultFont' => 'dejavu sans',
                'debug' => false,
            ])->loadView('contracts.pdf_new', $data);
            
            $output = $pdf->output();
            
            Log::info('PDF généré avec succès', [
                'pdf_size' => strlen($output),
                'contract_id' => $contract->id
            ]);
            
            return response($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="contrat_' . $contract->contract_number . '.pdf"',
                'Content-Transfer-Encoding' => 'binary',
                'Content-Length' => strlen($output)
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur génération PDF', [
                'contract_id' => $contract->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    }

    /**
     * Préparer les données pour le PDF - MÉTHODE PRIVÉE OPTIMISÉE
     */
    private function preparePdfData(Contract $contract)
    {
        // Gestion des images avec fallback
        $images = $this->prepareImages();
        
        $data = [
            'contract' => $contract,
            'client' => $contract->client,
            'header_image' => $images['header'],
            'footer_image' => $images['footer'],
            'watermark_image' => $images['watermark']
        ];
        
        return $data;
    }

    /**
     * Préparer les images pour le PDF
     */
    private function prepareImages()
    {
        $imageFiles = config('contracts.images', [
            'header' => 'images/yayedia.png',
            'footer' => 'images/footer-image.png',
            'watermark' => 'images/image.png'
        ]);
        
        $images = [];
        foreach ($imageFiles as $key => $path) {
            $fullPath = public_path($path);
            if (file_exists($fullPath)) {
                $images[$key] = 'data:image/png;base64,' . base64_encode(file_get_contents($fullPath));
            } else {
                Log::warning("Image manquante: " . $path);
                $images[$key] = '';
            }
        }
        
        return $images;
    }

    /**
     * Export Word - VERSION OPTIMISÉE
     */
    public function exportWord(Contract $contract)
    {
        try {
            $contract->load(['client', 'site', 'lot']);
            
            $phpWord = new PhpWord();
            $phpWord->setDefaultFontName('Times New Roman');
            $phpWord->setDefaultFontSize(12);
            
            $section = $phpWord->addSection([
                'marginLeft' => 1134,
                'marginRight' => 1134,
                'marginTop' => 1134,
                'marginBottom' => 1134,
            ]);
            
            // Contenu personnalisé ou par défaut
            if (request()->has('content') && !empty(trim(request('content')))) {
                $content = trim(request('content'));
                
                // Sauvegarder seulement si différent
                if ($contract->content !== $content) {
                    $contract->update(['content' => $content]);
                }
                
                Html::addHtml($section, $content, false, true);
            } else {
                $this->addDefaultWordContent($section, $contract);
            }

            // Génération du fichier
            $filename = 'contrat_' . $contract->contract_number . '.docx';
            $tempFile = tempnam(sys_get_temp_dir(), 'contract_');
            
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($tempFile);
            
            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error('Erreur export Word', [
                'contract_id' => $contract->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Erreur lors de l\'export Word: ' . $e->getMessage());
        }
    }

    /**
     * Ajouter le contenu par défaut au document Word
     */
    private function addDefaultWordContent($section, $contract)
    {
        // Logo
        if (file_exists(public_path('images/yayedia.png'))) {
            $header = $section->addHeader();
            $header->addImage(public_path('images/yayedia.png'), [
                'width' => 200,
                'alignment' => 'center'
            ]);
        }
        
        // Titre
        $section->addText(
            'CONTRAT DE RÉSERVATION',
            ['bold' => true, 'size' => 16],
            ['alignment' => 'center', 'spaceAfter' => 500]
        );
        
        // Contenu du contrat
        $section->addText('Entre les soussignés :', ['bold' => true], ['spaceAfter' => 200]);
        $section->addText('La Société YAYE DIA BTP', [], ['spaceAfter' => 200]);
        $section->addText('Et M./Mme ' . $contract->client->full_name, [], ['spaceAfter' => 400]);
        
        // Articles
        $section->addText(
            'Article 1 - OBJET\n' .
            'La société YAYE DIA BTP consent à M./Mme ' . $contract->client->full_name . 
            ' une option de réservation sur le lot n°' . ($contract->lot->reference ?? 'N/A') . 
            ' situé à ' . ($contract->site->name ?? 'N/A') . '.',
            [],
            ['spaceAfter' => 400]
        );
        
        // Signature
        $section->addText(
            'Fait à Dakar, le ' . now()->format('d/m/Y') . '\n\n' .
            'Le Client: ' . $contract->client->full_name . '\n' .
            'Pour YAYE DIA BTP: Le Gérant',
            [],
            ['spaceAfter' => 0]
        );
    }

    public function uploadSignedCopy(Request $request, Contract $contract)
    {
        $request->validate([
            'signed_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $file = $request->file('signed_file');
        $filename = 'contrat_signe_' . $contract->contract_number . '.' . $file->getClientOriginalExtension();
        
        $path = $file->storeAs('contracts/signed', $filename, 'public');

        $contract->update([
            'contract_file_url' => $path,
            'status' => 'signe',
            'signature_date' => now(),
            'signed_by_agent' => auth()->id(),
        ]);

        if (!$contract->client->isConverti()) {
            $contract->client->markAsConverti();
        }

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contrat signé uploadé avec succès.');
    }

    public function signContract(Request $request, Contract $contract)
    {
        $request->validate([
            'signature_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $contract->update([
            'status' => 'signe',
            'signature_date' => $request->signature_date,
            'signed_by_agent' => auth()->id(),
            'notes' => $request->notes,
        ]);

        $contract->client->markAsConverti();

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contrat signé avec succès.');
    }

    public function export()
    {
        $contracts = Contract::with('client')->get();

        $csvData = $contracts->map(function ($c) {
            return [
                'Numéro' => $c->contract_number,
                'Client' => $c->client->full_name,
                'Lot' => optional($c->lot)->reference,
                'Montant' => $c->total_amount,
                'Durée' => $c->payment_duration_months . ' mois',
                'Statut' => $c->status,
            ];
        });

        $filename = 'contracts_' . now()->format('Ymd_His') . '.csv';

        $handle = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=$filename");

        fputcsv($handle, array_keys($csvData->first()));
        foreach ($csvData as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);
        exit;
    }

    /**
     * Méthode pour AJAX de paiement
     */
    public function pay(Request $request, $scheduleId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $schedule = PaymentSchedule::findOrFail($scheduleId);
        $schedule->update([
            'amount' => $request->amount,
            'is_paid' => true,
            'paid_date' => now(),
        ]);

        $contract = $schedule->contract;
        $contract->paid_amount += $request->amount;
        $contract->remaining_amount = $contract->total_amount - $contract->paid_amount;
        $contract->save();

        return response()->json(['success' => true]);
    }
}