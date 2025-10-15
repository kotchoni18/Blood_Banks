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
        Schema::create('blood_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('blood_group');
            $table->integer('quantity_units');
            $table->date('expiry_date');
            $table->enum('status', ['good', 'low', 'critical'])->default('good');
           $table->string('location')->nullable();
            $table->decimal('temperature', 3, 1)->default(4.0);
            $table->date('collection_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_stocks');
    }
};
