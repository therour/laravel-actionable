<?php

namespace Therour\Actionable\Tests\Actions;

use Illuminate\Cache\CacheManager;
use Therour\Actionable\Actions\Actionable;
use Therour\Actionable\Tests\Params\MessageParam;

class SaveMessageAction implements Actionable
{
    protected $cache;

    public function __construct(CacheManager $cache)
    {
        $this->cache = $cache;
    }

    public function run(MessageParam $param)
    {
        $this->cache->put('message', $param->getMessage());

        return ['message' => $param->getMessage()];
    }
}