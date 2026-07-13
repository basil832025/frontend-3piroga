<?php

namespace Basil832025\FrontendThreePiroga;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class FrontendThreePirogaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $viewPath = __DIR__ . '/../resources/views';

        View::addNamespace('front.3piroga', $viewPath);
        Blade::anonymousComponentPath($viewPath . '/components');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->publishes([
            __DIR__ . '/../public' => public_path(),
        ], 'frontend-3piroga-assets');
    }
}
