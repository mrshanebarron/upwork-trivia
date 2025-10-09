<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class StickerScan extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'sticker_id',
        'user_id',
        'ip_address',
        'user_agent',
        'scan_latitude',
        'scan_longitude',
        'scanned_at',
    ];

    protected function casts(): array
    {
        return [
            'scan_latitude' => 'decimal:8',
            'scan_longitude' => 'decimal:8',
            'scanned_at' => 'datetime',
        ];
    }

    /**
     * Accessor for latitude (maps to scan_latitude)
     */
    protected function latitude(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->scan_latitude,
            set: fn ($value) => ['scan_latitude' => $value]
        );
    }

    /**
     * Accessor for longitude (maps to scan_longitude)
     */
    protected function longitude(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->scan_longitude,
            set: fn ($value) => ['scan_longitude' => $value]
        );
    }

    public function sticker()
    {
        return $this->belongsTo(Sticker::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
