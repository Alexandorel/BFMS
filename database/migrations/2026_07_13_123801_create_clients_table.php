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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->enum('client_type', ['individual', 'company'])->default('individual');
            $table->string('name', 255)->nullable();
            $table->string('cui', 20)->nullable();
            $table->string('trade_registry_number', 20)->nullable();
            $table->string('vat_number', 20)->nullable();
            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('cnp', 20)->nullable();
            $table->string('county', 255);
            $table->string('city', 255);
            $table->string('address', 255);
            $table->string('delivery_address', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 20)->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'cnp']);
            $table->unique(['company_id', 'cui']);
            $table->unique(['company_id', 'trade_registry_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
