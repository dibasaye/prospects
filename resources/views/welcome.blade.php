<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>YAYE DIA BTP - Gestion Immobilière</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <!-- Navigation -->
            <nav class="bg-white shadow-lg">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <div class="text-xl font-bold text-blue-600">YAYE DIA BTP</div>
                        </div>
                        <div class="flex items-center space-x-4">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="text-blue-600 hover:text-blue-800 font-medium">Tableau de bord</a>
                                @else
                                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800">Connexion</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Inscription</a>
                                    @endif
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-800 dark:to-gray-900">
                <div class="max-w-7xl mx-auto px-4 py-16 sm:py-24">
                    <div class="text-center">
                        <h1 class="text-4xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                            YAYE DIA BTP
                        </h1>
                        <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 mb-8">
                            Système de Gestion Immobilière Moderne
                        </p>
                        <p class="text-lg text-gray-500 dark:text-gray-400 mb-12 max-w-3xl mx-auto">
                            Gérez efficacement vos prospects, sites, lots, contrats et paiements avec notre plateforme intégrée.
                            Conçue spécialement pour les professionnels de l'immobilier au Sénégal.
                        </p>
                        
                        <div class="flex justify-center space-x-4">
                            @auth
                                <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-8 py-3 rounded-md text-lg font-medium hover:bg-blue-700 transition duration-150">
                                    Accéder au Tableau de Bord
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="bg-blue-600 text-white px-8 py-3 rounded-md text-lg font-medium hover:bg-blue-700 transition duration-150">
                                    Se Connecter
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="py-16 bg-white dark:bg-gray-800">
                <div class="max-w-7xl mx-auto px-4">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                            Fonctionnalités Principales
                        </h2>
                        <p class="text-lg text-gray-600 dark:text-gray-300">
                            Un système complet pour gérer tous les aspects de votre activité immobilière
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Gestion des Prospects -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Gestion des Prospects</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Suivez vos prospects de la première prise de contact jusqu'à la conversion, avec un système de statuts complet.
                            </p>
                        </div>

                        <!-- Gestion des Sites -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Gestion des Sites</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Gérez vos projets immobiliers avec les lots, prix, et toutes les informations importantes.
                            </p>
                        </div>

                        <!-- Système de Paiement -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Suivi des Paiements</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Suivez les paiements d'adhésion, de réservation et les mensualités avec un système de validation.
                            </p>
                        </div>

                        <!-- Gestion des Contrats -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Génération de Contrats</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Créez et gérez les contrats de vente avec des échéanciers de paiement personnalisés.
                            </p>
                        </div>

                        <!-- Système de Rôles -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Gestion des Accès</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Contrôlez les accès avec des rôles d'administrateur, responsable commercial et commercial.
                            </p>
                        </div>

                        <!-- Rapports et Analytics -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Rapports & Statistiques</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Tableaux de bord avec statistiques en temps réel et historique des activités détaillé.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Demo Accounts Section -->
            @guest
            <div class="py-16 bg-gray-50 dark:bg-gray-700">
                <div class="max-w-4xl mx-auto px-4 text-center">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">
                        Comptes de Démonstration
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Administrateur</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Accès complet au système</p>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <p><strong>Email:</strong> admin@yayedia.com</p>
                                <p><strong>Mot de passe:</strong> admin123</p>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Responsable Commercial</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Gestion d'équipe</p>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <p><strong>Email:</strong> manager@yayedia.com</p>
                                <p><strong>Mot de passe:</strong> manager123</p>
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Commercial</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Gestion des prospects</p>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <p><strong>Email:</strong> commercial@yayedia.com</p>
                                <p><strong>Mot de passe:</strong> commercial123</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endguest

            <!-- Footer -->
            <footer class="bg-gray-800 dark:bg-gray-900 text-white py-8">
                <div class="max-w-7xl mx-auto px-4 text-center">
                    <p>&copy; {{ date('Y') }} YAYE DIA BTP. Système de gestion immobilière moderne.</p>
                </div>
            </footer>
        </div>
    </body>
</html>