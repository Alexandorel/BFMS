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
            $table->string('denumire', 255);
            $table->string('forma_juridica', 255);
            $table->string('cui', 20);
            $table->string('nr_reg_com', 20);
            $table->string('judet', 255);
            $table->string('localitate', 255);
            $table->string('adresa', 255);
            $table->decimal('capital_social', 15, 2);
            $table->boolean('platitor_tva')->default(false);
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
