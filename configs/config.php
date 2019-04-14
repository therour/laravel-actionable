<?php

return [
    'controller' => Therour\Actionable\Http\ActionController::class,

    /*
    |--------------------------------------------------------------------------
    | Action Routes
    |--------------------------------------------------------------------------
    |
    | Next, you may define custom route for your actions.
    | we can autoload your routes by enable the route and
    | define your routes path.
    |
    | 1. you may define single file php that load routes, or
    | 2. define directory path to load all php files inside it, or
    | 3. define an array to load multiple routes path
    |
    */
    'enable_actions_route' => true,
    'route_path' => 'routes/actions.php'
];
