<?php

use App\Enums\OrderStatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->enum('status', array_column(OrderStatusEnum::cases(), 'value'))->default(OrderStatusEnum::PENDING);
            $table->foreignUlid('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->integer('discount_amount')->default(0); // Valor de descuento aplicado
            $table->integer('grand_total')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
