<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\Attributes\Test;

use App\Models\Sticker;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrCodeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected QrCodeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new QrCodeService();
    }

    #[Test]
    public function it_generates_sticker_with_unique_code()
    {
        $sticker = $this->service->generateSticker([
            'location_name' => 'Test Location',
            'property_name' => 'Test Property',
        ]);

        $this->assertInstanceOf(Sticker::class, $sticker);
        $this->assertNotNull($sticker->unique_code);
        $this->assertEquals('Test Location', $sticker->location_name);
        $this->assertEquals('Test Property', $sticker->property_name);
        $this->assertEquals('active', $sticker->status);
    }

    #[Test]
    public function it_uses_provided_unique_code()
    {
        $customCode = 'CUSTOM_CODE_123';

        $sticker = $this->service->generateSticker([
            'unique_code' => $customCode,
            'location_name' => 'Test Location',
        ]);

        $this->assertEquals($customCode, $sticker->unique_code);
    }

    #[Test]
    public function it_generates_qr_code_image()
    {
        $sticker = $this->service->generateSticker(['location_name' => 'Test']);

        $qrCode = $this->service->generateQrImage($sticker);

        $this->assertIsString($qrCode);
        $this->assertStringContainsString('svg', $qrCode);
    }

    #[Test]
    public function it_generates_qr_code_with_custom_size()
    {
        $sticker = $this->service->generateSticker(['location_name' => 'Test']);

        $qrCode = $this->service->generateQrImage($sticker, 500);

        $this->assertIsString($qrCode);
    }

    #[Test]
    public function it_generates_batch_of_stickers()
    {
        $count = 10;
        $stickers = $this->service->generateBatch($count, [
            'location_name' => 'Batch Location',
            'property_name' => 'Batch Property',
        ]);

        $this->assertCount($count, $stickers);
        $this->assertEquals($count, Sticker::count());

        foreach ($stickers as $sticker) {
            $this->assertEquals('Batch Location', $sticker->location_name);
            $this->assertEquals('Batch Property', $sticker->property_name);
        }
    }

    #[Test]
    public function batch_stickers_have_unique_codes()
    {
        $stickers = $this->service->generateBatch(5);

        $codes = collect($stickers)->pluck('unique_code')->toArray();
        $uniqueCodes = array_unique($codes);

        $this->assertCount(5, $uniqueCodes);
    }

    #[Test]
    public function it_generates_printable_batch()
    {
        $stickers = $this->service->generateBatch(3);

        $printable = $this->service->getPrintableBatch($stickers);

        $this->assertCount(3, $printable);

        foreach ($printable as $item) {
            $this->assertArrayHasKey('sticker', $item);
            $this->assertArrayHasKey('qr_code', $item);
            $this->assertArrayHasKey('url', $item);
            $this->assertInstanceOf(Sticker::class, $item['sticker']);
            $this->assertIsString($item['qr_code']);
            $this->assertIsString($item['url']);
        }
    }

    #[Test]
    public function printable_batch_urls_contain_sticker_codes()
    {
        $stickers = $this->service->generateBatch(2);
        $printable = $this->service->getPrintableBatch($stickers);

        foreach ($printable as $item) {
            $this->assertStringContainsString($item['sticker']->unique_code, $item['url']);
        }
    }
}
