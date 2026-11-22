<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activos_fijos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')
                ->constrained('empresas')
                ->restrictOnDelete();

            $table->foreignId('evidencia_id')
                ->nullable()
                ->constrained('evidencias')
                ->nullOnDelete();

            $table->string('codigo', 30);
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();

            $table->enum('categoria', [
                'vehiculo',
                'equipo',
                'mobiliario',
                'maquinaria',
                'otro'
            ]);

            $table->date('fecha_adquisicion');

            $table->decimal('costo_adquisicion', 15, 2);

            $table->decimal('valor_residual', 15, 2)
                ->default(0);

            $table->unsignedInteger('vida_util_anios');

            $table->enum('metodo_depreciacion', [
                'linea_recta',
                'suma_digitos',
                'doble_saldo_decreciente'
            ])->default('linea_recta');

            $table->enum('estado', [
                'activo',
                'en_mantenimiento',
                'dado_de_baja'
            ])->default('activo');

            $table->timestamps();

            $table->unique(['empresa_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activos_fijos');
    }
};
