<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Like;

class Idea extends Model
{
    use HasFactory;
    protected $fillable = [
        'resource_id',
        'collection_id'
    ];
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }
}
