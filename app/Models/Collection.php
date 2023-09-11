<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Idea;
use App\Models\User;

class Collection extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title'
    ];

    public function ideas(): HasMany
    {
        return $this->hasMany(Idea::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
