<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TextResource extends Model
{
    use HasFactory;
    protected $fillable = [
        'idea_id',
        'type_id',
        'text',
        'caption'
    ];

    public function types(): HasOne
    {
        return $this->hasOne(Type::class);
    }
}
