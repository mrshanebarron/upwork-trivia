<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class DailySubmissionsChart extends ChartWidget
{
    protected ?string $heading = 'Submissions & Scans (Last 7 Days)';

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        $submissions = [];
        $scans = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('M j');

            $submissions[] = \App\Models\Submission::whereDate('submitted_at', $date)->count();
            $scans[] = \App\Models\StickerScan::whereDate('scanned_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Submissions',
                    'data' => $submissions,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
                [
                    'label' => 'QR Scans',
                    'data' => $scans,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
