<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Prospect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ContractService
{
    /**
     * Créer un contrat à partir d'une réservation
     */
    public function createFromReservation(Prospect $prospect): Contract
    {
        if (Contract::where('client_id', $prospect->id)->exists()) {
            throw new \Exception('Un contrat existe déjà pour ce client.');
        }

        $reservation = $prospect->reservation;
        if (!$reservation || !$reservation->lot) {
            throw new \Exception('Aucune réservation active avec un lot associé.');
        }

        $lot = $reservation->lot;
        $site = $lot->site;

        $total = $lot->price ?? config('contracts.default_settings.default_price');
        $duration = config('contracts.default_settings.payment_duration_months');
        $monthly = $total / $duration;

        $contract = Contract::create([
            'contract_number' => Contract::generateContractNumber(),
            'client_id' => $prospect->id,
            'site_id' => $site->id,
            'lot_id' => $lot->id,
            'total_amount' => $total,
            'paid_amount' => $prospect->payments()->sum('amount'),
            'remaining_amount' => $total - $prospect->payments()->sum('amount'),
            'payment_duration_months' => $duration,
            'monthly_payment' => $monthly,
            'start_date' => now(),
            'end_date' => now()->addMonths($duration),
            'status' => Contract::STATUS_DRAFT,
            'generated_by' => auth()->id(),
        ]);

        // Marquer le prospect comme converti
        $prospect->markAsConverti();

        // Créer les échéances de paiement
        $this->createPaymentSchedules($contract, $duration);

        Log::info('Contrat créé depuis réservation', [
            'contract_id' => $contract->id,
            'prospect_id' => $prospect->id,
            'total_amount' => $total
        ]);

        return $contract;
    }

    /**
     * Créer les échéances de paiement
     */
    private function createPaymentSchedules(Contract $contract, int $duration): void
    {
        for ($i = 1; $i <= $duration; $i++) {
            $contract->paymentSchedules()->create([
                'installment_number' => $i,
                'amount' => 0, // Sera défini lors du paiement
                'due_date' => now()->addMonths($i),
                'is_paid' => false,
            ]);
        }
    }

    /**
     * Mettre à jour le contenu du contrat
     */
    public function updateContent(Contract $contract, string $content): array
    {
        if (!$contract->canEditContent()) {
            throw new \Exception('Ce contrat ne peut plus être modifié.');
        }

        // Nettoyage du contenu
        $cleanedContent = htmlspecialchars($content, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);

        // Vérification des changements
        if ($contract->content === $cleanedContent) {
            return [
                'success' => true,
                'message' => 'Aucune modification détectée.',
                'changed' => false
            ];
        }

        // Validation de la longueur
        $maxLength = config('contracts.auto_save.max_content_length', 50000);
        if (strlen($cleanedContent) > $maxLength) {
            throw new \Exception("Le contenu ne peut pas dépasser $maxLength caractères.");
        }

        // Mise à jour
        $contract->update([
            'content' => $cleanedContent,
            'updated_at' => now()
        ]);

        Log::info('Contenu du contrat mis à jour', [
            'contract_id' => $contract->id,
            'content_length' => strlen($cleanedContent)
        ]);

        return [
            'success' => true,
            'message' => 'Contenu mis à jour avec succès.',
            'changed' => true
        ];
    }

    /**
     * Signer un contrat
     */
    public function signContract(Contract $contract, array $data): Contract
    {
        $contract->update([
            'status' => Contract::STATUS_SIGNED,
            'signature_date' => $data['signature_date'] ?? now(),
            'signed_by_agent' => auth()->id(),
            'notes' => $data['notes'] ?? null,
        ]);

        // Marquer le client comme converti
        if (!$contract->client->isConverti()) {
            $contract->client->markAsConverti();
        }

        Log::info('Contrat signé', [
            'contract_id' => $contract->id,
            'signed_by' => auth()->id()
        ]);

        return $contract;
    }

    /**
     * Uploader une copie signée
     */
    public function uploadSignedCopy(Contract $contract, $file): Contract
    {
        $filename = 'contrat_signe_' . $contract->contract_number . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('contracts/signed', $filename, 'public');

        $contract->update([
            'contract_file_url' => $path,
            'status' => Contract::STATUS_SIGNED,
            'signature_date' => now(),
            'signed_by_agent' => auth()->id(),
        ]);

        // Marquer le client comme converti
        if (!$contract->client->isConverti()) {
            $contract->client->markAsConverti();
        }

        Log::info('Copie signée uploadée', [
            'contract_id' => $contract->id,
            'file_path' => $path
        ]);

        return $contract;
    }

    /**
     * Préparer les données pour l'export PDF
     */
    public function preparePdfData(Contract $contract): array
    {
        // Précharger les relations
        $contract->load(['client', 'site', 'lot']);

        // Préparer les images
        $images = $this->prepareImages();

        // Données de base
        $data = [
            'contract' => $contract,
            'client' => $contract->client,
            'header_image' => $images['header'],
            'footer_image' => $images['footer'],
            'watermark_image' => $images['watermark'],
            'company_info' => config('contracts.company_info'),
            'project_info' => config('contracts.project_info'),
        ];

        return $data;
    }

    /**
     * Préparer les images en base64 pour le PDF
     */
    private function prepareImages(): array
    {
        $imageFiles = config('contracts.images');
        $images = [];

        foreach ($imageFiles as $key => $path) {
            $fullPath = public_path($path);
            if (file_exists($fullPath)) {
                $images[$key] = 'data:image/png;base64,' . base64_encode(file_get_contents($fullPath));
            } else {
                Log::warning("Image manquante pour l'export PDF", ['path' => $path]);
                $images[$key] = '';
            }
        }

        return $images;
    }

    /**
     * Obtenir les statistiques des contrats
     */
    public function getContractStats(): array
    {
        return [
            'total' => Contract::count(),
            'draft' => Contract::where('status', Contract::STATUS_DRAFT)->count(),
            'signed' => Contract::where('status', Contract::STATUS_SIGNED)->count(),
            'cancelled' => Contract::where('status', Contract::STATUS_CANCELLED)->count(),
            'completed' => Contract::where('status', Contract::STATUS_COMPLETED)->count(),
            'current_month' => Contract::currentMonth()->count(),
            'total_amount' => Contract::sum('total_amount'),
            'paid_amount' => Contract::sum('paid_amount'),
        ];
    }

    /**
     * Calculer les métriques de performance
     */
    public function getPerformanceMetrics(): array
    {
        $totalContracts = Contract::count();
        $signedContracts = Contract::where('status', Contract::STATUS_SIGNED)->count();
        $averageValue = Contract::avg('total_amount');
        $conversionRate = $totalContracts > 0 ? ($signedContracts / $totalContracts) * 100 : 0;

        return [
            'total_contracts' => $totalContracts,
            'signed_contracts' => $signedContracts,
            'conversion_rate' => round($conversionRate, 2),
            'average_contract_value' => round($averageValue, 2),
            'monthly_performance' => $this->getMonthlyPerformance(),
        ];
    }

    /**
     * Obtenir les performances mensuelles
     */
    private function getMonthlyPerformance(): array
    {
        return Contract::selectRaw('
            YEAR(created_at) as year,
            MONTH(created_at) as month,
            COUNT(*) as total,
            SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as signed,
            SUM(total_amount) as total_amount
        ', [Contract::STATUS_SIGNED])
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->limit(12)
        ->get()
        ->toArray();
    }

    /**
     * Nettoyer les anciens fichiers temporaires
     */
    public function cleanupTempFiles(): int
    {
        $tempDir = sys_get_temp_dir();
        $prefix = config('contracts.export.temp_file_prefix', 'contract_');
        $cleaned = 0;

        $files = glob($tempDir . '/' . $prefix . '*');
        foreach ($files as $file) {
            // Supprimer les fichiers de plus de 1 heure
            if (filemtime($file) < time() - 3600) {
                if (unlink($file)) {
                    $cleaned++;
                }
            }
        }

        if ($cleaned > 0) {
            Log::info("Nettoyage des fichiers temporaires", ['files_cleaned' => $cleaned]);
        }

        return $cleaned;
    }
}