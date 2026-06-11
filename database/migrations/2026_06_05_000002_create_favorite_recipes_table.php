<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorite_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('mealdb_id', 40);
            $table->string('name');
            $table->string('thumbnail', 1000)->nullable();
            $table->string('category', 120)->nullable();
            $table->string('area', 120)->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'mealdb_id']);
            $table->index('mealdb_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorite_recipes');
    }
};
