<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');
            $table->string('nombre_comercial')->nullable();
            $table->string('representante_legal')->nullable();
            $table->string('nit')->nullable();
            $table->text('actividad_economica')->nullable();
            $table->text('direccion')->nullable();

            $table->string('moneda', 3)->default('USD');
            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};