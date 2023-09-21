<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meeting extends Model
{
    use HasFactory;
    protected $fillable = [
        'idea_id',
        'user_id',
        'title',
        'datetime',
        'latitude',
        'longitude',
    ];
    
    public function idea(): BelongsTo
    {
        return $this->belongsTo(Idea::class);
    }
}
