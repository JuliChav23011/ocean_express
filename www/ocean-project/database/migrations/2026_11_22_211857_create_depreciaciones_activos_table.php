<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depreciaciones_activos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('activo_fijo_id')
                ->constrained('activos_fijos')
                ->restrictOnDelete();

            $table->foreignId('periodo_contable_id')
                ->constrained('periodos_contables')
                ->restrictOnDelete();

            $table->date('fecha_calculo');

            $table->enum('metodo', [
                'linea_recta',
                'suma_digitos',
                'doble_saldo_decreciente'
            ]);

            $table->decimal('valor_inicial', 15, 2);

            $table->decimal('monto_depreciacion', 15, 2);

            $table->decimal('depreciacion_acumulada', 15, 2);

            $table->decimal('valor_en_libros', 15, 2);

            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->unique([
                'activo_fijo_id',
                'periodo_contable_id'
            ], 'depreciacion_activo_periodo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depreciaciones_activos');
    }
};
