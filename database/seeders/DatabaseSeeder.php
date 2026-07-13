<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'nume' => 'admin',
            'prenume' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('a123'), // Replace with your desired password
        ]);
    }
}
