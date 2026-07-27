<?php

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Facade;

return [
    /*
     |--------------------------------------------------------------------------
     | Application Name
     |--------------------------------------------------------------------------
     */
    'name' => env('APP_NAME', 'Laravel'),

    /*
     |--------------------------------------------------------------------------
     | Application Environment
     |--------------------------------------------------------------------------
     */
    'env' => env('APP_ENV', 'production'),

    /*
     |--------------------------------------------------------------------------
     | Application Debug Mode
     |--------------------------------------------------------------------------
     */
    'debug' => (bool) env('APP_DEBUG', false),

    /*
     |--------------------------------------------------------------------------
     | Application URL
     |--------------------------------------------------------------------------
     */
    'url' => env('APP_URL', 'http://localhost'),

    /*
     |--------------------------------------------------------------------------
     | Application Timezone
     |--------------------------------------------------------------------------
     */
    'timezone' => 'UTC',

    /*
     |--------------------------------------------------------------------------
     | Application Locale Configuration
     |--------------------------------------------------------------------------
     */
    'locale' => env('APP_LOCALE', 'fr'), // Français par défaut
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'), // Anglais comme secours
    'faker_locale' => env('APP_FAKER_LOCALE', 'fr_FR'),
    'available_locales' => ['fr', 'en'], // Langues supportées

    /*
     |--------------------------------------------------------------------------
     | Encryption Key
     |--------------------------------------------------------------------------
     */
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [
        ...array_filter(explode(',', env('APP_PREVIOUS_KEYS', ''))),
    ],

    /*
     |--------------------------------------------------------------------------
     | Maintenance Mode Driver
     |--------------------------------------------------------------------------
     */
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    /*
     |--------------------------------------------------------------------------
     | Autoloaded Service Providers
     |--------------------------------------------------------------------------
     */
    'providers' => ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ])->toArray(),

    /*
     |--------------------------------------------------------------------------
     | Class Aliases
     |--------------------------------------------------------------------------
     */
    'aliases' => Facade::defaultAliases()->merge([])->toArray(),
];
