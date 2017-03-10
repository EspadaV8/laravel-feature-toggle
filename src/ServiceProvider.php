<?php
declare(strict_types=1);

namespace Kirschbaum\LaravelFeatureFlag;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->handleConfigs();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('featureflag', function () {
            return new FeatureFlag($this->app->environment());
        });
    }

    private function handleConfigs()
    {
        $configPath = __DIR__ . '/../config/feature-flags.php';

        $this->publishes([$configPath => config_path('feature-flags.php')]);

        $this->mergeConfigFrom($configPath, 'feature-flags');
    }
}
