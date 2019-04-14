<?php

namespace Therour\Actionable\Tests;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Therour\Actionable\Tests\Actions\PingAction;

class TestPingAction extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::action('GET', '/ping', PingAction::class);
    }

    public function testRun()
    {
        $headers = ['Accept' => 'Application/json'];
        $response = $this->get('/ping', [], $headers);

        $content = $response->response->content();
        $response->assertEquals('pong!', $content);
    }
}
