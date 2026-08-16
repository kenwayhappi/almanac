<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if (config('app.env') === 'production' || str_contains(request()->header('x-forwarded-proto', ''), 'https')) {
            URL::forceScheme('https');
        }

        // Alignement des types de colonnes PostgreSQL pour éviter SQLSTATE[42883] character varying = integer
        try {
            if (\Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql') {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE administrative_divisions ALTER COLUMN country_id TYPE VARCHAR(255) USING country_id::varchar;');
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE administrative_division_types ALTER COLUMN country_id TYPE VARCHAR(255) USING country_id::varchar;');
            }
        } catch (\Throwable $e) {
            // Ignorer si les colonnes sont déjà converties ou pas de privilèges DDL
        }
    }
}
