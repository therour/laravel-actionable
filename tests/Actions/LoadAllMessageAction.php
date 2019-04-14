<?php

namespace Therour\Actionable\Tests\Actions;

use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;
use Illuminate\Cache\CacheManager;
use Therour\Actionable\Actions\Actionable;
use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LoadAllMessageAction implements Actionable, Responsable
{
    protected $cache;

    protected $message;

    public function __construct(CacheManager $cache)
    {
        $this->cache = $cache;
    }

    public function run()
    {
        $message = $this->cache->get('message');

        return $this->result = $message;
    }

    public function toResponse($request)
    {
        $data = array_map(function ($message) {
            return ['message' => $message];
        }, $this->result);

        return new JsonResponse(compact('data'));
    }
}
