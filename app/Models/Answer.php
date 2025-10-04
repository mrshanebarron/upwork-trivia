<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    protected $fillable = [
        'trivia_code_id',
        'order',
        'answer',
    ];

    public function triviaCode(): BelongsTo
    {
        return $this->belongsTo(TriviaCode::class);
    }
}
