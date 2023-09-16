<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Type;
use App\Models\Idea;

class FileResource extends Model
{
    use HasFactory;
    protected $fillable = [
        'idea_id',
        'type_id',
        'caption',
        'path',
    ];

    public function types(): HasOne
    {
        return $this->hasOne(Type::class);
    }

    public function idea(): BelongsTo
    {
        return $this->belongsTo(Idea::class);
    }
}
