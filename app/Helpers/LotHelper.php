<?php

if (! function_exists('lotStatusColor')) {
    function lotStatusColor(string $status): string
    {
        return match ($status) {
            'available' => '#28a745',       // vert
            'temp_reserved' => '#ffc107',   // jaune
            'reserved' => '#fd7e14',        // orange
            'sold' => '#dc3545',            // rouge
            default => '#6c757d',           // gris
        };
    }
}

if (! function_exists('lotStatusDisplay')) {
    function lotStatusDisplay(string $status): string
    {
        return match ($status) {
            'available' => 'Disponible',
            'temp_reserved' => 'Réservation temporaire',
            'reserved' => 'Réservé',
            'sold' => 'Vendu',
            default => 'Inconnu',
        };
    }
}
