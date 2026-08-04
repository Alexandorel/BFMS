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
        Schema::create('invoice_notifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');

            // legat doar pentru notificările de tip payment_confirmation
            $table->foreignId('payment_id')->nullable()
                ->constrained('payments')->onDelete('cascade');

            $table->enum('type', [
                'issued',
                'reminder_before_due',
                'reminder_due',
                'overdue_1',
                'overdue_2',
                'payment_confirmation',
            ]);

            $table->timestamp('sent_at')->nullable();
            $table->string('sent_to'); // email-ul folosit efectiv la trimitere

            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index(['invoice_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_notifications');
    }
};
