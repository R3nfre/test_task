<?php

namespace App\Providers;

use App\Services\CurrencyRatesApiService;
use App\Services\CurrencyExchangeService;
use Illuminate\Support\ServiceProvider;

class CurrencyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(CurrencyRatesApiService::class, function ($app) {
            return new CurrencyRatesApiService();
        });

        $this->app->singleton(CurrencyExchangeService::class, function ($app) {
            return new CurrencyExchangeService(
                $app->make(CurrencyRatesApiService::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
