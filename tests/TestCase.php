<?php

namespace Therour\Actionable\Tests;

use Orchestra\Testbench\BrowserKit\TestCase as BaseTestCase;
use Therour\Actionable\Providers\ServiceProvider;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class
        ];
    }
}