<?php

namespace Basil832025\FrontendThreePiroga;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class FrontendThreePirogaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app['view']->addLocation(__DIR__ . '/../resources/views');
        Blade::anonymousComponentPath(__DIR__ . '/../resources/views/front/3piroga/components');
    }
}

