<?php

namespace App\Http\Controllers;

use App\Models\Sticker;
use App\Models\StickerScan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ScanController extends Controller
{
    /**
     * Handle QR code scan
     */
    public function scan(Request $request, string $code): RedirectResponse
    {
        $sticker = Sticker::where('unique_code', $code)
            ->where('status', 'active')
            ->firstOrFail();

        // Log the scan for analytics
        $this->logScan($request, $sticker);

        // Redirect to contest page with sticker code
        return redirect()->route('contest.show', ['code' => $code]);
    }

    /**
     * Log sticker scan for analytics
     */
    protected function logScan(Request $request, Sticker $sticker): void
    {
        StickerScan::create([
            'sticker_id' => $sticker->id,
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'scan_latitude' => $request->input('latitude'),
            'scan_longitude' => $request->input('longitude'),
            'scanned_at' => now(),
        ]);
    }

    /**
     * Show sticker details (admin preview)
     */
    public function show(Sticker $sticker): Response
    {
        return Inertia::render('Scan/Show', [
            'sticker' => $sticker,
            'scans_count' => $sticker->scans()->count(),
            'recent_scans' => $sticker->scans()
                ->with('user')
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }
}
