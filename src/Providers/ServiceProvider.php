<?php

namespace Therour\Actionable\Providers;

use Illuminate\Support\Arr;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * @var string
     */
    private $defaultActionableConfigPath = __DIR__.'/../../configs/config.php';

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->defaultActionableConfigPath, 'actionable');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            $this->defaultActionableConfigPath => config_path('actionable.php')
        ], 'config');

        
        Router::macro('action', function ($methods, $uri, $action) {
            $route =  $this->match(
                Arr::wrap($methods),
                $uri,
                config('actionable.controller')
            );
            $route->defaults = ['actionable' => $action];
            return $route;
        });
        
        if (config('actionable.enable_actions_route')) {
            Route::group(
                [],
                config('actionable.route_path', base_path('routes/actions.php'))
            );
        }
    }
}
