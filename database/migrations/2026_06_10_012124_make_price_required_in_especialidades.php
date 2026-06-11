<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rellenar nulls con 2000 antes de cambiar a NOT NULL
        DB::table('especialidades')
            ->whereNull('price')
            ->update(['price' => 2000.00]);

        // 2. Cambiar la columna a NOT NULL con default 2000
        Schema::table('especialidades', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(2000)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('especialidades', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->change();
        });
    }
};
