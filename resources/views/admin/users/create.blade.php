<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 fw-bold">Ajouter un Utilisateur</h2>
    </x-slot>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="first_name" class="form-label">Prénom</label>
            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
        </div>

        <div class="mb-3">
            <label for="last_name" class="form-label">Nom</label>
            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Adresse Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Rôle</label>
            <select name="role" class="form-select" required>
                <option value="">-- Choisir un rôle --</option>
                <option value="administrateur">Administrateur</option>
                <option value="responsable_commercial">Responsable Commercial</option>
                <option value="commercial">Commercial</option>
                <option value="caissier">Caissier</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Créer</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</x-app-layout>
