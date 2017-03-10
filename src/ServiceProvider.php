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

    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $config = require config_path('feature-flags.php');

        $this->app->bind('featureflag', function () use ($config) {
            return new FeatureFlag(
                $this->app->environment(),
                $config
            );
        });
    }
}
