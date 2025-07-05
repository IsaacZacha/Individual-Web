<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculo', function (Blueprint $table) {
            $table->id('id_vehiculo');
            $table->string('placa')->unique();
            $table->string('marca');
            $table->string('modelo');
            $table->integer('anio');
            $table->string('tipo_id');
            $table->string('estado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculo');
    }
};