<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flujos_efectivo', function (Blueprint $table) {
            $table->id();

            $table->foreignId('alternativa_inversion_id')
                ->constrained('alternativas_inversion')
                ->restrictOnDelete();

            $table->foreignId('evidencia_id')
                ->nullable()
                ->constrained('evidencias')
                ->nullOnDelete();

            $table->unsignedInteger('periodo');

            $table->enum('tipo', [
                'ingreso',
                'egreso',
                'inversion',
                'valor_residual',
                'recuperacion_capital'
            ]);

            $table->string('concepto', 200);

            $table->text('descripcion')->nullable();

            $table->decimal('monto', 15, 2);

            $table->enum('clasificacion_dato', [
                'real',
                'estimado',
                'calculado'
            ])->default('estimado');

            $table->text('justificacion')->nullable();

            $table->date('fecha_referencia')->nullable();

            $table->timestamps();

            $table->index([
                'alternativa_inversion_id',
                'periodo'
            ], 'flujos_alternativa_periodo_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flujos_efectivo');
    }
};
