<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DebugPaymentProofs extends Command
{
    protected $signature = 'debug:payment-proofs';
    protected $description = 'Déboguer les justificatifs de paiement';

    public function handle()
    {
        $this->info("Vérification des justificatifs de paiement...\n");

        // Vérifier les fichiers dans le stockage
        $this->info("Fichiers dans le dossier de stockage (storage/app/public/payment_proofs):");
        $files = Storage::disk('public')->files('payment_proofs');
        foreach ($files as $file) {
            $this->line("- " . $file);
        }

        // Vérifier les paiements avec justificatifs
        $this->info("\nPaiements avec justificatifs dans la base de données:");
        $payments = Payment::whereNotNull('payment_proof_path')->get();
        
        if ($payments->isEmpty()) {
            $this->warn("Aucun paiement avec justificatif trouvé dans la base de données.");
        } else {
            foreach ($payments as $payment) {
                $this->line(sprintf(
                    "- ID: %d - Chemin: %s - Existe: %s",
                    $payment->id,
                    $payment->payment_proof_path,
                    Storage::disk('public')->exists($payment->payment_proof_path) ? 'Oui' : 'Non'
                ));
            }
        }

        // Vérifier les paiements validés par le caissier
        $this->info("\nPaiements validés par le caissier (sans justificatif):");
        $payments = Payment::where('caissier_validated', true)
            ->whereNull('payment_proof_path')
            ->get();

        if ($payments->isEmpty()) {
            $this->info("Tous les paiements validés par le caissier ont un justificatif enregistré.");
        } else {
            foreach ($payments as $payment) {
                $this->warn(sprintf(
                    "- ID: %d - Validé le: %s - Par utilisateur ID: %d",
                    $payment->id,
                    $payment->caissier_validated_at,
                    $payment->caissier_validated_by
                ));
            }
            
            $this->warn("\nCes paiements sont marqués comme validés par le caissier mais n'ont pas de justificatif enregistré.");
        }

        // Vérifier le lien symbolique
        $this->info("\nVérification du lien symbolique de stockage:");
        $link = public_path('storage');
        if (is_link($link)) {
            $this->info("- Le lien symbolique existe: " . $link);
            $this->info("  Pointe vers: " . readlink($link));
        } else {
            $this->error("- Le lien symbolique n'existe pas. Exécutez: php artisan storage:link");
        }
        
        return 0;
    }
}
