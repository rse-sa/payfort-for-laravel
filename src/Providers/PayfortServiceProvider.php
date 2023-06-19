<?php

namespace RSE\PayfortForLaravel\Providers;

use Illuminate\Support\ServiceProvider;
use RSE\PayfortForLaravel\PayfortIntegration;

class PayfortServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected bool $defer = false;

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/payfort.php', 'payfort');

        $this->publishes([
            __DIR__ . '/../../config/payfort.php' => config_path('payfort.php'),
        ], 'payfort-config');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->bind(PayfortIntegration::class, function () {
            return new PayfortIntegration();
        });
    }
}
