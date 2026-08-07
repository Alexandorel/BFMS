<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Produse pentru compania de test comună tuturor celor trei conturi.
     */
    public function run(): void
    {
        $company = Company::where('cui', '12345678')->first();

        if (! $company) {
            $this->command->warn('Compania de test nu există. Rulează mai întâi DatabaseSeeder.');

            return;
        }

        Product::firstOrCreate(
            ['company_id' => $company->id, 'sku' => 'SRV-CONS'],
            [
                'name' => 'Servicii de consultanță',
                'unit_measure' => 'oră',
                'unit_price' => 250.00,
                'quantity' => 120.00,
                'vat_rate' => 19.00,
            ]
        );

        Product::firstOrCreate(
            ['company_id' => $company->id, 'sku' => 'SRV-DEV'],
            [
                'name' => 'Dezvoltare software',
                'unit_measure' => 'oră',
                'unit_price' => 300.00,
                'quantity' => 80.00,
                'vat_rate' => 19.00,
            ]
        );

        Product::firstOrCreate(
            ['company_id' => $company->id, 'sku' => 'PRD-LIC'],
            [
                'name' => 'Licență software anuală',
                'unit_measure' => 'buc',
                'unit_price' => 1200.00,
                'quantity' => 50.00,
                'vat_rate' => 19.00,
            ]
        );

        $this->command->info('Produse create pentru compania de test comună celor trei conturi.');
    }
}
