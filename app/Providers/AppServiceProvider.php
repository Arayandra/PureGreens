<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
use App\Services\AprioriRecommendationService;
use App\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Http\Request;

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
        // iPaymu signs the original callback payload. Laravel's global request
        // normalizers must not trim or convert values before HMAC verification.
        $isIpaymuCallback = fn (Request $request): bool => $request->is('ipaymu/callback');

        TrimStrings::skipWhen($isIpaymuCallback);
        ConvertEmptyStringsToNull::skipWhen($isIpaymuCallback);

        // Filament::serving(function () {
        //     Filament::registerViteTheme('resources/css/filament.css');
        // });
    }}
