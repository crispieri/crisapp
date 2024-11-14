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
        Schema::create('client_profiles', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->onDelete('cascade'); // Relación con el usuario
            $table->enum('client_type', ['individual', 'company'])->default('individual'); // Tipo de cliente
            $table->string('rut')->unique(); // RUT
            $table->string('business_name')->nullable(); // Razón Social (solo para empresas)
            $table->string('giro')->nullable(); // Giro o actividad económica (solo para empresas)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_profiles');
    }
};
