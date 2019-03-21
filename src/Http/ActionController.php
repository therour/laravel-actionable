<?php

namespace Therour\Actionable\Http;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Validator;
use Therour\Actionable\Contracts\Actionable;
use Illuminate\Contracts\Support\Responsable;
use Therour\Actionable\Http\AbstractActionableController;

class ActionController extends AbstractActionableController
{
    /**
     * Execute action.
     *
     * @param \Therour\Actionable\Contracts\Actionable $action
     * @param array $data
     * @return mixed
     */
    protected function execute(Actionable $action, array $data)
    {
        $result = $action->run($data);

        if ($action instanceof Responsable) {
            return $action->toResponse($this->request);
        }

        return $result;
    }
}
