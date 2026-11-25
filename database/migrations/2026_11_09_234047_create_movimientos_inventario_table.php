<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ficha_id')->constrained('fichas')->onDelete('restrict');
            $table->enum('tipo', ['entrada', 'salida']);
            // inventario_inicial, compra, venta, anulacion_venta, ajuste
            $table->string('concepto', 30);
            $table->unsignedInteger('cantidad');            // siempre positiva; el tipo indica el sentido
            $table->integer('saldo');                       // stock de la ficha DESPUÉS del movimiento
            $table->decimal('costo_unitario', 12, 2)->nullable();
            $table->nullableMorphs('referencia');           // documento que lo originó (Venta, Compra...)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('notas', 255)->nullable();       // motivo obligatorio en los ajustes
            $table->timestamps();

            $table->index(['ficha_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
