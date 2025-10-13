<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Informations communes
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'agent', 'donor'])->default('donor');
            $table->boolean('is_active')->default(true);
            
            // Informations générales
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['M', 'F', 'O'])->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            
            // Informations spécifiques DONNEUR
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->date('last_donation_date')->nullable();
            $table->integer('donation_count')->default(0);
            
            // Informations spécifiques AGENT
            $table->string('department', 100)->nullable();
            $table->string('employee_number', 50)->unique()->nullable();
            $table->date('hire_date')->nullable();
            
            // Informations spécifiques ADMIN
            $table->boolean('super_admin')->default(false);
            
            // Système
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};