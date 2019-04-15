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

#### Edit (optional)
1. If you create a custom controller to handle Actionable class, you may define the controller class in key `controller` at config file.
2. If you want to load action routes, define the php file or directory path to your routes file, because `web.php` and `api.php` has default controller namespace, so we cannot load our default action controller that has different namespace

## Usage
### Routes
create a routes file, by default `routes/actions.php` and add the action route.
or you may edit `route_path` in your `config/actionable.php` configuration
```php
<?php

Route::action('GET', '/users/{id}', App\User\Actions\GetUser::class);

// If you need to define your FormRequest class
Route::action('POST', '/users', App\user\Actions\CreateUser::class);

```

### Create an Action
Create a class implementing `Therour\Actionable\Contracts\Actionable` interface
**nb:** you may inject your dependency at `__construct()` method

the route parameter `{id}` will be passed to variable `$id` at the `run` method of actionable class.

```php
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
    public function run($id)
    {
        return $this->result = $this->model->find($id);
    }
}
```

### Create Param to Filter and Validate parameters
You may define `Param` class to filter and validate parameters, by typehint `Param` class in `run` method of `Actionable Class` all request data will be passed to `Param` class.

By defining `rules` static method, our default controller will validate it before running the action.

```php
use Therour\Actionable\Params\AbstractParam;

class CreateUserParam extends AbstractParam
{
    private $name;

    private $email;

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return strtolower($this->email);
    }

    public static function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users'
        ];
    }
}
```
##### Param Usage
```php
...
public function run(CreateUserParam $param)
{
    // load all filtered parameters. with `getParameters()`
    // $this->model->create($param->getParameters())

    $this->model->create([
        'user' => $param->getName(),
        'email' => $param->getEmail()
    ]);
}
...
```
Or you may run the action without action route.
```php

public function blabla(Request $request, CreateUser $action, CreateUserParam $param)
{
    $param->create($request->all())
        ->validate();

    $user = $action->run($param);

    return $user;
}
```

### Modify as Http Response
You can use the `Responsable` interface of Laravel by implemented it, and add method `toResponse($request)` inside the action class.
```php
...
use Illuminate\Http\JsonResponse;

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
        return new JsonResponse(['data' => $this->result]);
    }
}
```
