<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // 🧾 Legal Section
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender');
            $table->date('date_of_birth');
            $table->string('roll')->nullable();
            $table->string('class');
            $table->string('admission_number')->unique();
            $table->string('address')->nullable();
            $table->string('image')->nullable();

            // 👨‍👩‍👧 Parent Information
            $table->string('parent_name')->nullable();
            $table->string('parent_number')->nullable();
            $table->string('parent_relationship')->nullable();
            $table->string('parent_email')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_number')->nullable();
            $table->string('guardian_email')->nullable();

            // ⚽ Co-Activities
            $table->string('sports')->nullable();
            $table->string('clubs')->nullable();
            $table->string('talents')->nullable();


            // 🏥 Medical Information
            $table->string('blood_group')->nullable();
            $table->string('known_allergies')->nullable();
            $table->string('medical_condition')->nullable();
            $table->string('doctor_contact')->nullable();
            $table->string('emergency_contact')->nullable();

            // 💰 Financial Information
            $table->decimal('fee_amount', 10, 2)->nullable();
            $table->string('financial_year')->nullable();
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->string('fee_type')->nullable();
            $table->decimal('balance', 10, 2)->nullable();
            $table->string('payment_status')->nullable();
            $table->string('transaction_id')->nullable();
            $table->date('next_due_date')->nullable();
            $table->string('scholarship')->nullable();
            $table->string('sponsor_name')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
