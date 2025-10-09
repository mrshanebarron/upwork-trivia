<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Sticker extends Model
{
    use HasFactory;
    protected $fillable = [
        'unique_code',
        'location_name',
        'property_name',
        'property_manager_id',
        'latitude',
        'longitude',
        'installed_at',
        'status',
        'scan_count',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'installed_at' => 'date',
            'scan_count' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        // Auto-generate unique code on creation
        static::creating(function ($sticker) {
            if (!$sticker->unique_code) {
                $sticker->unique_code = Str::random(12);
            }
        });
    }

    /**
     * Generate QR code URL
     */
    public function getQrUrl(): string
    {
        return route('scan', ['code' => $this->unique_code]);
    }

    /**
     * Increment scan count
     */
    public function incrementScans(): void
    {
        $this->increment('scan_count');
    }

    // Relationships
    public function scans(): HasMany
    {
        return $this->hasMany(StickerScan::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
