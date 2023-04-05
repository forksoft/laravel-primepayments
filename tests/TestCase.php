<?php

namespace Forksoft\PrimePayments\Test;

use Forksoft\PrimePayments\PrimePayments;
use Forksoft\PrimePayments\PrimePaymentsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * @var PrimePayments
     */
    protected $primepayments;

    public function setUp(): void
    {
        parent::setUp();

        $this->primepayments = $this->app['primepayments'];

        $this->app['config']->set('primepayments.project_id', '12345');
        $this->app['config']->set('primepayments.secret_key', 'secret_key');
        $this->app['config']->set('primepayments.secret_key_second', 'secret_key_second');
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            PrimePaymentsServiceProvider::class,
        ];
    }

    /**
     * @param array $config
     */
    protected function withConfig(array $config)
    {
        $this->app['config']->set($config);
        $this->app->forgetInstance(PrimePayments::class);
        $this->primepayments = $this->app->make(PrimePayments::class);
    }
}
