<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Resource;

class Type extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
    ];

    public function resources(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }
}
