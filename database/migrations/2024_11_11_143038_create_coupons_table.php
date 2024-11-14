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
        Schema::create('coupons', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('code')->unique();
            $table->enum('type', ['fixed', 'percentage']); // Tipo de descuento
            $table->decimal('discount_value', 8, 2); // Valor del descuento
            $table->dateTime('expires_at')->nullable(); // Fecha de expiración
            $table->integer('usage_limit')->nullable(); // Límite de usos del cupón
            $table->integer('used_count')->default(0); // Cantidad de veces usado
            $table->boolean('is_active')->default(true); // Estado activo/inactivo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
