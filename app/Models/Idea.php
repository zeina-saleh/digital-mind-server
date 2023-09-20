<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Like;
use App\Models\Collection;
use App\Models\TextResource;
use App\Models\FileResource;
use App\Models\Meeting;

class Idea extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
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

    public function text_res(): HasMany
    {
        return $this->hasMany(TextResource::class);
    }

    public function file_res(): HasMany
    {
        return $this->hasMany(FileResource::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }
}
