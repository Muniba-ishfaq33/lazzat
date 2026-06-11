<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroceryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'measure',
        'source',
        'checked',
    ];

    protected function casts(): array
    {
        return [
            'checked' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
