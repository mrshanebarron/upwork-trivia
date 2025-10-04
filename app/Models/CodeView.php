<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodeView extends Model
{
    protected $fillable = [
        'trivia_code_id',
        'ip_address',
        'user_agent',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function triviaCode(): BelongsTo
    {
        return $this->belongsTo(TriviaCode::class);
    }
}
