<?php

use App\Enums\DocumentType;
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
        Schema::create('electronic_documents', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('order_id')->constrained()->onDelete('cascade'); // Relacion con la orden
            $table->enum('type', array_column(DocumentType::cases(), 'value')); // Tipo de documento (Factura, Boleta, etc)
            $table->unsignedBigInteger('folio')->nullable(); // Número de folio del documento
            $table->decimal('total', 15, 2); // Monto total del documento
            $table->string('client_name'); // Nombre del cliente
            $table->string('client_rut'); // RUT del cliente
            $table->string('status')->default('pending'); // Estado del documento (ej: pending, issued, canceled)
            $table->json('response_data')->nullable(); // Respuesta de la API del SII para auditoría
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electronic_documents');
    }
};
