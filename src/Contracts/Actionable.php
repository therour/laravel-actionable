<?php

namespace Therour\Actionable\Contracts;

interface Actionable
{
    /**
     * Start running the action.
     *
     * @param array $data
     * @return mixed
     */
    public function run(array $data);
}
