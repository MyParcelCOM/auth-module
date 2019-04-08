<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests\Mocks;

use Closure;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Cache\Store;
use Mockery;

class CacheMock implements Repository
{
    public $data = [];

    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return $this->data[$key];
        }

        return $default;
    }

    public function pull($key, $default = null)
    {
        if ($this->has($key)) {
            $value = $this->data[$key];
            unset($this->data[$key]);

            return $value;
        }

        return $default;
    }

    public function put($key, $value, $minutes = null)
    {
        $this->data[$key] = $value;
    }

    public function add($key, $value, $minutes = null)
    {
        if ($this->has($key)) {
            return false;
        }

        $this->put($key, $value, $minutes);

        return true;
    }

    public function increment($key, $value = 1)
    {
        if (!$this->has($key)) {
            $this->data[$key] = 0;
        }

        return ($this->data[$key] += $value);
    }

    public function decrement($key, $value = 1)
    {
        if (!$this->has($key)) {
            $this->data[$key] = 0;
        }

        return ($this->data[$key] -= $value);
    }

    public function forever($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function remember($key, $minutes, Closure $callback)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $this->put($key, $value = $callback(), $minutes);

        return $value;
    }

    public function sear($key, Closure $callback)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $this->forever($key, $value = $callback());

        return $value;
    }

    public function rememberForever($key, Closure $callback)
    {
        return $this->sear($key, $callback);
    }

    public function forget($key)
    {
        unset($this->data[$key]);
    }

    public function set($key, $value, $ttl = null)
    {
        $this->data[$key] = $value;

        return true;
    }

    public function delete($key)
    {
        unset($this->data[$key]);

        return true;
    }

    public function clear()
    {
        $this->data = [];
    }

    public function getMultiple($keys, $default = null)
    {
        $return = [];
        foreach ($keys as $key) {
            $return[$key] = $this->data[$key] ?? $default;
        }

        return $return;
    }

    public function setMultiple($values, $ttl = null)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            unset($this->data[$key]);
        }

        return true;
    }

    public function getStore()
    {
        return Mockery::mock(Store::class);
    }
}
