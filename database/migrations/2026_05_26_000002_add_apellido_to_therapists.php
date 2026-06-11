<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('therapists', function (Blueprint $table) {
            $table->string('apellido')->nullable()->after('nombre');
            $table->string('email')->nullable()->after('apellido');
            $table->string('dni')->nullable()->after('email');
            $table->string('telefono_propio')->nullable()->after('dni');
        });
    }

    public function down(): void
    {
        Schema::table('therapists', function (Blueprint $table) {
            $table->dropColumn(['apellido', 'email', 'dni', 'telefono_propio']);
        });
    }
};
