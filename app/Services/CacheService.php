<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    private $cacheExpireTime = 60;
    private $cacheKey = '';

    public function remember(array | string $data)
    {
        $currentConnection = $this->getConnection();
        $cacheExpirationTime = $this->getCacheExpireTime();
        if ($currentConnection === 'redis') {
            return Redis::set($this->getCacheKey(), json_encode($data), $cacheExpirationTime);
        } else {
            return Cache::put($this->getCacheKey(), json_encode($data), $cacheExpirationTime);
        }
    }

    public function getCacheValue()
    {
        $currentConnection = $this->getConnection();
        if ($currentConnection === 'redis') {
            return json_decode(Redis::get($this->getCacheKey()));
        } else {
            return json_decode(Cache::get($this->getCacheKey()));
        }
    }

    public function forget(array $keys)
    {
        $currentConnection = $this->getConnection();
        if (count($keys)) {
            $cache = ($currentConnection === 'redis') ? Redis::del($keys) : Cache::forget($keys);
        } else {
            $cache = ($currentConnection === 'redis') ? Redis::flushall() : Cache::flush();
        }

        return (bool) $cache;
    }

    private function getConnection(): ?string
    {
        return env('CACHE_DRIVER');
    }

    public function setCacheExpireTime($cacheExpireTime): self
    {
        $this->cacheExpireTime = $cacheExpireTime;
        return $this;
    }

    private function getCacheExpireTime(): ?string
    {
        return $this->cacheExpireTime;
    }

    public function setCacheKey($cacheKey): self
    {
        $this->cacheKey = $cacheKey;
        return $this;
    }

    private function getCacheKey(): ?string
    {
        return $this->cacheKey;
    }

    public function checkCacheKeyIfExist(): bool
    {
        $key = $this->getCacheKey();
        if ($key != null) {
            if ($this->getConnection() === 'redis') {
                return Redis::exists($key);
            } else {
                return Cache::has($key);
            }
        }
        return false;
    }
}
