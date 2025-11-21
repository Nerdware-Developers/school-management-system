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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location')->nullable();
            $table->enum('type', ['holiday', 'exam', 'event', 'meeting', 'sports', 'cultural', 'other'])->default('event');
            $table->enum('visibility', ['public', 'staff', 'students', 'parents', 'specific_class'])->default('public');
            $table->string('target_class')->nullable(); // For class-specific events
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('color')->default('#3b82f6'); // For calendar display
            $table->boolean('is_all_day')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['start_date', 'end_date']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
