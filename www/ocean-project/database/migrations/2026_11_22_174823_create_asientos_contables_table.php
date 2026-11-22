<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asientos_contables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')
                ->constrained('empresas')
                ->restrictOnDelete();

            $table->foreignId('periodo_contable_id')
                ->constrained('periodos_contables')
                ->restrictOnDelete();

            $table->string('numero_asiento', 30);
            $table->date('fecha');
            $table->text('concepto');

            $table->enum('tipo', [
                'apertura',
                'normal',
                'ajuste',
                'cierre'
            ])->default('normal');

            $table->enum('estado', [
                'borrador',
                'contabilizado',
                'anulado'
            ])->default('borrador');

            $table->string('referencia_documento')->nullable();
            $table->timestamps();

            $table->unique([
                'empresa_id',
                'periodo_contable_id',
                'numero_asiento'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asientos_contables');
    }
};
