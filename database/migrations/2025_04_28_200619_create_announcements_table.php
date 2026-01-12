<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->integer('scoring')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('title');
            $table->string('description');
            $table->float('price')->default(0);
            $table->integer('durationInMinutes');
            $table->string('currency')->default('ARG');
            $table->foreignId('therapist_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
