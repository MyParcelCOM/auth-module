<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests;

use Mockery;
use MyParcelCom\AuthModule\Contracts\UserInterface;
use MyParcelCom\AuthModule\Contracts\UserRepositoryInterface;
use MyParcelCom\AuthModule\JwtAuthenticator;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;
use PHPUnit\Framework\TestCase;
use Tests\Traits\AccessTokenTrait;

class JwtAuthenticatorTest extends TestCase
{
    use AccessTokenTrait;

    /** @var JwtAuthenticator */
    protected $jwtAuthenticator;

    /** @var UserInterface */
    protected $user;

    protected function setUp()
    {
        parent::setUp();

        $this->user = Mockery::mock(UserInterface::class);

        $this->generateKeys();

        $this->jwtAuthenticator = (new JwtAuthenticator())
            ->setPublicKey($this->publicKey)
            ->setUserRepository(Mockery::mock(UserRepositoryInterface::class, [
                'makeAuthenticatedUser' => $this->user,
            ]));
    }

    protected function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function testAuthenticate()
    {
        $token = $this->createTokenString([], null, 'some-user-id', [], false);
        $this->assertSame($this->user, $this->jwtAuthenticator->authenticate($token));
    }

    /** @test */
    public function testAuthenticateWithInvalidToken()
    {
        $token = $this->createTokenString([], null, 'some-user-id', [], false);
        $token .= 'this-will-make-it-invalid';
        $this->expectException(InvalidAccessTokenException::class);
        $this->jwtAuthenticator->authenticate($token);
    }

    /** @test */
    public function testAuthenticateWithInvalidSignature()
    {
        $privateKeyResource = openssl_pkey_new();
        openssl_pkey_export($privateKeyResource, $this->privateKey);
        $token = $this->createTokenString([], null, 'some-user-id', [], false);
        $this->expectException(InvalidAccessTokenException::class);
        $this->jwtAuthenticator->authenticate($token);
    }

    /** @test */
    public function testAuthenticateWithExpiredToken()
    {
        $token = $this->createTokenString([], time() - 100, 'some-user-id', [], false);
        $this->expectException(InvalidAccessTokenException::class);
        $this->jwtAuthenticator->authenticate($token);
    }

    /** @test */
    public function testAccessTokenParsing()
    {
        $this->expectException(InvalidAccessTokenException::class);
        $this->jwtAuthenticator->authenticate('r.i.p');
    }
}
