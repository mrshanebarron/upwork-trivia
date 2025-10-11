<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BagSubmission extends Model
{
    protected $fillable = [
        'trivia_code_id',
        'user_id',
        'answer',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'trivia_code_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function triviaCode(): BelongsTo
    {
        return $this->belongsTo(TriviaCode::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
