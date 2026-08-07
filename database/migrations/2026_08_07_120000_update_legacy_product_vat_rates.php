<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Align the reusable product catalogue with the Romanian VAT rates that
     * apply from 1 August 2025. Invoice line snapshots remain untouched so
     * previously issued documents preserve their original fiscal values.
     */
    public function up(): void
    {
        DB::table('products')->where('vat_rate', 19)->update(['vat_rate' => 21]);
        DB::table('products')->whereIn('vat_rate', [5, 9])->update(['vat_rate' => 11]);
    }

    /**
     * This data migration is intentionally irreversible: mapping 11% back to
     * either 5% or 9% cannot be done reliably.
     */
    public function down(): void
    {
        // Intentionally left blank.
    }
};
