<?php

namespace Therour\Actionable\Tests\Actions;

use Illuminate\Http\JsonResponse;
use Illuminate\Cache\CacheManager;
use Therour\Actionable\Actions\Actionable;
use Illuminate\Contracts\Support\Responsable;
use Therour\Actionable\Tests\Params\MessageParam;
use Therour\Actionable\Tests\Actions\LoadAllMessageAction;

class SaveMessageAction implements Actionable, Responsable
{
    protected $cache;

    protected $result;

    protected $key;

    protected $loadAllMessageAction;

    public function __construct(CacheManager $cache, LoadAllMessageAction $loadAllMessageAction)
    {
        $this->cache = $cache;
        $this->loadAllMessageAction = $loadAllMessageAction;
    }

    public function run(MessageParam $param, $key)
    {
        $message = $this->loadAllMessageAction->run();
        
        $message[$key] = $param->getMessage();
        $this->cache->put('message', $message);

        $this->key = $key;
        return $this->result = true;
    }

    public function toResponse($request)
    {
        return new JsonResponse(['message' => "Successfully saved message with key '{$this->key}'"]);
    }
}
