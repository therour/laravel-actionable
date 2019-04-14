<?php

namespace Therour\Actionable\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Therour\Actionable\Tests\Actions\PostAction;
use Therour\Actionable\Tests\Actions\LoadMessageAction;
use Therour\Actionable\Tests\Actions\SaveMessageAction;

class TestPostAction extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::action('POST', '/message', SaveMessageAction::class);
        Route::action('GET', '/message', LoadMessageAction::class);
    }

    public function testRun()
    {
        $response = $this->getJson('/message');
        $response->assertResponseStatus(404);

        $response = $this->postJson('/message');
        $response->assertResponseStatus(422);

        $helloMessage = ['message' => 'Hello World!'];
        $response = $this->postJson('/message', $helloMessage);
        $response->assertResponseStatus(200);

        $response = $this->getJson('/message', []);
        $content = $response->response->getContent();
        $response->assertJson($content);
        $this->assertEquals('HELLO WORLD!', (json_decode($content))->message);
    }
}
