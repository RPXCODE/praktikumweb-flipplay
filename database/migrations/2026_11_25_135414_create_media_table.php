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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->string('type'); // 'movie' atau 'music'
            $table->string('genre');
            $table->integer('duration_minutes');
            $table->integer('release_year');
            // Atribut spesifik Movie
            $table->string('director')->nullable();
            $table->string('age_rating')->nullable()->default('PG-13');
            // Atribut spesifik Music
            $table->string('artist')->nullable();
            $table->string('album')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
