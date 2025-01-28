<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'musique'],
            ['name' => 'Cuisine'],
            ['name' => 'Art'],
            ['name' => 'Cinéma'],
            ['name' => 'Théâtre'],
            ['name' => 'Conférence'],   
            ['name' => 'Webinaire'],
            ['name' => 'Festival'],
            ['name' => 'Sport'],
        ]);
    }
}
