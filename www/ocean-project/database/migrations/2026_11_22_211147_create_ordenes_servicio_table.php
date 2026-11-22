<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_servicio', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')
                ->constrained('empresas');

            $table->string('numero_orden', 30);
            $table->date('fecha');
            $table->string('cliente')->nullable();
            $table->text('descripcion');

            $table->decimal('ingreso_total', 15, 2)
                ->default(0);

            $table->enum('estado', [
                'pendiente',
                'en_proceso',
                'finalizada',
                'cancelada'
            ])->default('pendiente');

            $table->timestamps();

            $table->unique([
                'empresa_id',
                'numero_orden'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_servicio');
    }
};
