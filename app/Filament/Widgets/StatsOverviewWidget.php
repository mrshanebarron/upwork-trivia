<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseStatsOverviewWidget
{
    protected function getStats(): array
    {
        $today = now()->toDateString();
        $currentMonth = now()->startOfMonth()->toDateString();

        $todaySubmissions = \App\Models\Submission::whereDate('submitted_at', $today)->count();
        $todayWinners = \App\Models\Winner::whereDate('created_at', $today)->count();
        $todayScans = \App\Models\StickerScan::whereDate('scanned_at', $today)->count();

        $prizePool = \App\Models\PrizePool::where('month', $currentMonth)->first();
        $remaining = $prizePool?->remaining ?? 0;

        return [
            Stat::make('Today\'s Submissions', $todaySubmissions)
                ->description('Total answers submitted today')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),

            Stat::make('Today\'s Winners', $todayWinners)
                ->description('Winners today')
                ->descriptionIcon('heroicon-o-trophy')
                ->color('warning'),

            Stat::make('Today\'s QR Scans', $todayScans)
                ->description('Sticker scans today')
                ->descriptionIcon('heroicon-o-qr-code')
                ->color('info'),

            Stat::make('Prize Pool Balance', '$' . number_format($remaining, 2))
                ->description($prizePool ? 'Spent: $' . number_format($prizePool->spent, 2) : 'No pool active')
                ->descriptionIcon($remaining < 100 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-banknotes')
                ->color($remaining < 100 ? 'danger' : 'success'),
        ];
    }
}
