<x-app-layout>
    <x-slot name="header">
        <h2 class="h5">Ajouter une relance - {{ $prospect->full_name }}</h2>
    </x-slot>

    <div class="container py-4">
        <form method="POST" action="{{ route('prospects.followup.store', $prospect) }}">
            @csrf
            <div class="mb-3">
                <label for="type" class="form-label">Type d'action</label>
                <select name="type" id="type" class="form-select" required>
                    <option value="appel">Appel</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="rdv">RDV</option>
                    <option value="email">Email</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="notes" class="form-label">Compte rendu</label>
                <textarea name="notes" id="notes" rows="4" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label for="next_follow_up" class="form-label">Prochaine relance (facultatif)</label>
                <input type="date" name="next_follow_up" class="form-control" />
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Statut du prospect</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="en_relance" {{ $prospect->status == 'en_relance' ? 'selected' : '' }}>En relance</option>
                    <option value="interesse" {{ $prospect->status == 'interesse' ? 'selected' : '' }}>Intéressé</option>
</select>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer</button>
        </form>
    </div>
</x-app-layout>
