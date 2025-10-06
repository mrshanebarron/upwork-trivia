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

    protected $casts = [
        'trivia_code_id' => 'integer',
        'order' => 'integer',
    ];

    public function triviaCode(): BelongsTo
    {
        return $this->belongsTo(TriviaCode::class);
    }
}
