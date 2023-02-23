<?php

namespace App\Providers;

use App\Services\InvoiceProccesator;
use App\Services\SageConnector;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\ServiceProvider;

class InvoiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind(InvoiceProccesator::class, function ($app) {
            return new InvoiceProccesator();
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
