<?php

namespace Therour\Actionable\Tests\Actions;

use Therour\Actionable\Actions\Actionable;

class PingAction implements Actionable
{
    public function run()
    {
        return "pong!";
    }
}