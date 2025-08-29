<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reçu de Paiement</title>
    <style>
        body { font-family: sans-serif; }
        .container { padding: 20px; }
        h1 { font-size: 18px; margin-bottom: 10px; }
        p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reçu de Paiement</h1>
        <p><strong>Contrat :</strong> {{ $schedule->contract->contract_number }}</p>
        <p><strong>Client :</strong> {{ $schedule->contract->client->full_name }}</p>
        <p><strong>Montant :</strong> {{ number_format($schedule->amount, 0, ',', ' ') }} FCFA</p>
        <p><strong>Date de paiement :</strong> {{ $schedule->paid_date->format('d/m/Y') }}</p>
        <p><strong>Lot :</strong> {{ $schedule->contract->lot->lot_number ?? '-' }}</p>
        <hr>
        <p>Merci pour votre paiement.</p>
    </div>
</body>
</html>
