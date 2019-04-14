<?php

namespace Therour\Actionable\Tests\Actions;

use Illuminate\Cache\CacheManager;
use Therour\Actionable\Actions\Actionable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LoadMessageAction implements Actionable
{
    protected $cache;

    public function __construct(CacheManager $cache)
    {
        $this->cache = $cache;
    }

    public function run()
    {
        $message = $this->cache->get('message');

        if ($message == null) {
            throw new NotFoundHttpException('Cache message is not found.');
        }

        return ['message' => $message];
    }
}