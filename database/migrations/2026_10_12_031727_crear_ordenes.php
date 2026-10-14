<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     */
    public function up(): void
    {
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 20)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->enum('tipo_servicio', ['comer_aqui', 'para_llevar', 'telefono']);
            $table->enum('estado', ['abierta', 'pagada', 'cancelada'])->default('abierta');
            $table->string('cliente_nombre', 100)->nullable();
            $table->string('cliente_telefono', 20)->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia'])->nullable();
            $table->decimal('monto_recibido', 10, 2)->nullable();
            $table->decimal('cambio', 10, 2)->default(0);
            $table->timestamp('pagada_at')->nullable();
            $table->timestamps();

            $table->index(['estado', 'created_at']);
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};