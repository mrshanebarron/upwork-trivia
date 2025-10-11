<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TriviaCode extends Model
{
    protected $fillable = [
        'code',
        'title',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class)->orderBy('order');
    }

    public function views(): HasMany
    {
        return $this->hasMany(CodeView::class);
    }

    /**
     * Generate QR code URL for bag trivia
     */
    public function getQrUrl(): string
    {
        return route('trivia.show', ['code' => $this->code]);
    }
}
