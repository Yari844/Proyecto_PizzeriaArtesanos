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
        Schema::create('orden_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->foreignId('tamano_id')->nullable()->constrained('tamanos')->restrictOnDelete();
            $table->string('producto_nombre', 100);
            $table->string('tamano_nombre', 30)->nullable();
            $table->boolean('orilla_queso')->default(false);
            $table->decimal('precio_unitario', 8, 2);
            $table->decimal('precio_orilla', 8, 2)->default(0);
            $table->unsignedSmallInteger('cantidad')->default(1);
            $table->decimal('importe', 10, 2);
            $table->string('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_detalles');
    }
};