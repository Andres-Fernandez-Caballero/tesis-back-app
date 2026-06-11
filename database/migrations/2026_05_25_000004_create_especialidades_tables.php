<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de especialidades por local
        Schema::create('especialidades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('local_id');
            $table->foreign('local_id')->references('id')->on('locals')->cascadeOnDelete();
            $table->string('nombre');
            $table->timestamps();

            $table->unique(['local_id', 'nombre']);
        });

        // Tabla pivot masajista ↔ especialidad
        Schema::create('especialidad_therapist', function (Blueprint $table) {
            $table->unsignedBigInteger('especialidad_id');
            $table->unsignedBigInteger('therapist_id');

            $table->foreign('especialidad_id')->references('id')->on('especialidades')->cascadeOnDelete();
            $table->foreign('therapist_id')->references('id')->on('therapists')->cascadeOnDelete();

            $table->primary(['especialidad_id', 'therapist_id']);
        });

        // Eliminar la columna de texto libre que quedó obsoleta
        Schema::table('therapists', function (Blueprint $table) {
            $table->dropColumn('especialidades');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('especialidad_therapist');
        Schema::dropIfExists('especialidades');

        Schema::table('therapists', function (Blueprint $table) {
            $table->text('especialidades')->nullable()->after('nombre');
        });
    }
};
