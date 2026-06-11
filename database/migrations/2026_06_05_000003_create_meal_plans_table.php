<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('day_index');
            $table->unsignedTinyInteger('meal_index');
            $table->string('mealdb_id', 40);
            $table->string('name');
            $table->string('thumbnail', 1000)->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'day_index', 'meal_index']);
            $table->index('mealdb_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_plans');
    }
};
