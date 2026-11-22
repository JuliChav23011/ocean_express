<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalles_asientos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asiento_contable_id')
                ->constrained('asientos_contables')
                ->cascadeOnDelete();

            $table->foreignId('cuenta_contable_id')
                ->constrained('cuentas_contables')
                ->restrictOnDelete();

            $table->text('descripcion')->nullable();

            $table->decimal('debe', 15, 2)->default(0);
            $table->decimal('haber', 15, 2)->default(0);

            $table->timestamps();

            $table->index('cuenta_contable_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_asientos');
    }
};
