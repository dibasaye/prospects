<x-app-layout>
    <x-slot name="header">
        <h2 class="h5">Contrat #{{ $contract->contract_number }}</h2>
    </x-slot>

    <div class="container py-4">
        <!-- Infos contrat -->
        <div class="card mb-4">
            <div class="card-body">
                <h5>Client : {{ $contract->client->full_name }}</h5>
                <p><strong>Site :</strong> {{ $contract->site->name }}</p>
                <p><strong>Lot :</strong> {{ $contract->lot->lot_number ?? '-' }}</p>
                <p><strong>Montant total :</strong> {{ number_format($contract->total_amount, 0, ',', ' ') }} FCFA</p>
                <p><strong>Montant payé :</strong> {{ number_format($contract->paid_amount, 0, ',', ' ') }} FCFA</p>
                <p><strong>Reste à payer :</strong> {{ number_format($contract->remaining_amount, 0, ',', ' ') }} FCFA</p>
                <p><strong>Durée :</strong> {{ $contract->payment_duration_months }} mois</p>
                <p><strong>Début :</strong> {{ $contract->start_date->format('d/m/Y') }}</p>
                <p><strong>Fin :</strong> {{ $contract->end_date->format('d/m/Y') }}</p>
                <p><strong>Statut :</strong> 
                    <span class="badge bg-secondary text-capitalize">
                        {{ $contract->status }}
                    </span>
                </p>
            </div>
        </div>

        <!-- Téléchargement -->
        <div class="mb-4 d-flex gap-2">
            <a href="{{ route('contracts.export.pdf', $contract) }}" class="btn btn-outline-primary">
                <i class="fas fa-file-pdf"></i> Télécharger en PDF
            </a>
            <a href="{{ route('contracts.export.word', $contract) }}" class="btn btn-outline-success">
                <i class="fas fa-file-word"></i> Télécharger en Word
            </a>
        </div>

        <!-- Import contrat signé -->
        @if ($contract->status !== 'signe')
            <div class="card">
                <div class="card-header">Importer contrat signé (PDF)</div>
                <div class="card-body">
                    <form action="{{ route('contracts.upload-signed', $contract) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="signed_file" class="form-label">Fichier PDF signé :</label>
                            <input type="file" name="signed_file" class="form-control" accept="application/pdf" required>
                        </div>
                        <button class="btn btn-success">📎 Importer</button>
                    </form>
                </div>
            </div>
        @else
            <div class="alert alert-success mt-4">
                 Contrat signé le {{ $contract->signature_date->format('d/m/Y') }}
            </div>
        @endif

        <!-- Tableau échéancier
        @if ($contract->paymentSchedules->count())
            <h4 class="mt-5"> Échéancier de Paiement</h4>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Montant</th>
                        <th>Date d’échéance</th>
                        <th>Status</th>
                        <th>Date de paiement</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contract->paymentSchedules as $schedule)
                        <tr id="row-{{ $schedule->id }}">
                            <td>{{ $schedule->installment_number }}</td>
                            <td id="amount-cell-{{ $schedule->id }}">-</td>
                            <td>{{ $schedule->due_date->format('d/m/Y') }}</td>
                            <td id="status-cell-{{ $schedule->id }}">
                                @if ($schedule->is_paid)
                                    <span class="text-success"> Payé</span>
                                @elseif ($schedule->due_date->isPast())
                                    <span class="text-danger"> En retard</span>
                                @else
                                    <span class="text-warning"> À venir</span>
                                @endif
                            </td>
                            <td id="paid-date-{{ $schedule->id }}">
                                {{ $schedule->paid_date ? $schedule->paid_date->format('d/m/Y') : '-' }}
                            </td>
                            <td id="action-cell-{{ $schedule->id }}">
                                @if (!$schedule->is_paid)
                                    <button class="btn btn-sm btn-primary pay-btn" 
                                            data-schedule-id="{{ $schedule->id }}" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#payModal">
                                        💵 Verser
                                    </button>
                                @else
                                    <a href="{{ route('schedules.receipt', $schedule) }}" class="btn btn-sm btn-outline-secondary">
                                        📄 Reçu
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div> -->

    <!-- Modal -->
    <div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="payForm">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Saisir le montant à verser</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="current-schedule-id">
                        <div class="mb-3">
                            <label for="custom_amount" class="form-label">Montant à payer (FCFA)</label>
                            <input type="number" name="amount" id="custom_amount" class="form-control" required placeholder="Entrez le montant">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Confirmer le versement</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const payButtons = document.querySelectorAll('.pay-btn');
            const payForm = document.getElementById('payForm');
            const customAmount = document.getElementById('custom_amount');
            const currentScheduleId = document.getElementById('current-schedule-id');

            let actionUrl = '';

            payButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.scheduleId;
                    actionUrl = `/payment-schedules/${id}/pay`;
                    payForm.setAttribute('action', actionUrl);
                    customAmount.value = '';
                    currentScheduleId.value = id;
                });
            });

            payForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const amount = customAmount.value;
                const id = currentScheduleId.value;

                fetch(actionUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        _method: 'PUT',
                        amount: amount
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // ✅ Met à jour la cellule de montant
                        document.getElementById(`amount-cell-${id}`).textContent = parseInt(amount).toLocaleString('fr-FR') + ' FCFA';
                        // ✅ Met à jour statut
                        document.getElementById(`status-cell-${id}`).innerHTML = '<span class="text-success"> Payé</span>';
                        // ✅ Met à jour la date
                        const today = new Date();
                        const formattedDate = today.toLocaleDateString('fr-FR');
                        document.getElementById(`paid-date-${id}`).textContent = formattedDate;
                        // ✅ Remplace le bouton par le lien reçu
                        document.getElementById(`action-cell-${id}`).innerHTML =
                            `<a href="/payment-schedules/${id}/receipt" class="btn btn-sm btn-outline-secondary">📄 Reçu</a>`;
                        // Ferme le modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('payModal'));
                        modal.hide();
                    } else {
                        alert('Une erreur est survenue.');
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert('Erreur réseau.');
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
