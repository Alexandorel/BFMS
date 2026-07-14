<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
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
        $user = User::create([
            'first_name' => 'user',
            'last_name' => 'test',
            'email' => 'user@example.com',
            'password' => bcrypt('user123'),
            'role' => 'administrator',
        ]);

        $company = Company::create([
            'name' => 'Test Company',
            'juridical_form' => 'SRL',
            'cui' => '12345678',
            'trade_registry_number' => 'J40/1234/2020',
            'county' => 'Test County',
            'city' => 'Test City',
            'address' => '123 Test Street',
            'social_capital' => 10000.00,
            'vat_payer' => true,
        ]);

        $company->users()->attach($user->id);
    }
}
