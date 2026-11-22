<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidencias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')
                ->constrained('empresas')
                ->restrictOnDelete();

            $table->string('codigo', 50);
            $table->string('titulo');
            $table->text('descripcion')->nullable();

            $table->enum('tipo', [
                'factura',
                'cotizacion',
                'entrevista',
                'fotografia',
                'estado_financiero',
                'registro_contable',
                'autorizacion',
                'otro'
            ]);

            $table->date('fecha_documento')->nullable();
            $table->string('emisor')->nullable();

            $table->string('ruta_archivo')->nullable();
            $table->string('nombre_archivo')->nullable();

            $table->boolean('verificada')->default(false);
            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->unique(['empresa_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencias');
    }
};
