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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('restrict');
            $table->foreignId('document_series_id')->constrained('document_series')->onDelete('restrict');

            // Tipul documentului: factura, proforma, chitanta
            $table->enum('document_type', ['factura', 'proforma', 'chitanta']);
            $table->string('series', 10)->nullable();
            $table->unsignedInteger('number')->nullable();

            $table->enum('status', [
                'draft',            // Ciornă
                'issued',           // Emisă
                'partially_paid',   // Încasată Parțial
                'fully_paid',       // Încasată Total
                'cancelled',        // Anulată
                'credited',         // Stornată
            ])->default('draft');

            $table->date('issue_date')->nullable();
            $table->date('due_date')->nullable();

            $table->enum('currency', ['RON', 'EUR', 'USD'])->default('RON');
            $table->decimal('exchange_rate', 10, 4)->default(1);

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('vat_total', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);

            $table->foreignId('credited_invoice_id')->nullable()
                ->constrained('invoices')->onDelete('restrict');


            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');

            $table->timestamps();

            $table->unique(['company_id', 'document_type', 'series', 'number'], 'invoices_series_number_unique');

            $table->index(['company_id', 'status']);
            $table->index(['client_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
