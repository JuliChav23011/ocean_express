<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones_economicas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('alternativa_inversion_id')
                ->constrained('alternativas_inversion')
                ->restrictOnDelete();

            $table->date('fecha_evaluacion');

            $table->enum('tipo_evaluacion', [
                'antes_impuestos',
                'despues_impuestos'
            ]);

            $table->decimal('tasa_descuento', 8, 4);

            $table->unsignedInteger('horizonte_anios');

            $table->decimal('vpn', 18, 2)->nullable();

            $table->decimal('tir', 10, 4)->nullable();

            $table->decimal('caue', 18, 2)->nullable();

            $table->decimal('relacion_beneficio_costo', 12, 4)
                ->nullable();

            $table->decimal('periodo_recuperacion', 10, 4)
                ->nullable();

            $table->enum('metodo_depreciacion', [
                'linea_recta',
                'suma_digitos',
                'doble_saldo_decreciente',
                'no_aplica'
            ])->default('no_aplica');

            $table->decimal('vpn_manual', 18, 2)->nullable();

            $table->decimal('tir_manual', 10, 4)->nullable();

            $table->decimal('caue_manual', 18, 2)->nullable();

            $table->decimal('bc_manual', 12, 4)->nullable();

            $table->boolean('verificacion_correcta')
                ->default(false);

            $table->text('observaciones_verificacion')
                ->nullable();

            $table->text('conclusion')->nullable();

            $table->boolean('recomendada')->default(false);

            $table->timestamps();

            $table->index('alternativa_inversion_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones_economicas');
    }
};
