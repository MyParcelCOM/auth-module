<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests;

use Exception;
use Illuminate\Http\Request;
use Mockery;
use MyParcelCom\AuthModule\JwtRequestAuthenticator;
use MyParcelCom\AuthModule\Tests\Traits\AccessTokenTrait;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;
use MyParcelCom\JsonApi\Exceptions\MissingTokenException;
use PHPUnit\Framework\TestCase;

class JwtRequestAuthenticatorTest extends TestCase
{
    use AccessTokenTrait;

    /** @var JwtRequestAuthenticator */
    protected $authenticator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generateKeys();

        $this->authenticator = (new JwtRequestAuthenticator())
            ->setPublicKey($this->publicKey);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function testAuthenticate()
    {
        $this->expectNotToPerformAssertions();
        $authorizationHeader = 'Bearer ' . $this->createTokenString([], null, 'some-user-id', []);
        $request = Mockery::mock(Request::class, ['header' => $authorizationHeader]);

        $this->authenticator->authenticate($request);
    }

    /** @test */
    public function testAuthenticateWithInvalidToken()
    {
        $authorizationHeader = 'Bearer ' . $this->createTokenString([], null, 'some-user-id', []);
        $authorizationHeader .= 'this-will-make-it-invalid';
        $request = Mockery::mock(Request::class, ['header' => $authorizationHeader]);

        $this->expectException(InvalidAccessTokenException::class);
        $this->authenticator->authenticate($request);
    }

    /** @test */
    public function testAuthenticateWithInvalidSignature()
    {
        $privateKeyResource = openssl_pkey_new(['private_key_bits' => 1024]);
        openssl_pkey_export($privateKeyResource, $this->privateKey);
        $authorizationHeader = 'Bearer ' . $this->createTokenString([], null, 'some-user-id', []);
        $request = Mockery::mock(Request::class, ['header' => $authorizationHeader]);

        $this->expectException(InvalidAccessTokenException::class);
        $this->authenticator->authenticate($request);
    }

    /** @test */
    public function testAuthenticateWithExpiredToken()
    {
        $authorizationHeader = 'Bearer ' . $this->createTokenString([], time() - 100, 'some-user-id', []);
        $request = Mockery::mock(Request::class, ['header' => $authorizationHeader]);
        $this->expectException(InvalidAccessTokenException::class);
        $this->authenticator->authenticate($request);
    }

    /** @test */
    public function testAccessTokenWithRequestWithoutAuthorizationHeader()
    {
        $this->expectException(MissingTokenException::class);
        $request = Mockery::mock(Request::class, ['header' => null]);
        $this->authenticator->authenticate($request);
    }

    /** @test */
    public function testAccessTokenParsing()
    {
        $this->expectException(InvalidAccessTokenException::class);
        $request = Mockery::mock(Request::class, ['header' => 'r.i.p']);
        $this->authenticator->authenticate($request);
    }

    /** @test */
    public function testGetPublicKeyExceptionWithoutKey()
    {
        $this->authenticator = new JwtRequestAuthenticator();
        $this->expectException(Exception::class);
        $this->authenticator->getPublicKey();
    }
}
