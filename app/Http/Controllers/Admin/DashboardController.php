<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdBox;
use App\Models\CodeView;
use App\Models\TriviaCode;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_visitors' => CodeView::distinct('ip_address')->count('ip_address'),
            'views_today' => CodeView::whereDate('created_at', today())->count(),
            'active_codes' => TriviaCode::where('is_active', true)->count(),
            'total_ad_clicks' => AdBox::sum('click_count') ?? 0,
        ];

        $recent_views = CodeView::with('triviaCode')
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_views'));
    }

    public function exportAnalytics()
    {
        $views = CodeView::with('triviaCode')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'analytics-export-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($views) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, [
                'Date/Time',
                'Code',
                'Code Title',
                'IP Address',
                'User Agent'
            ]);

            // CSV Data
            foreach ($views as $view) {
                fputcsv($file, [
                    $view->created_at ? $view->created_at->format('Y-m-d H:i:s') : 'N/A',
                    $view->triviaCode->code ?? 'N/A',
                    $view->triviaCode->title ?? 'N/A',
                    $view->ip_address ?? 'N/A',
                    $view->user_agent ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
