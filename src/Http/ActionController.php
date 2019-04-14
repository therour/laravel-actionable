<?php

namespace Therour\Actionable\Http;

use Illuminate\Support\Arr;
use Therour\Actionable\Actions\Actionable;
use Therour\Actionable\Params\AbstractParam;
use Illuminate\Contracts\Support\Responsable;
use Therour\Actionable\Http\AbstractActionableController;

class ActionController extends AbstractActionableController
{
    /**
     * Execute action.
     *
     * @param \Therour\Actionable\Actions\Actionable $action
     * @param array $data
     * @return mixed
     */
    protected function execute(Actionable $action, array $data)
    {
        $result = $this->runningAction($action, $data);

        if ($action instanceof Responsable) {
            return $action->toResponse($this->request);
        }

        return $result;
    }

    /**
     * resolve Running Action.
     *
     * @param \Therour\Actionable\Actions\Actionable $action
     * @param array $data
     * @return mixed
     */
    protected function runningAction(Actionable $action, array $data)
    {
        $parameters = $this->resolveParameters($action, $data);

        return $this->app->call([$action, 'run'], $parameters);
    }

    /**
     * Resolving parameters action run method.
     *
     * @param \Therour\Actionable\Actions\Actionable $action
     * @param array $data
     * @return array
     */
    protected function resolveParameters(Actionable $action, array $data)
    {
        $r = new \ReflectionMethod($action, 'run');

        $routeParams = $this->getRouteParameters();

        $actionParams = [];
        foreach ($r->getParameters() as $param) {
            if ($param->hasType() && is_subclass_of($param->getType()->getName(), AbstractParam::class)) {
                $paramClass = $param->getType()->getName();
                $actionParams[$param->name] = new $paramClass($data);
                $actionParams[$param->name]->validate();
            } elseif (isset($routeParams[$param->name])) {
                $actionParams[$param->name] = $routeParams[$param->name];
            } elseif (!$param->hasType() || $param->getType()->getName() == 'array') {
                $actionParams[$param->name] = $data;
            }
        }

        return $actionParams;
    }

    protected function getRouteParameters()
    {
        return Arr::except($this->route->parameters(), ['actionable', 'form_request']);

    }
}
