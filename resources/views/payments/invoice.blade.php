<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Facture - {{ $payment->reference_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .section { margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #000; padding: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Facture de Paiement</h1>
        <p>Référence : {{ $payment->reference_number }}</p>
        <p>Date : {{ $payment->payment_date->format('d/m/Y') }}</p>
    </div>

    <div class="section">
        <h2>Client</h2>
        <p>{{ $payment->client->full_name }}</p>
        <p>Téléphone : {{ $payment->client->phone }}</p>
        <p>Email : {{ $payment->client->email ?? 'N/A' }}</p>
    </div>

    <div class="section">
        <h2>Détails du paiement</h2>
        <table class="table">
            <tr>
                <th>Site</th>
                <td>{{ $payment->site->name }}</td>
            </tr>
            <tr>
                <th>Type</th>
                <td>{{ ucfirst($payment->type) }}</td>
            </tr>
            <tr>
                <th>Montant (FCFA)</th>
                <td>{{ number_format($payment->amount, 0, ',', ' ') }}</td>
            </tr>
            <tr>
                <th>Mode de paiement</th>
                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ $payment->description ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <p><em>Motif : "Adhésion / Frais d’ouverture de dossier"</em></p>
    </div>
</body>
</html>
