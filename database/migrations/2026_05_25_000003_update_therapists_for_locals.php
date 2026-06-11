<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('therapists', function (Blueprint $table) {
            // Asociar masajistas a un local
            $table->unsignedBigInteger('local_id')->nullable()->after('user_id');
            $table->foreign('local_id')->references('id')->on('locals')->nullOnDelete();

            // Campos para masajistas gestionados por el dueño del local
            $table->string('nombre')->nullable()->after('local_id');
            $table->text('especialidades')->nullable()->after('nombre');
            $table->text('descripcion')->nullable()->after('especialidades');
            $table->string('foto_url')->nullable()->after('descripcion');
            $table->boolean('activo')->default(true)->after('foto_url');

            // Hacer nullable los campos de certificado (no aplican a masajistas de locales)
            $table->string('certificate_file')->nullable()->change();
            $table->string('certificate_file_name')->nullable()->change();

            // Hacer nullable user_id (masajistas de locales no necesitan cuenta de usuario)
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('therapists', function (Blueprint $table) {
            $table->dropForeign(['local_id']);
            $table->dropColumn(['local_id', 'nombre', 'especialidades', 'descripcion', 'foto_url', 'activo']);
            $table->string('certificate_file')->nullable(false)->change();
            $table->string('certificate_file_name')->nullable(false)->change();
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
