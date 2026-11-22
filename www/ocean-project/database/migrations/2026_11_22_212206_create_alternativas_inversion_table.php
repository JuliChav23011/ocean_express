<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alternativas_inversion', function (Blueprint $table) {
            $table->id();

            $table->foreignId('estudio_inversion_id')
                ->constrained('estudios_inversion')
                ->restrictOnDelete();

            $table->foreignId('evidencia_id')
                ->nullable()
                ->constrained('evidencias')
                ->nullOnDelete();

            $table->string('codigo', 20);
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();

            $table->enum('tipo', [
                'compra_activo',
                'contratacion_servicio',
                'otra'
            ]);

            $table->decimal('inversion_inicial', 15, 2)
                ->default(0);

            $table->decimal('costo_operativo_anual', 15, 2)
                ->default(0);

            $table->decimal('ingreso_anual_estimado', 15, 2)
                ->default(0);

            $table->decimal('valor_residual', 15, 2)
                ->default(0);

            $table->unsignedInteger('vida_util_anios')
                ->nullable();

            $table->enum('metodo_depreciacion', [
                'linea_recta',
                'suma_digitos',
                'doble_saldo_decreciente',
                'no_aplica'
            ])->default('no_aplica');

            $table->enum('clasificacion_datos', [
                'real',
                'estimado',
                'mixto'
            ])->default('estimado');

            $table->text('justificacion')->nullable();
            $table->boolean('activa')->default(true);

            $table->timestamps();

            $table->unique([
                'estudio_inversion_id',
                'codigo'
            ], 'alternativa_estudio_codigo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alternativas_inversion');
    }
};
