<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
use App\Services\AprioriRecommendationService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AprioriRecommendationService::class, function ($app) {
        return new AprioriRecommendationService();
    });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Filament::serving(function () {
        //     // Using Vite
        //     Filament::registerViteTheme('resources/css/filament.css');
        // });
    }
}
