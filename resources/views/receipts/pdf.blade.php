<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reçu de Paiement</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h2 { text-align: center; }
        .info { margin-top: 20px; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <h2>Reçu de Paiement</h2>

    <div class="info">
        <p><span class="label">Client :</span> {{ $schedule->contract->client->full_name }}</p>
        <p><span class="label">Contrat :</span> {{ $schedule->contract->contract_number }}</p>
        <p><span class="label">Échéance # :</span> {{ $schedule->installment_number }}</p>
        <p><span class="label">Montant payé :</span> {{ number_format($schedule->amount, 0, ',', ' ') }} FCFA</p>
        <p><span class="label">Date de paiement :</span> {{ $schedule->paid_date->format('d/m/Y') }}</p>
    </div>

    <p style="margin-top: 40px;">Merci pour votre paiement.</p>
</body>
</html>
