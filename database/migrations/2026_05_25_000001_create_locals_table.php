<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locals', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_local');
            $table->string('direccion');
            $table->string('cuit');
            $table->string('telefono');
            $table->string('email');
            $table->text('descripcion')->nullable();
            $table->string('localidad')->nullable();
            $table->string('instagram')->nullable();
            $table->string('status')->default('active');
            $table->unsignedBigInteger('local_registration_id')->nullable();
            $table->foreign('local_registration_id')
                  ->references('id')
                  ->on('local_registrations')
                  ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locals');
    }
};
