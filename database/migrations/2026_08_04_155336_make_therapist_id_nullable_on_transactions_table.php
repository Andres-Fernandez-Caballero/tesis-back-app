<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Los masajistas gestionados por un local (flujo de reservas por local) no
     * tienen cuenta de usuario propia, por lo que therapist_id no siempre puede
     * apuntar a un usuario.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['therapist_id']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('therapist_id')->nullable()->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('therapist_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['therapist_id']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('therapist_id')->nullable(false)->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('therapist_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
