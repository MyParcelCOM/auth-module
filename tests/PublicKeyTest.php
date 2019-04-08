<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests;

use Exception;
use Illuminate\Support\Str;
use MyParcelCom\AuthModule\PublicKey;
use MyParcelCom\AuthModule\Tests\Mocks\CacheMock;
use PHPUnit\Framework\Error\Warning;
use PHPUnit\Framework\TestCase;

class PublicKeyTest extends TestCase
{
    /** @var PublicKey */
    private $key;

    protected function setUp()
    {
        parent::setUp();

        $this->key = new PublicKey();

        $tempFile = tempnam(sys_get_temp_dir(), 'key');
        file_put_contents($tempFile, 'this-is-an-oauth-key');

        $this->key->setPath($tempFile);
    }

    /** @test */
    public function testGetKeyString(): void
    {
        $keyString = $this->key->getKeyString();

        $this->assertIsString($keyString);
        $this->assertEquals('this-is-an-oauth-key', $keyString);
    }

    /** @test */
    public function testToString(): void
    {
        $this->assertEquals($this->key->getKeyString(), (string) $this->key);
    }

    /** @test */
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

    /** @test */
    public function testSetPath(): void
    {
        $publicKey = new PublicKey();

        $keyString = str_random(128);
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

    /** @test */
    public function testUnSetPath(): void
    {
        $publicKey = new PublicKey();

        $this->expectException(Exception::class);
        $publicKey->getKeyString();
    }

    /** @test */
    public function testIncorrectPath(): void
    {
        $publicKey = new PublicKey();

        $this->expectException(Warning::class);
        $publicKey->setPath('fly-me-to-the-moon/and-let-me-play-among/exceptions')->getKeyString();
    }
}
