<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Idea;
use App\Models\Message;
use App\Models\User;

class Discussion extends Model
{
    use HasFactory;
    protected $fillable = [
        'idea_id',
        'user_id',
        'title',
    ];

    public function idea(): BelongsTo
    {
        return $this->belongsTo(Idea::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
