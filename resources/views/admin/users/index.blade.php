<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 fw-bold">Gestion des Utilisateurs</h2>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus me-1"></i> Ajouter un Utilisateur
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td style="min-width: 150px;">
                        <form action="{{ route('admin.users.updateRole', $user) }}" method="POST" class="m-0">
                            @csrf
                            <select name="role" onchange="this.form.submit()" class="form-select form-select-sm">
                                <option value="administrateur" {{ $user->role == 'administrateur' ? 'selected' : '' }}>Administrateur</option>
                                <option value="responsable_commercial" {{ $user->role == 'responsable_commercial' ? 'selected' : '' }}>Responsable Commercial</option>
                                <option value="commercial" {{ $user->role == 'commercial' ? 'selected' : '' }}>Commercial</option>
                                <option value="caissier" {{ $user->role == 'caissier' ? 'selected' : '' }}>Caissier</option>
                            </select>
                        </form>
                    </td>
                    <td style="min-width: 100px;">
                        <form action="{{ route('admin.users.toggle', $user) }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-success' : 'btn-secondary' }}">
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </button>
                        </form>
                    </td>
                    <td style="min-width: 110px;">
                        @if(auth()->id() !== $user->id)
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Supprimer cet utilisateur ?')" class="m-0">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                        @else
                            <span class="badge bg-primary">Vous</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
