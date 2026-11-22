<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudios_inversion', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')
                ->constrained('empresas')
                ->restrictOnDelete();

            $table->string('codigo', 30);
            $table->string('nombre', 200);

            $table->text('descripcion')->nullable();
            $table->text('objetivo')->nullable();

            $table->date('fecha_estudio');

            $table->unsignedInteger('horizonte_anios');

            $table->decimal('tasa_descuento', 8, 4);

            $table->decimal('tasa_impuesto', 8, 4)
                ->default(0);

            $table->decimal('tasa_inflacion', 8, 4)
                ->default(0);

            $table->string('moneda', 3)->default('USD');

            $table->enum('estado', [
                'borrador',
                'en_evaluacion',
                'finalizado'
            ])->default('borrador');

            $table->text('supuestos')->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->unique(['empresa_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudios_inversion');
    }
};
