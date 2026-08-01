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

            // Metodele acceptate sunt fixate de F-401 (etichetele in App\Enums\PaymentMethod)
            $table->enum('payment_method', [
                'cash',
                'bank_transfer',
                'card',
                'ramburs',
            ]);

            //Numar plata
            $table->string('reference', 100)->nullable();

            // F-401: optional receipt for cash payment
            // allocated number from company's series (DocumentType::Receipt).
            $table->string('receipt_series', 10)->nullable();
            $table->unsignedInteger('receipt_number')->nullable();

            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->index(['invoice_id']);
            $table->index(['company_id', 'payment_date']);

            // null on both columns if there is no payment
            $table->unique(
                ['company_id', 'receipt_series', 'receipt_number'],
                'payments_receipt_number_unique'
            );
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
