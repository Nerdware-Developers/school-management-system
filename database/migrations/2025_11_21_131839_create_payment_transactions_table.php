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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_fee_term_id')->nullable()->constrained()->nullOnDelete();
            $table->string('transaction_id')->unique(); // Internal transaction ID
            $table->string('payment_gateway')->default('stripe'); // stripe, paypal, etc.
            $table->string('gateway_transaction_id')->nullable(); // Gateway's transaction ID
            $table->string('gateway_payment_intent_id')->nullable(); // Stripe payment intent ID
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('KES');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable(); // card, bank_transfer, etc.
            $table->text('description')->nullable();
            $table->json('gateway_response')->nullable(); // Store full gateway response
            $table->text('failure_reason')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->string('receipt_url')->nullable();
            $table->string('receipt_number')->nullable();
            $table->text('metadata')->nullable(); // Additional data
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index('transaction_id');
            $table->index('gateway_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
