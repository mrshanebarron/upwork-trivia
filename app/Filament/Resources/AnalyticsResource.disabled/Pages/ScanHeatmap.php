<?php

namespace App\Filament\Resources\AnalyticsResource\Pages;

use App\Filament\Resources\AnalyticsResource;
use App\Models\QrScan;
use App\Models\Submission;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;

class ScanHeatmap extends Page
{
    protected static string $resource = AnalyticsResource::class;

    protected static string $view = 'filament.resources.analytics.pages.scan-heatmap';

    protected static ?string $title = 'Scan Location Heatmap';

    public function getViewData(): array
    {
        return [
            'scanLocations' => $this->getScanLocations(),
            'submissionLocations' => $this->getSubmissionLocations(),
            'stats' => $this->getStats(),
            'googleMapsApiKey' => config('services.google_maps.api_key'),
        ];
    }

    protected function getScanLocations(): array
    {
        return QrScan::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('latitude', 'longitude', 'scanned_at', 'sticker_id')
            ->with('sticker:id,identifier,location_name,property_name')
            ->orderBy('scanned_at', 'desc')
            ->limit(500) // Limit for performance
            ->get()
            ->map(function ($scan) {
                return [
                    'lat' => (float) $scan->latitude,
                    'lng' => (float) $scan->longitude,
                    'time' => $scan->scanned_at->format('Y-m-d H:i:s'),
                    'location' => $scan->sticker?->location_name ?? 'Unknown',
                    'property' => $scan->sticker?->property_name ?? 'Unknown',
                ];
            })
            ->toArray();
    }

    protected function getSubmissionLocations(): array
    {
        return Submission::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('latitude', 'longitude', 'created_at', 'is_correct', 'is_winner')
            ->orderBy('created_at', 'desc')
            ->limit(500) // Limit for performance
            ->get()
            ->map(function ($submission) {
                return [
                    'lat' => (float) $submission->latitude,
                    'lng' => (float) $submission->longitude,
                    'time' => $submission->created_at->format('Y-m-d H:i:s'),
                    'correct' => $submission->is_correct,
                    'winner' => $submission->is_winner,
                ];
            })
            ->toArray();
    }

    protected function getStats(): array
    {
        $totalScans = QrScan::count();
        $scansWithLocation = QrScan::whereNotNull('latitude')->whereNotNull('longitude')->count();
        $uniqueLocations = QrScan::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select(DB::raw('COUNT(DISTINCT CONCAT(ROUND(latitude, 3), ",", ROUND(longitude, 3))) as count'))
            ->value('count');

        $topLocations = QrScan::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with('sticker')
            ->select('sticker_id', DB::raw('COUNT(*) as scan_count'))
            ->groupBy('sticker_id')
            ->orderByDesc('scan_count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'location' => $item->sticker?->location_name ?? 'Unknown',
                    'property' => $item->sticker?->property_name ?? 'Unknown',
                    'scans' => $item->scan_count,
                ];
            });

        return [
            'total_scans' => $totalScans,
            'scans_with_location' => $scansWithLocation,
            'unique_locations' => $uniqueLocations,
            'location_capture_rate' => $totalScans > 0 ? round(($scansWithLocation / $totalScans) * 100, 1) : 0,
            'top_locations' => $topLocations,
        ];
    }
}
