<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'day_index',
        'meal_index',
        'mealdb_id',
        'name',
        'thumbnail',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'day_index' => 'integer',
            'meal_index' => 'integer',
            'payload' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
