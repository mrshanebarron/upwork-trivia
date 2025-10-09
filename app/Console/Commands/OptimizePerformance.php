<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class OptimizePerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:optimize-performance
                            {--warm-cache : Warm up application caches}
                            {--clear-cache : Clear all caches first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize application performance (caching, config, routes, views)';

    /**
     * Execute the console command.
     */
    public function handle(CacheService $cacheService): int
    {
        $this->info('🚀 Starting performance optimization...');
        $this->newLine();

        // Clear caches if requested
        if ($this->option('clear-cache')) {
            $this->clearCaches();
        }

        // Optimize configuration
        $this->optimizeConfig();

        // Optimize routes
        $this->optimizeRoutes();

        // Optimize views
        $this->optimizeViews();

        // Optimize events
        $this->optimizeEvents();

        // Warm up caches if requested
        if ($this->option('warm-cache')) {
            $this->warmCaches($cacheService);
        }

        $this->newLine();
        $this->info('✅ Performance optimization complete!');
        $this->newLine();

        $this->displayRecommendations();

        return Command::SUCCESS;
    }

    /**
     * Clear all application caches
     */
    protected function clearCaches(): void
    {
        $this->task('Clearing application cache', fn() => Artisan::call('cache:clear'));
        $this->task('Clearing config cache', fn() => Artisan::call('config:clear'));
        $this->task('Clearing route cache', fn() => Artisan::call('route:clear'));
        $this->task('Clearing view cache', fn() => Artisan::call('view:clear'));
        $this->newLine();
    }

    /**
     * Optimize configuration caching
     */
    protected function optimizeConfig(): void
    {
        $this->task('Caching configuration', fn() => Artisan::call('config:cache'));
    }

    /**
     * Optimize route caching
     */
    protected function optimizeRoutes(): void
    {
        $this->task('Caching routes', fn() => Artisan::call('route:cache'));
    }

    /**
     * Optimize view compilation
     */
    protected function optimizeViews(): void
    {
        $this->task('Caching views', fn() => Artisan::call('view:cache'));
    }

    /**
     * Optimize event discovery
     */
    protected function optimizeEvents(): void
    {
        $this->task('Caching events', fn() => Artisan::call('event:cache'));
    }

    /**
     * Warm up application caches
     */
    protected function warmCaches(CacheService $cacheService): void
    {
        $this->newLine();
        $this->info('🔥 Warming up caches...');
        $this->newLine();

        $this->task('Warming critical caches', function () use ($cacheService) {
            $cacheService->warmUpCaches();
            return true;
        });
    }

    /**
     * Display performance recommendations
     */
    protected function displayRecommendations(): void
    {
        $this->info('💡 Performance Recommendations:');
        $this->newLine();

        $recommendations = [
            'Production' => [
                '• Ensure APP_DEBUG=false in .env',
                '• Use Redis for cache and sessions (CACHE_STORE=redis, SESSION_DRIVER=redis)',
                '• Enable opcache in php.ini (opcache.enable=1)',
                '• Use MySQL instead of SQLite for production (DB_CONNECTION=mysql)',
                '• Configure queue workers (QUEUE_CONNECTION=redis)',
                '• Enable HTTPS and HTTP/2',
            ],
            'Monitoring' => [
                '• Monitor queue workers with Horizon or Supervisor',
                '• Set up application monitoring (New Relic, DataDog, etc.)',
                '• Review Flare error logs regularly',
                '• Monitor Redis memory usage',
            ],
            'Scaling' => [
                '• Consider Laravel Octane for high traffic (swoole/roadrunner)',
                '• Use CDN for static assets',
                '• Implement database read replicas if needed',
                '• Add rate limiting to API endpoints',
            ],
        ];

        foreach ($recommendations as $category => $items) {
            $this->comment($category . ':');
            foreach ($items as $item) {
                $this->line($item);
            }
            $this->newLine();
        }
    }
}
