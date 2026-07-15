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
        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');

            $table->foreignId('product_id')->nullable()
                ->constrained('products')->onDelete('set null');

            $table->string('product_name_snapshot');
            $table->string('sku_snapshot', 100)->nullable();
            $table->string('unit_measure_snapshot', 50);
            $table->decimal('unit_price_snapshot', 10, 2);
            $table->decimal('vat_rate_snapshot', 5, 2);

            $table->decimal('quantity', 10, 2);

            // Totaluri per linie (calculate la salvare, nu recalculate ulterior)
            $table->decimal('line_subtotal', 15, 2);
            $table->decimal('line_vat', 15, 2);
            $table->decimal('line_total', 15, 2);

            // Ordinea liniilor pe factură (pentru afișare/PDF)
            $table->unsignedInteger('position')->default(0);

            $table->timestamps();

            $table->index(['invoice_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_lines');
    }
};
