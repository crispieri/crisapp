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
        Schema::create('addresses', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users')->cascadeOnDelete(); // Relación con el usuario
            $table->string('street_address'); // Dirección principal
            $table->string('commune')->nullable(); // Comuna
            $table->string('city')->nullable(); // Ciudad
            $table->string('region')->nullable(); // Región
            $table->string('country')->default('Chile'); // País
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
