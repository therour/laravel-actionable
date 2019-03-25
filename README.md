# Actionable - Laravel Single Action Class Routing

Laravel Actionable is used to create single action class and made routing simple to them.

## Installation
### Composer
Install by composer command
```
composer require therour/laravel-actionable
```
### Service Provider
**Version >= Laravel 5.5** attached automatically, no need action
**Version < Laravel 5.5**
In your `config/app.php` add `Therour\Actionable\Providers\ServiceProvider::class` to the packages of provider array
```php
'providers' => [
    ...
    /*
    * Package Service Providers...
    */
    ...
    Therour\Actionable\Providers\ServiceProvider::class,
    ...
],
```
### Configuration
#### Publish
```
php artisan vendor:publish --provider "Therour\Actionable\Providers\ServiceProvider::class"
```
## Usage
### Create an Action
Create a class implementing `Therour\Actionable\Contracts\Actionable` interface
**nb:** you may inject your dependency at `__construct()` method
```php
namespace App\User\Actions;

use Therour\Actionable\Contracts\Actionable;
use App\User\Models\User;

class GetUser implements Actionable
{
    /**
     * @var mixed
     */
    protected $result;

    /**
     * @var \App\User\Models\User
     */
    protected $model;

    /**
     * Initiate a single action class
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Start running the action
     * 
     * @var array $data
     * @return mixed
     */
    public function run(array $data)
    {
        return $this->result = $this->model->find($data['id']);
    }
}
```
### Routes
create a routes file, by default `routes/actions.php` and add the action route.
or you may edit `route_path` in your `config/actionable.php` configuration
```php
<?php

Route::action('GET', '/user/{id}', App\User\Actions\GetUser::class);

// If you need to define your FormRequest class
Route::action('POST', '/user', App\user\Actions\CreateUser::class)
    ->request(App\User\Http\Request\CreateUserRequest::class);

```
then you can hit `/user/{id}` endpoint

### Modify as Http Response
You can use the `Responsable` interface of Laravel by implemented it, and add method `toResponse($request)` inside the action class.
```php
...
use Illuminate\Contracts\Support\Responsable;

class GetUser implements Actionable, Responsable
{
    ...

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request)
    {
        // accessing the result of `run` method by assigned `result` attribute
        return response()->json(['data' => $this->result]);
    }
}
```
## Explanation
all the route parameter will be accessible in `array $data` on method `run(array $data)` as well as request payloads.
so in case route `/user/{id}` the `{id}` is accessible at `$data['id]`