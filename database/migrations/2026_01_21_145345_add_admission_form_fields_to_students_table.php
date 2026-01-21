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
        Schema::table('students', function (Blueprint $table) {
            // Transport fields
            $table->boolean('uses_transport')->default(false);
            $table->integer('transport_section')->nullable()->comment('1, 2, or 3');
            
            // Additional student information from admission form
            $table->string('former_school')->nullable();
            $table->string('residence')->nullable();
            $table->string('term')->nullable();
            
            // Father's information
            $table->string('father_name')->nullable();
            $table->string('father_telephone')->nullable();
            
            // Mother's information
            $table->string('mother_name')->nullable();
            $table->string('mother_telephone')->nullable();
            
            // Additional parent/guardian information
            $table->string('occupation')->nullable();
            $table->string('religion')->nullable();
            
            // Medical information
            $table->boolean('has_ailment')->default(false);
            $table->text('ailment_details')->nullable();
            
            // Emergency contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_telephone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'uses_transport',
                'transport_section',
                'former_school',
                'residence',
                'term',
                'father_name',
                'father_telephone',
                'mother_name',
                'mother_telephone',
                'occupation',
                'religion',
                'has_ailment',
                'ailment_details',
                'emergency_contact_name',
                'emergency_contact_telephone',
            ]);
        });
    }
};
