<x-app-layout>
    <x-slot name="header">
        <h2>Assigner le prospect : {{ $prospect->full_name }}</h2>
    </x-slot>

    <div class="container py-4">
        <form action="{{ route('prospects.assign', $prospect) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="commercial_id" class="form-label">Choisir un commercial</label>
                <select name="commercial_id" id="commercial_id" class="form-select" {{ $prospect->assigned_to_id ? 'disabled' : '' }} required>
                    <option value="">-- Sélectionner un commercial --</option>
                    @foreach ($commerciaux as $commercial)
                        <option value="{{ $commercial->id }}"
                            {{ $prospect->assigned_to_id == $commercial->id ? 'selected' : '' }}>
                            {{ $commercial->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary" {{ $prospect->assigned_to_id ? 'disabled' : '' }}>Assigner</button>
            <a href="{{ route('prospects.index') }}" class="btn btn-secondary">Annuler</a>

            @if ($prospect->assigned_to_id)
                <div class="alert alert-info mt-3">
                    Ce prospect est déjà assigné à <strong>{{ $prospect->assignedTo->full_name ?? 'un commercial' }}</strong>.
                </div>
            @endif
        </form>
    </div>
</x-app-layout>
