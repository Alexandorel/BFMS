<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('juridical_form', 255);
            $table->string('cui', 20)->unique();
            $table->string('trade_registry_number', 20)->unique();
            $table->string('county', 255);
            $table->string('city', 255);
            $table->string('address', 255);
            $table->decimal('social_capital', 15, 2);
            $table->boolean('vat_payer')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
