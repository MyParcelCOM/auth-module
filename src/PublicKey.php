<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule;

use DateInterval;
use Psr\SimpleCache\CacheInterface;
use RuntimeException;

class PublicKey
{
    /** @var string */
    protected const CACHE_KEY = 'public-key';

    /** @var CacheInterface */
    protected $cache;

    /** @var string */
    protected $path;

    /** @var bool */
    protected $verifySsl = true;

    /**
     * Get the public key as a string.
     *
     * @return string
     */
    public function getKeyString(): string
    {
        // Try and get the key from the cache.
        if (isset($this->cache) && !empty($keyString = $this->cache->get(self::CACHE_KEY))) {
            return $keyString;
        }

        if (!isset($this->path)) {
            throw new RuntimeException('Public key path not set');
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
            $this->cache->set(self::CACHE_KEY, $keyString, new DateInterval('P30D'));
        }

        return $keyString;
    }

    /**
     * Remove the public key from the cache.
     *
     * @return $this
     * @throws RuntimeException
     */
    public function flushCache(): self
    {
        if ($this->cache) {
            $this->cache->delete(self::CACHE_KEY);
        }

        return $this;
    }

    /**
     * @param CacheInterface $cache
     * @return $this
     */
    public function setCache(CacheInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * If the set path is a remote https url with an invalid cert,
     * setting this to false will disable the ssl cert check.
     *
     * @param bool $verifySsl
     * @return $this
     */
    public function setVerifySsl(bool $verifySsl): self
    {
        $this->verifySsl = $verifySsl;

        return $this;
    }

    /**
     * Returns the public key as a string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getKeyString();
    }
}
