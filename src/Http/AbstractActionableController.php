<?php

namespace Therour\Actionable\Http;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Validator;
use Therour\Actionable\Contracts\Actionable;
use Illuminate\Contracts\Support\Responsable;

abstract class AbstractActionableController extends Controller
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var \Illuminate\Routing\Route
     */
    protected $route;

    /**
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * @var \Therour\Actionable\Contracts\Actionable
     */
    protected $action;

    /**
     * Handle Http Request
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Container\Container $app
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Container $app)
    {
        $this->app = $app;
        $this->request = $this->getRequest($request);
        $this->route = $request->route();
        $this->action = $this->getAction();

        $data = $this->getData();
        $this->validate($data);
        
        $result = $this->execute($this->action, $data);
        
        return $result;
    }

    /**
     * Execute action.
     *
     * @param \Therour\Actionable\Contracts\Actionable $action
     * @param array $data
     * @return mixed
     */
    abstract protected function execute(Actionable $action, array $data);

    /**
     * Validate data.
     *
     * @param array $data
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validate(array $data)
    {
        if (method_exists($this->action, 'rules')) {
            Validator::make(
                $data,
                $this->action->rules(),
                method_exists($this->action, 'messages') ? $this->action->messages() : []
            )->validate();
        }
    }

    /**
     * Resolve Request instance.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Request
     */
    protected function getRequest($request)
    {
        if ($requestClass = Arr::get($request->route()->parameters(), 'form_request')) {
            $request = $this->app->make($requestClass);
            if (! $request instanceof Request) {
                throw new \RuntimeException('request class must be an instance of \Illuminate\Http\Request');
            }
        }

        return $request;
    }

    /**
     * Get the Actionable class instance.
     *
     * @return \Therour\Actionable\Contracts\Actionable
     */
    protected function getAction()
    {
        return $this->app->make($this->route->defaults['actionable']);
    }
    
    /**
     * Get request data
     *
     * @return array
     */
    protected function getData()
    {
        return array_merge(
            $this->request->all(),
            Arr::except($this->route->parameters(), ['actionable', 'form_request'])
        );
    }
}
