<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * client pentru compania user-ului user@example.com
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

        $this->command->info('Clienți creați pentru compania user@example.com.');
    }
}
