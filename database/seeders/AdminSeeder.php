<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Création de l'administrateur
        User::create([
            'username' => 'melissa',
            'email' => 'melissa19@gmail.com',
            'password' => Hash::make('commentfiarepourlehasher'), // Remplacez 'password' par un mot de passe sécurisé
            'role' => 'admin',
        ]);

    }
}

