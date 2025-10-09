<?php

namespace App\Services;

use App\Models\Sticker;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Generate a new sticker with QR code
     */
    public function generateSticker(array $data): Sticker
    {
        $sticker = Sticker::create([
            'unique_code' => $data['unique_code'] ?? Str::random(12),
            'location_name' => $data['location_name'] ?? null,
            'property_name' => $data['property_name'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'installed_at' => $data['installed_at'] ?? now(),
            'status' => 'active',
        ]);

        return $sticker;
    }

    /**
     * Generate QR code image
     */
    public function generateQrImage(Sticker $sticker, int $size = 300): string
    {
        $url = route('scan', ['code' => $sticker->unique_code]);

        return QrCode::size($size)
            ->format('svg')
            ->generate($url);
    }

    /**
     * Generate batch of stickers
     */
    public function generateBatch(int $count, array $baseData = []): array
    {
        $stickers = [];

        for ($i = 0; $i < $count; $i++) {
            $stickers[] = $this->generateSticker(array_merge($baseData, [
                'unique_code' => Str::random(12),
            ]));
        }

        return $stickers;
    }

    /**
     * Get printable QR codes for batch
     */
    public function getPrintableBatch(array $stickers): array
    {
        return collect($stickers)->map(function ($sticker) {
            return [
                'sticker' => $sticker,
                'qr_code' => $this->generateQrImage($sticker),
                'url' => route('scan', ['code' => $sticker->unique_code]),
            ];
        })->toArray();
    }
}
