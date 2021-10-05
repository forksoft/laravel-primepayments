<?php

namespace DexiLandazel\PrimePayments;

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
        $this->publishes([
            __DIR__.'/../views/primepayments.blade.php' => resource_path('/views/posts/primepayments.blade.php'),
        ], 'views');
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
