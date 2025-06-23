<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Administrator
        User::create([
            'first_name' => 'Yaye',
            'last_name' => 'Dia',
            'email' => 'admin@yayedia.com',
            'password' => Hash::make('admin123'),
            'role' => 'administrateur',
            'phone' => '+221701234567',
            'is_active' => true,
        ]);

        // Create Commercial Manager
        User::create([
            'first_name' => 'Fatou',
            'last_name' => 'Sow',
            'email' => 'manager@yayedia.com',
            'password' => Hash::make('manager123'),
            'role' => 'responsable_commercial',
            'phone' => '+221702345678',
            'is_active' => true,
        ]);

        // Create Commercial Agent
        User::create([
            'first_name' => 'Moussa',
            'last_name' => 'Ba',
            'email' => 'commercial@yayedia.com',
            'password' => Hash::make('commercial123'),
            'role' => 'commercial',
            'phone' => '+221703456789',
            'is_active' => true,
        ]);
    }
}