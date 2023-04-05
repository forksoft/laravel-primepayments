<?php

namespace Forksoft\PrimePayments;

use Illuminate\Support\ServiceProvider;

class PrimePaymentsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/primepayments.php' => config_path('primepayments.php'),
        ], 'config');
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/primepayments.php', 'primepayments');

        $this->app->singleton('primepayments', function () {
            return $this->app->make(PrimePayments::class);
        });

        $this->app->alias('primepayments', 'PrimePayments');

        //
    }
}
