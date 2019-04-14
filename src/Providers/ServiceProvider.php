<?php

namespace Therour\Actionable\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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

        \Illuminate\Routing\Route::macro('request', function (string $requestClass) {
            $this->defaults['form_request'] = $requestClass;
        });
        
        $routePath = config('actionable.route_path');
        if (config('actionable.enable_actions_route')) {
            $this->loadRoutes($routePath);
        }
    }

    protected function loadRoutes($routePath)
    {
        if (is_array($routePath)) {
            foreach ($routePath as $path) {
                $this->loadRoutes($path);
            }
        } elseif (is_string($routePath) && Str::endsWith($routePath, '.php') && file_exists(base_path($routePath))) {
            Route::group([], base_path($routePath));
        } elseif (is_string($routePath) && is_dir($routePath)) {
            $routes = glob(
                Str::finish(base_path($routePath), '/') . '*'
            );

            foreach ($routes as $route) {
                $this->loadRoutes($route);
            }
        }
    }
}
