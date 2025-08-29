<x-guest-layout>
    <style>
        /* Texte marron */
        label, 
        .form-check-label, 
        a, 
        h5, 
        .list-unstyled li {
            color: #5C4033 !important;
            font-weight: 600;
        }

        /* Lien mot de passe oublié marron avec hover */
        a {
            text-decoration: none;
        }
        a:hover {
            color: #3e2d22;
            text-decoration: underline;
        }

        /* Bouton connexion marron */
        .btn-primary {
            background-color: #5C4033;
            border-color: #5C4033;
            color: white;
            font-weight: 700;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #3e2d22;
            border-color: #3e2d22;
            color: white;
        }

        /* Pour les erreurs */
        .invalid-feedback {
            color: #b00020;
            font-weight: 600;
        }
    </style>

    <!-- Logo centré -->
    <div class="text-center my-4">
        <img src="{{ asset('images/image.png') }}" alt="Logo Yaye Dia" style="height: 100px;">
        <h2 class="mt-3" style="color:#5C4033;">Bienvenue sur Yaye Dia BTP</h2>
    </div>

    <!-- Statut de session -->
    @if (session('status'))
        <div class="alert alert-success" style="color:#155724; font-weight:600;">
            {{ session('status') }}
        </div>
    @endif

    <!-- Formulaire de connexion -->
    <form method="POST" action="{{ route('login') }}" class="card p-4 shadow-sm">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Adresse e-mail</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                   name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Mot de passe -->
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                   name="password" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Se souvenir de moi -->
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
            <label class="form-check-label" for="remember_me">Se souvenir de moi</label>
        </div>

        <!-- Bouton / mot de passe oublié -->
        <div class="d-flex justify-content-between align-items-center">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
            @endif
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </div>
    </form>

    <!-- Comptes de démonstration -->
    <div class="mt-4 p-3 bg-light border rounded">
        <h5 class="mb-3">Comptes de démonstration :</h5>
        <ul class="list-unstyled small">
            <li><strong>Administrateur :</strong> admin@yayedia.com / admin123</li>
            <li><strong>Responsable :</strong> manager@yayedia.com / manager123</li>
            <li><strong>Commercial :</strong> commercial@yayedia.com / commercial123</li>
        </ul>
    </div>
</x-guest-layout>
