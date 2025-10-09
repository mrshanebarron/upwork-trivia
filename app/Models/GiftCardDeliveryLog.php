<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiftCardDeliveryLog extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'gift_card_id',
        'attempt_number',
        'status',
        'error_message',
        'api_response',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'api_response' => 'array',
            'attempted_at' => 'datetime',
        ];
    }

    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class);
    }
}
