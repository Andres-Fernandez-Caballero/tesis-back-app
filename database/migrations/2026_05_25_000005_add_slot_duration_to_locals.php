<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locals', function (Blueprint $table) {
            $table->unsignedSmallInteger('slot_duration_minutes')
                  ->default(60)
                  ->after('status')
                  ->comment('Duración fija de cada turno en minutos');
        });
    }

    public function down(): void
    {
        Schema::table('locals', function (Blueprint $table) {
            $table->dropColumn('slot_duration_minutes');
        });
    }
};
