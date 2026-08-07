<?php

namespace Database\Seeders;

use App\Models\Company;
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
        $administrator = User::updateOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'first_name' => 'admin',
            'last_name' => 'test',
            'password' => bcrypt('admin123'),
            'role' => 'administrator',
        ]);

        $company = Company::updateOrCreate([
            'cui' => '12345678',
        ], [
            'name' => 'Companie Test SRL',
            'juridical_form' => 'SA',
            'trade_registry_number' => 'J40/1234/2020',
            'county' => 'Test County',
            'city' => 'Test City',
            'address' => '123 Test Street',
            'social_capital' => 10000.00,
            'vat_payer' => true,
            'email' => 'companietest332@gmail.com',
        ]);

        $company->users()->syncWithoutDetaching([$administrator->id]);

        $operator = User::updateOrCreate([
            'email' => 'operator@gmail.com',
        ], [
            'first_name' => 'operator',
            'last_name' => 'test',
            'password' => bcrypt('operator123'),
            'role' => 'operator',
        ]);

        $company->users()->syncWithoutDetaching([$operator->id]);

        $accountant = User::updateOrCreate([
            'email' => 'contabil@gmail.com',
        ], [
            'first_name' => 'contabil',
            'last_name' => 'test',
            'password' => bcrypt('contabil123'),
            'role' => 'contabil',
        ]);

        $company->users()->syncWithoutDetaching([$accountant->id]);

        $this->call([
            ClientSeeder::class,
            ProductSeeder::class,
            InvoiceSeeder::class,
            DocumentSeriesSeeder::class,
        ]);
    }
}
