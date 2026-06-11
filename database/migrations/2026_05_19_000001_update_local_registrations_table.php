<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('local_registrations', function (Blueprint $table) {
            // Nuevos campos requeridos
            $table->string('direccion')->after('nombre_local');
            $table->string('cuit')->after('direccion');
            $table->text('descripcion')->nullable()->after('cuit');

            // Campos anteriores que ya no son obligatorios
            $table->string('nombre')->nullable()->change();
            $table->string('apellido')->nullable()->change();
            $table->string('instagram')->nullable()->change();
            $table->string('localidad')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('local_registrations', function (Blueprint $table) {
            $table->dropColumn(['direccion', 'cuit', 'descripcion']);

            $table->string('nombre')->nullable(false)->change();
            $table->string('apellido')->nullable(false)->change();
            $table->string('localidad')->nullable(false)->change();
        });
    }
};
