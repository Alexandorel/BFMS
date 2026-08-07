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
            'first_name' => 'admin',
            'last_name' => 'test',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123'),
            'role' => 'administrator',
        ]);

        $company = Company::create([
            'name' => 'Companie Test SRL',
            'juridical_form' => 'SA',
            'cui' => '12345678',
            'trade_registry_number' => 'J40/1234/2020',
            'county' => 'Test County',
            'city' => 'Test City',
            'address' => '123 Test Street',
            'social_capital' => 10000.00,
            'vat_payer' => true,
            'email' => 'companietest332@gmail.com',
        ]);

        $company->users()->attach($user->id);

        $user = User::create([
            'first_name' => 'operator',
            'last_name' => 'test',
            'email' => 'operator@gmail.com',
            'password' => bcrypt('operator123'),
            'role' => 'operator',
        ]);

        $company->users()->attach($user->id);

        $user = User::create([
            'first_name' => 'contabil',
            'last_name' => 'test',
            'email' => 'contabil@gmail.com',
            'password' => bcrypt('contabil123'),
            'role' => 'contabil',
        ]);

        $company->users()->attach($user->id);

        $this->call([
            ClientSeeder::class,
            ProductSeeder::class,
            InvoiceSeeder::class,
            DocumentSeriesSeeder::class,
        ]);
    }
}
