<?php
declare(strict_types=1);

namespace Kirschbaum\LaravelFeatureFlag\Facades;

use Illuminate\Support\Facades\Facade;

class FeatureFlag extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'featureflag';
    }
}
