<?php

namespace Therour\Actionable\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Therour\Actionable\Tests\Actions\PostAction;
use Therour\Actionable\Tests\Actions\LoadMessageAction;
use Therour\Actionable\Tests\Actions\SaveMessageAction;
use Therour\Actionable\Tests\Actions\LoadAllMessageAction;

class TestPostAction extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::action('POST', '/message/{key}', SaveMessageAction::class);
        Route::action('GET', '/message', LoadAllMessageAction::class);
        Route::action('GET', '/message/{key}', LoadMessageAction::class);
    }

    public function testRun()
    {
        $messages = ['key1' => 'Hello!', 'key2' => 'World!'];
        
        foreach ($messages as $key => $message) {
            $response = $this->getJson('/message/'.$key);
            
            $response->assertResponseStatus(404);
            
            $response->postJson('/message/'.$key);
            $response->assertResponseStatus(422);
            
            $response = $this->postJson('/message/'.$key, ['message' => $message]);
            $response->assertResponseStatus(200);
            $jsonResponse = json_decode($response->response->getContent());
            $response->assertEquals("Successfully saved message with key '{$key}'", $jsonResponse->message);

            $response = $this->getJson('/message/'.$key);
            $jsonResponse = json_decode($response->response->getContent());
            $this->assertEquals(strtoupper($message), $jsonResponse->data->message);
        }

        $response = $this->getJson('/message');
        $jsonResponse = json_decode($response->response->getContent());
        foreach ($messages as $key => $message) {
            $this->assertEquals(strtoupper($message), $jsonResponse->data->{$key}->message);
        }
    }
}
