<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>YAYE DIA BTP - Gestion Immobilière</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Figtree', sans-serif;
            background-color: #f3f4f6;
            color: #5C4033;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        a {
            text-decoration: none;
            color: #5C4033;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: background-color 0.3s ease, color 0.3s ease;
            display: inline-block;
        }
        a:hover {
            text-decoration: none;
            background-color: #5C4033;
            color: white !important;
        }
        h1, h2, h3, p {
            color: #5C4033;
        }

        nav {
            background-color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        nav .nav-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        nav .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.25rem;
            font-weight: 700;
        }
        nav .brand img {
            height: 70px; /* agrandi */
            width: auto;
        }
        nav .nav-links {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .btn-primary {
            background-color: #5C4033;
            color: white !important;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 700;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #3e2d22;
            color: white !important;
        }
        /* Pour les liens simples 'Connexion' */
        .nav-links > a:not(.btn-primary) {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            border: 2px solid transparent;
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
        .nav-links > a:not(.btn-primary):hover {
            background-color: #5C4033;
            color: white !important;
            border-color: #5C4033;
            text-decoration: none;
        }

        .video-hero {
            position: relative;
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .video-bg {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            transform: translate(-50%, -50%);
            z-index: -2;
            object-fit: cover;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(92, 64, 51, 0.5);
            z-index: -1;
        }
        .video-hero .content {
            z-index: 1;
            padding: 2rem;
            max-width: 700px;
        }
        .video-hero .content h1,
        .video-hero .content .lead,
        .video-hero .content .description {
            color: #D2B48C;
        }
        .video-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .video-hero .lead {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .video-hero .description {
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .features {
            background-color: white;
            padding: 4rem 1rem;
        }
        .features .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        .features-grid {
            display: grid;
            gap: 2rem;
            grid-template-columns: 1fr;
        }
        @media (min-width: 768px) {
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (min-width: 1024px) {
            .features-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        .feature-card {
            background-color: #f9fafb;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 0 6px rgba(0, 0, 0, 0.05);
        }

        .demo-accounts {
            background-color: #f9fafb;
            padding: 4rem 1rem;
            text-align: center;
        }
        .demo-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        @media (min-width: 768px) {
            .demo-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        .demo-card {
            background-color: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            text-align: left;
        }

        footer {
            background-color: #1f2937;
            color: white;
            padding: 2rem 1rem;
            text-align: center;
            margin-top: auto;
        }
    </style>
</head>
<body>

    <nav>
        <div class="nav-inner">
            <div class="brand">
                <img src="{{ asset('images/image.png') }}" alt="Logo">
                YAYE DIA BTP
            </div>
            <div class="nav-links">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary">Tableau de bord</a>
                    @else
                        <a href="{{ route('login') }}">Connexion</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary">Inscription</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <section class="video-hero">
        <video autoplay muted loop playsinline class="video-bg">
            <source src="{{ asset('videos/background.mp4') }}" type="video/mp4">
            Votre navigateur ne supporte pas la vidéo HTML5.
        </video>
        <div class="overlay"></div>
        <div class="content">
            <h1>YAYE DIA BTP</h1>
            <p class="lead">Système de Gestion Immobilière Moderne</p>
            <p class="description">
                Gérez efficacement vos prospects, sites, lots, contrats et paiements avec notre plateforme intégrée.
                Conçue pour les professionnels de l'immobilier au Sénégal.
            </p>
            <div class="btn-group">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-primary">Accéder au Tableau de Bord</a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary">Se Connecter</a>
                @endauth
            </div>
        </div>
    </section>

    <section class="features">
        <div class="section-title">
            <h2>Fonctionnalités Principales</h2>
            <p>Un système complet pour gérer tous les aspects de votre activité immobilière</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <h3>Gestion des Prospects</h3>
                <p>Suivi des prospects du premier contact jusqu’à la signature du contrat.</p>
            </div>
            <div class="feature-card">
                <h3>Gestion des Sites</h3>
                <p>Organisation des projets avec détails des lots, disponibilités, prix, etc.</p>
            </div>
            <div class="feature-card">
                <h3>Suivi des Paiements</h3>
                <p>Validation des paiements : adhésion, réservation et mensualités.</p>
            </div>
            <div class="feature-card">
                <h3>Génération de Contrats</h3>
                <p>Création automatisée des contrats avec échéancier personnalisé.</p>
            </div>
            <div class="feature-card">
                <h3>Contrôle d’Accès</h3>
                <p>Rôles : administrateur, responsable commercial, commercial.</p>
            </div>
            <div class="feature-card">
                <h3>Statistiques</h3>
                <p>Dashboard avec données en temps réel et historique des activités.</p>
            </div>
        </div>
    </section>

    @guest
    <section class="demo-accounts">
        <div class="container">
            <h2>Comptes de Démonstration</h2>
            <div class="demo-grid">
                <div class="demo-card">
                    <h3>Administrateur</h3>
                    <p><strong>Email:</strong> admin@yayedia.com</p>
                    <p><strong>Mot de passe:</strong> admin123</p>
                </div>
                <div class="demo-card">
                    <h3>Responsable Commercial</h3>
                    <p><strong>Email:</strong> manager@yayedia.com</p>
                    <p><strong>Mot de passe:</strong> manager123</p>
                </div>
                <div class="demo-card">
                    <h3>Commercial</h3>
                    <p><strong>Email:</strong> commercial@yayedia.com</p>
                    <p><strong>Mot de passe:</strong> commercial123</p>
                </div>
            </div>
        </div>
    </section>
    @endguest

    <footer>
        <div class="container">
            <p>&copy; {{ date('Y') }} YAYE DIA BTP. Système de gestion immobilière moderne.</p>
        </div>
    </footer>

</body>
</html>
