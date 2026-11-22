<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas_contables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')
                ->constrained('empresas')
                ->restrictOnDelete();

            $table->foreignId('cuenta_padre_id')
                ->nullable()
                ->constrained('cuentas_contables')
                ->restrictOnDelete();

            $table->string('codigo', 30);
            $table->string('nombre', 200);

            $table->enum('tipo', [
                'activo',
                'pasivo',
                'patrimonio',
                'ingreso',
                'costo',
                'gasto'
            ]);

            $table->enum('naturaleza', [
                'deudora',
                'acreedora'
            ]);

            $table->boolean('permite_movimientos')
                ->default(true);

            $table->boolean('activa')
                ->default(true);

            $table->timestamps();

            $table->unique(['empresa_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_contables');
    }
};
