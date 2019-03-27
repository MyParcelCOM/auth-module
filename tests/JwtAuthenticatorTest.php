<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests;

use Lcobucci\JWT\Token;
use Mockery;
use MyParcelCom\AuthModule\JwtAuthenticator;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;
use PHPUnit\Framework\TestCase;
use MyParcelCom\AuthModule\Tests\Traits\AccessTokenTrait;

class JwtAuthenticatorTest extends TestCase
{
    use AccessTokenTrait;

    /** @var JwtAuthenticator */
    protected $jwtAuthenticator;

    protected function setUp()
    {
        parent::setUp();


        $this->generateKeys();

        $this->jwtAuthenticator = (new JwtAuthenticator())
            ->setPublicKey($this->publicKey);
    }

    protected function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function testAuthenticate()
    {
        $token = $this->createTokenString([], null, 'some-user-id', []);
        $this->assertInstanceOf(Token::class, $this->jwtAuthenticator->authenticate($token));
    }

    /** @test */
    public function testAuthenticateWithInvalidToken()
    {
        $token = $this->createTokenString([], null, 'some-user-id', []);
        $token .= 'this-will-make-it-invalid';
        $this->expectException(InvalidAccessTokenException::class);
        $this->jwtAuthenticator->authenticate($token);
    }

    /** @test */
    public function testAuthenticateWithInvalidSignature()
    {
        $privateKeyResource = openssl_pkey_new(['private_key_bits' => 1024]);
        openssl_pkey_export($privateKeyResource, $this->privateKey);
        $token = $this->createTokenString([], null, 'some-user-id', []);
        $this->expectException(InvalidAccessTokenException::class);
        $this->jwtAuthenticator->authenticate($token);
    }

    /** @test */
    public function testAuthenticateWithExpiredToken()
    {
        $token = $this->createTokenString([], time() - 100, 'some-user-id', []);
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
