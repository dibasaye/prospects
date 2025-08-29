<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illware_Console_Kernel::class);

$app->boot();

use App\Models\Payment;

$payments = Payment::all();

echo "Vérification des paiements avec justificatifs...\n";

foreach ($payments as $payment) {
    if ($payment->payment_proof_path) {
        $fullPath = storage_path('app/public/' . $payment->payment_proof_path);
        echo sprintf(
            "Payment ID: %d - Path: %s - Exists: %s\n",
            $payment->id,
            $payment->payment_proof_path,
            file_exists($fullPath) ? 'Oui' : 'Non'
        );
    }
}

echo "Vérification terminée.\n";
