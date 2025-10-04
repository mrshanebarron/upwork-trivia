<?php

namespace App\Filament\Widgets;

use App\Models\AdBox;
use App\Models\CodeView;
use App\Models\TriviaCode;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalViews = CodeView::count();
        $totalCodes = TriviaCode::where('is_active', true)->count();
        $totalAdClicks = AdBox::sum('click_count');
        $viewsToday = CodeView::whereDate('viewed_at', today())->count();

        return [
            Stat::make('Total Views', $totalViews)
                ->description('All time code views')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Views Today', $viewsToday)
                ->description('Views in last 24 hours')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),

            Stat::make('Active Codes', $totalCodes)
                ->description('Currently available')
                ->descriptionIcon('heroicon-m-key')
                ->color('warning'),

            Stat::make('Ad Clicks', $totalAdClicks)
                ->description('Total advertisement clicks')
                ->descriptionIcon('heroicon-m-cursor-arrow-rays')
                ->color('danger'),
        ];
    }
}
