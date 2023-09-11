<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Idea;

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
}
