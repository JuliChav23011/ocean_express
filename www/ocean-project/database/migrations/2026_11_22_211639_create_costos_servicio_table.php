<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('costos_servicio', function (Blueprint $table) {
            $table->id();

            $table->foreignId('orden_servicio_id')
                ->constrained('ordenes_servicio')
                ->restrictOnDelete();

            $table->foreignId('evidencia_id')
                ->nullable()
                ->constrained('evidencias')
                ->nullOnDelete();

            $table->date('fecha');

            $table->string('concepto', 150);

            $table->enum('categoria', [
                'combustible',
                'mano_obra',
                'transporte_externo',
                'mantenimiento',
                'peajes',
                'otros'
            ]);

            $table->enum('clasificacion', [
                'directo',
                'indirecto'
            ]);

            $table->decimal('monto', 15, 2);

            $table->text('descripcion')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('costos_servicio');
    }
};
