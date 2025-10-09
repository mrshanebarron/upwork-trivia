<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Query Caching
    |--------------------------------------------------------------------------
    |
    | Enable query result caching for expensive database operations.
    | Cache duration is in seconds.
    |
    */

    'query_cache' => [
        'enabled' => env('QUERY_CACHE_ENABLED', true),
        'ttl' => env('QUERY_CACHE_TTL', 300), // 5 minutes default
    ],

    /*
    |--------------------------------------------------------------------------
    | Eager Loading
    |--------------------------------------------------------------------------
    |
    | Automatically eager load relationships to prevent N+1 query problems.
    |
    */

    'eager_loading' => [
        'enabled' => env('EAGER_LOADING_ENABLED', true),

        // Default relationships to load for each model
        'defaults' => [
            'User' => ['winners', 'submissions'],
            'DailyQuestion' => ['winner', 'submissions'],
            'Winner' => ['user', 'dailyQuestion', 'giftCard'],
            'Submission' => ['user', 'dailyQuestion', 'sticker'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Optimization
    |--------------------------------------------------------------------------
    |
    | Configuration for production asset optimization.
    |
    */

    'assets' => [
        // Browser cache duration in seconds (1 year)
        'cache_duration' => env('ASSET_CACHE_DURATION', 31536000),

        // Enable asset versioning for cache busting
        'versioning' => env('ASSET_VERSIONING', true),

        // Compress responses
        'compression' => env('ASSET_COMPRESSION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Connection Pool
    |--------------------------------------------------------------------------
    |
    | Configuration for database connection pooling to reduce overhead.
    |
    */

    'database_pool' => [
        'enabled' => env('DB_POOL_ENABLED', false),
        'min_connections' => env('DB_POOL_MIN', 2),
        'max_connections' => env('DB_POOL_MAX', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Response Caching
    |--------------------------------------------------------------------------
    |
    | Cache full HTTP responses for routes that don't change often.
    |
    */

    'response_cache' => [
        'enabled' => env('RESPONSE_CACHE_ENABLED', true),

        // Routes to cache (route name => TTL in seconds)
        'routes' => [
            'terms' => 86400, // 1 day
            'privacy' => 86400, // 1 day
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Optimization
    |--------------------------------------------------------------------------
    |
    | Configuration for optimizing images on upload/generation.
    |
    */

    'images' => [
        'optimize' => env('IMAGE_OPTIMIZE', true),
        'quality' => env('IMAGE_QUALITY', 85),
        'max_width' => env('IMAGE_MAX_WIDTH', 2000),
        'max_height' => env('IMAGE_MAX_HEIGHT', 2000),
    ],

    /*
    |--------------------------------------------------------------------------
    | CDN Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for serving assets from a CDN.
    |
    */

    'cdn' => [
        'enabled' => env('CDN_ENABLED', false),
        'url' => env('CDN_URL'),

        // Asset types to serve from CDN
        'asset_types' => ['css', 'js', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'woff', 'woff2'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloading
    |--------------------------------------------------------------------------
    |
    | Configure critical resources to preload for faster page rendering.
    |
    */

    'preload' => [
        'enabled' => env('PRELOAD_ENABLED', true),

        // Critical assets to preload
        'assets' => [
            '/build/assets/app.css',
            '/build/assets/app.js',
        ],
    ],

];
