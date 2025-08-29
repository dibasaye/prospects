<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CaissierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si un caissier existe déjà
        if (!User::where('role', 'caissier')->exists()) {
            User::create([
                'first_name' => 'Caissier',
                'last_name' => 'Principal',
                'email' => 'caissier@prospecttracker.com',
                'password' => Hash::make('password123'),
                'role' => 'caissier',
                'phone' => '774567890',
                'address' => 'Adresse du caissier',
                'is_active' => true,
            ]);

            $this->command->info('Utilisateur caissier créé avec succès !');
            $this->command->info('Email: caissier@prospecttracker.com');
            $this->command->info('Mot de passe: password123');
        } else {
            $this->command->info('Un utilisateur caissier existe déjà.');
        }
    }
} 