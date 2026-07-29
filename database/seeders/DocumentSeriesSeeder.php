<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Services\DocumentSeriesService;
use Illuminate\Database\Seeder;

class DocumentSeriesSeeder extends Seeder
{
    /**
     * Backfill: default series for companies that don't have any yet.
     */
    public function run(): void
    {
        $seriesService = app(DocumentSeriesService::class);

        Company::query()->each(function (Company $company) use ($seriesService) {
            $seriesService->ensureDefaultsFor($company);
        });

        $this->command->info('Serii implicite completate pentru toate firmele.');
    }
}
