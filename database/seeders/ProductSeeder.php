<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * produse pentru compania user-ului user@example.com.
     */
    public function run(): void
    {
        $user = User::where('email', 'user@example.com')->first();

        if (! $user) {
            $this->command->warn('User-ul user@example.com nu există. Rulează mai întâi DatabaseSeeder.');
            return;
        }

        $company = $user->companies()->first();

        if (! $company) {
            $this->command->warn('User-ul user@example.com nu are nicio companie asociată.');
            return;
        }

        Product::firstOrCreate(
            ['company_id' => $company->id, 'sku' => 'SRV-CONS'],
            [
                'name' => 'Servicii de consultanță',
                'unit_measure' => 'oră',
                'unit_price' => 250.00,
                'vat_rate' => 19.00,
            ]
        );

        Product::firstOrCreate(
            ['company_id' => $company->id, 'sku' => 'SRV-DEV'],
            [
                'name' => 'Dezvoltare software',
                'unit_measure' => 'oră',
                'unit_price' => 300.00,
                'vat_rate' => 19.00,
            ]
        );

        Product::firstOrCreate(
            ['company_id' => $company->id, 'sku' => 'PRD-LIC'],
            [
                'name' => 'Licență software anuală',
                'unit_measure' => 'buc',
                'unit_price' => 1200.00,
                'vat_rate' => 19.00,
            ]
        );

        $this->command->info('Produse create pentru compania user@example.com.');
    }
}
