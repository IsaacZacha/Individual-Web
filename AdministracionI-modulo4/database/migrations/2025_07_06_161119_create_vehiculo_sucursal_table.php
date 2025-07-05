<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculo_sucursal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_vehiculo');
            $table->unsignedBigInteger('id_sucursal');
            $table->timestamp('fecha_asignacion')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_vehiculo')->references('id_vehiculo')->on('vehiculo')->onDelete('cascade');
            $table->foreign('id_sucursal')->references('id_sucursal')->on('sucursal')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculo_sucursal');
    }
};