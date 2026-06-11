<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locals', function (Blueprint $table) {
            // decimal(10,7) admite hasta ±180.0000000 con 7 decimales de precisión (~1 cm)
            $table->decimal('latitude',  10, 7)->nullable()->after('localidad');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('locals', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
