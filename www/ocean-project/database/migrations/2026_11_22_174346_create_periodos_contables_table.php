<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodos_contables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')
                ->constrained('empresas')
                ->restrictOnDelete();

            $table->string('nombre', 100);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');

            $table->enum('estado', [
                'abierto',
                'cerrado'
            ])->default('abierto');

            $table->timestamps();

            $table->unique([
                'empresa_id',
                'fecha_inicio',
                'fecha_fin'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodos_contables');
    }
};
