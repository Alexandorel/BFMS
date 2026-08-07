<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Company;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Clienți pentru compania de test comună tuturor celor trei conturi.
     */
    public function run(): void
    {
        $company = Company::where('cui', '12345678')->first();

        if (! $company) {
            $this->command->warn('Compania de test nu există. Rulează mai întâi DatabaseSeeder.');

            return;
        }

        // Client persoana juridica
        Client::firstOrCreate(
            ['company_id' => $company->id, 'cui' => 'RO45678901'],
            [
                'client_type' => 'company',
                'name' => 'Alpha Distribution SRL',
                'trade_registry_number' => 'J40/5678/2019',
                'vat_number' => 'RO45678901',
                'county' => 'București',
                'city' => 'București',
                'address' => 'Str. Victoriei nr. 10',
                'email' => 'contact@alpha-distribution.ro',
                'phone' => '0721000111',
            ]
        );

        // Client persoana fizica
        Client::firstOrCreate(
            ['company_id' => $company->id, 'cnp' => '1900101223344'],
            [
                'client_type' => 'individual',
                'first_name' => 'Ion',
                'last_name' => 'Popescu',
                'county' => 'Cluj',
                'city' => 'Cluj-Napoca',
                'address' => 'Str. Memorandumului nr. 28',
                'email' => 'ion.popescu@example.com',
                'phone' => '0742555666',
            ]
        );

        $this->command->info('Clienți creați pentru compania de test comună celor trei conturi.');
    }
}
