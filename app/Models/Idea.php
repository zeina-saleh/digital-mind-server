<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Like;
use App\Models\Collection;

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

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }
}
