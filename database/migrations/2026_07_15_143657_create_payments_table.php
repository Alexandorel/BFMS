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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');

            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');

            $table->date('payment_date');
            $table->decimal('amount', 15, 2);

            $table->enum('currency', ['RON', 'EUR', 'USD'])->default('RON');
            $table->decimal('exchange_rate', 10, 4)->default(1);

            $table->enum('payment_method', [
                'cash',           // Cash / Chitanță
                'bank_transfer',  // Ordin de plată
                'card',           // Card online
            ]);

            //Numar plata
            $table->string('reference', 100)->nullable();

            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->index(['invoice_id']);
            $table->index(['company_id', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
