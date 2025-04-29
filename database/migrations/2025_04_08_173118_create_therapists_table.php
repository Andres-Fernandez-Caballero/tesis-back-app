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
        Schema::create('therapists', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('certificate_file');
            $table->string('certificate_file_name');
            $table->date('certificate_file_create_date')->nullable();
            $table->date('certificate_file_expiration_date')->nullable();

            // $table->string('field_m')->nullable();
            $table->string('field_o')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapists');
    }
};
