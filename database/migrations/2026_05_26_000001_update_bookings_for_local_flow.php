<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Make announcement_id optional for local-based bookings
            $table->unsignedBigInteger('announcement_id')->nullable()->change();

            // Track which local and specialty the booking belongs to
            $table->foreignId('local_id')
                ->nullable()
                ->after('announcement_id')
                ->constrained('locals')
                ->nullOnDelete();

            $table->foreignId('especialidad_id')
                ->nullable()
                ->after('local_id')
                ->constrained('especialidades')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['local_id']);
            $table->dropForeign(['especialidad_id']);
            $table->dropColumn(['local_id', 'especialidad_id']);
            $table->unsignedBigInteger('announcement_id')->nullable(false)->change();
        });
    }
};
