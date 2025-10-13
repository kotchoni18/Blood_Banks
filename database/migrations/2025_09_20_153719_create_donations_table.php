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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->string('blood_group');
            $table->enum('donation_type', ['whole_blood', 'plasma', 'platelets', 'double_red']);
            $table->integer('quantity_ml')->default(450);
            $table->decimal('hemoglobin_level', 3, 1);
            $table->string('blood_pressure');
            $table->decimal('weight', 5, 2);
            $table->text('medical_notes')->nullable();
            $table->enum('status', ['completed', 'pending', 'rejected'])->default('completed');
            $table->boolean('consent_given')->default(true);
            $table->boolean('medical_check_passed')->default(true);
            $table->boolean('eligibility_verified')->default(true);
            $table->datetime('donation_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
