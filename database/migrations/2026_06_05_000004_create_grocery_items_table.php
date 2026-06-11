<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grocery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('measure', 120)->nullable();
            $table->string('source')->nullable();
            $table->boolean('checked')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'checked']);
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grocery_items');
    }
};
