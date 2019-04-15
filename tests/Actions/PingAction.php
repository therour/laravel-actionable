<?php

namespace Therour\Actionable\Tests\Actions;

use Therour\Actionable\Contracts\Actionable;

class PingAction implements Actionable
{
    public function run()
    {
        return "pong!";
    }
}