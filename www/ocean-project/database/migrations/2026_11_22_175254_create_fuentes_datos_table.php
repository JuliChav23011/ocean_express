<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuentes_datos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')
                ->constrained('empresas')
                ->restrictOnDelete();

            $table->string('nombre_dato');
            $table->text('descripcion')->nullable();

            $table->decimal('valor', 18, 4)->nullable();
            $table->string('unidad', 30)->default('USD');

            $table->enum('clasificacion', [
                'real',
                'estimado',
                'calculado'
            ]);

            $table->text('justificacion')->nullable();

            $table->date('fecha_referencia')->nullable();

            $table->timestamps();
        });

        Schema::create('evidencias_fuentes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('fuente_dato_id')
                ->constrained('fuentes_datos')
                ->cascadeOnDelete();

            $table->foreignId('evidencia_id')
                ->constrained('evidencias')
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique([
                'fuente_dato_id',
                'evidencia_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencias_fuentes');
        Schema::dropIfExists('fuentes_datos');
    }
};
