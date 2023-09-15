<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\FileResource;
use App\Models\TextResource;

class Type extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
    ];

    public function files(): BelongsTo
    {
        return $this->belongsTo(FileResource::class);
    }

    public function texts(): BelongsTo
    {
        return $this->belongsTo(TextResource::class);
    }
}
