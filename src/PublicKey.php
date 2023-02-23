<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule;

use DateInterval;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class PublicKey
{
    protected const CACHE_KEY = 'public-key';
    protected const CACHE_TTL = 'P30D';

    protected ?CacheInterface $cache = null;
    protected string $path;
    protected bool $verifySsl = true;

    /**
     * Get the public key as a string.
     */
    public function getKeyString(): string
    {
        // Try and get the key from the cache.
        if (isset($this->cache) && !empty($keyString = $this->cache->get(self::CACHE_KEY))) {
            return $keyString;
        }

        // Get the key contents from the file.
        // If verifySsl is false, don't verify the remote cert (if the file is requested over https).
        $keyString = $this->verifySsl
            ? file_get_contents($this->path)
            : file_get_contents($this->path, false, stream_context_create([
                'ssl' => [
                    'verify_peer'      => false,
                    'verify_peer_name' => false,
                ],
            ]));

        // Cache the key if a cache is set.
        if (isset($this->cache)) {
            $this->cache->set(self::CACHE_KEY, $keyString, new DateInterval(self::CACHE_TTL));
        }

        return $keyString;
    }

    /**
     * Remove the public key from the cache.
     *
     * @throws InvalidArgumentException
     */
    public function flushCache(): self
    {
        $this->cache?->delete(self::CACHE_KEY);

        return $this;
    }

    public function setCache(CacheInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * If the set path is a remote https url with an invalid cert,
     * setting this to false will disable the ssl cert check.
     */
    public function setVerifySsl(bool $verifySsl): self
    {
        $this->verifySsl = $verifySsl;

        return $this;
    }

    /**
     * Returns the public key as a string.
     */
    public function __toString(): string
    {
        return $this->getKeyString();
    }
}
