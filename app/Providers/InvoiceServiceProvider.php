<?php

namespace App\Providers;

use App\Services\InvoiceConnetor;
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
        $this->app->bind(InvoiceConnetor::class, function ($app) {
            return new InvoiceConnetor();
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
