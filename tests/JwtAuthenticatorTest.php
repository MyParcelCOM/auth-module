<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests;

use Exception;
use Mockery;
use MyParcelCom\AuthModule\JwtAuthenticator;
use MyParcelCom\AuthModule\Tests\Traits\AccessTokenTrait;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;
use PHPUnit\Framework\TestCase;

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
    public function testAuthenticateAuthorizationHeader()
    {
        $this->expectNotToPerformAssertions();
        $authorizationHeader = 'Bearer '.$this->createTokenString([], null, 'some-user-id', []);
        $this->jwtAuthenticator->authenticateAuthorizationHeader($authorizationHeader);
    }

    /** @test */
    public function testAuthenticateWithInvalidToken()
    {
        $authorizationHeader = 'Bearer '.$this->createTokenString([], null, 'some-user-id', []);
        $authorizationHeader .= 'this-will-make-it-invalid';
        $this->expectException(InvalidAccessTokenException::class);
        $this->jwtAuthenticator->authenticateAuthorizationHeader($authorizationHeader);
    }

    /** @test */
    public function testAuthenticateWithInvalidSignature()
    {
        $privateKeyResource = openssl_pkey_new(['private_key_bits' => 1024]);
        openssl_pkey_export($privateKeyResource, $this->privateKey);
        $authorizationHeader = 'Bearer '.$this->createTokenString([], null, 'some-user-id', []);
        $this->expectException(InvalidAccessTokenException::class);
        $this->jwtAuthenticator->authenticateAuthorizationHeader($authorizationHeader);
    }

    /** @test */
    public function testAuthenticateWithExpiredToken()
    {
        $authorizationHeader ='Bearer '. $this->createTokenString([], time() - 100, 'some-user-id', []);
        $this->expectException(InvalidAccessTokenException::class);
        $this->jwtAuthenticator->authenticateAuthorizationHeader($authorizationHeader);
    }

    /** @test */
    public function testAccessTokenParsing()
    {
        $this->expectException(InvalidAccessTokenException::class);
        $this->jwtAuthenticator->authenticateAuthorizationHeader('r.i.p');
    }

    /** @test */
    public function testGetPublicKeyExceptionWithoutKey()
    {
        $this->jwtAuthenticator = new JwtAuthenticator();
        $this->expectException(Exception::class);
        $this->jwtAuthenticator->getPublicKey();
    }

}
