<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests;

use Error;
use Illuminate\Support\Str;
use MyParcelCom\AuthModule\PublicKey;
use MyParcelCom\AuthModule\Tests\Mocks\CacheMock;
use PHPUnit\Framework\TestCase;

class PublicKeyTest extends TestCase
{
    private PublicKey $key;

    protected function setUp(): void
    {
        parent::setUp();

        $this->key = new PublicKey();

        $tempFile = tempnam(sys_get_temp_dir(), 'key');
        file_put_contents($tempFile, 'this-is-an-oauth-key');

        $this->key->setPath($tempFile);
    }

    public function testGetKeyString(): void
    {
        $keyString = $this->key->getKeyString();

        $this->assertIsString($keyString);
        $this->assertEquals('this-is-an-oauth-key', $keyString);
    }

    public function testGetKeyStringWithoutSsl(): void
    {
        $keyString = $this->key->setVerifySsl(false)->getKeyString();

        $this->assertIsString($keyString);
        $this->assertEquals('this-is-an-oauth-key', $keyString);
    }

    public function testToString(): void
    {
        $this->assertEquals($this->key->getKeyString(), (string) $this->key);
    }

    public function testCache(): void
    {
        $publicKey = $this->key->setCache(new CacheMock());

        $keyString = $publicKey->getKeyString();

        $tempFile = tempnam(sys_get_temp_dir(), 'key');
        file_put_contents($tempFile, Str::random(128));

        $publicKey->setPath($tempFile);

        $this->assertEquals(
            $keyString,
            $publicKey->getKeyString(),
            'Public key should have been cached on first read'
        );

        $publicKey->flushCache();
        $this->assertNotEquals(
            $keyString,
            $publicKey->getKeyString(),
            'Public key should have been flushed from the cache'
        );
    }

    public function testSetPath(): void
    {
        $publicKey = new PublicKey();

        $keyString = Str::random(128);
        $tempFile = tempnam(sys_get_temp_dir(), 'key');
        file_put_contents($tempFile, $keyString);

        $this->assertSame(
            $publicKey,
            $publicKey->setPath($tempFile),
            '`setPath()` should return itself'
        );

        $this->assertEquals(
            $keyString,
            $publicKey->getKeyString(),
            '`getKeyString()` should return the contents of the file'
        );
    }

    public function testUnSetPath(): void
    {
        $publicKey = new PublicKey();

        $this->expectException(Error::class);
        $publicKey->getKeyString();
    }
}
