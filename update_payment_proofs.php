<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Payment;
use Illuminate\Support\Facades\Storage;

// Récupérer tous les fichiers de justificatifs
$files = Storage::disk('public')->files('payment_proofs');

if (empty($files)) {
    echo "Aucun fichier de justificatif trouvé dans le dossier de stockage.\n";
    exit(1);
}

echo "Fichiers de justificatifs trouvés :\n";
foreach ($files as $file) {
    echo "- $file\n";
}

// Mettre à jour les paiements validés par le caissier sans justificatif
foreach ($files as $file) {
    // Trouver le premier paiement validé par le caissier sans justificatif
    $payment = Payment::where('caissier_validated', true)
        ->whereNull('payment_proof_path')
        ->first();
    
    if ($payment) {
        // Mettre à jour le chemin du justificatif
        $payment->payment_proof_path = $file;
        $saved = $payment->save();
        
        if ($saved) {
            echo "Mise à jour du paiement ID {$payment->id} avec le fichier $file\n";
        } else {
            echo "Échec de la mise à jour du paiement ID {$payment->id} avec le fichier $file\n";
        }
    } else {
        echo "Aucun paiement à mettre à jour pour le fichier $file\n";
    }
}

echo "\nVérification des mises à jour...\n";

// Vérifier les mises à jour
$updatedPayments = Payment::whereIn('payment_proof_path', $files)->get();

echo "\nPaiements mis à jour avec succès : " . $updatedPayments->count() . "\n";

foreach ($updatedPayments as $payment) {
    echo "- ID: {$payment->id} - Chemin: {$payment->payment_proof_path}\n";
}

echo "\nTerminé.\n";
