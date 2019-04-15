<?php

namespace Therour\Actionable\Tests\Actions;

use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;
use Illuminate\Cache\CacheManager;
use Therour\Actionable\Contracts\Actionable;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LoadMessageAction implements Actionable, Responsable
{
    protected $cache;

    protected $message;

    public function __construct(CacheManager $cache)
    {
        $this->cache = $cache;
    }

    public function run($key)
    {
        $message = $this->cache->get('message');

        $message = Arr::get($message, $key);
        if ($message == null) {
            throw new NotFoundHttpException("Cache {$key} is not found.");
        }

        return $this->result = $message;
    }

    public function toResponse($request)
    {
        return new JsonResponse(['data' => ['message' => $this->result]]);
    }
}
