<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests;

use Error;
use Illuminate\Http\Request;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use MyParcelCom\AuthModule\JwtRequestAuthenticator;
use MyParcelCom\AuthModule\Tests\Traits\AccessTokenTrait;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;
use MyParcelCom\JsonApi\Exceptions\MissingTokenException;
use PHPUnit\Framework\TestCase;

class JwtRequestAuthenticatorTest extends TestCase
{
    use AccessTokenTrait;
    use MockeryPHPUnitIntegration;

    protected JwtRequestAuthenticator $authenticator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generateKeys();

        $this->authenticator = (new JwtRequestAuthenticator())
            ->setPublicKey($this->publicKey);
    }

    /** @test */
    public function testAuthenticate(): void
    {
        $authorizationHeader = 'Bearer ' . $this->createTokenString([], null, 'some-user-id', []);
        $request = Mockery::mock(Request::class, ['header' => $authorizationHeader, 'has' => false]);

        $token = $this->authenticator->authenticate($request);
        $this->assertEquals('some-user-id', $token->claims()->get('user_id'));
    }

    /** @test */
    public function testAuthenticateWithQueryParameter(): void
    {
        $request = Mockery::mock(Request::class, [
            'has'    => true,
            'header' => null,
            'query'  => $this->createTokenString([], null, 'some-user-id', []),
        ]);

        $token = $this->authenticator->authenticate($request);
        $this->assertEquals('some-user-id', $token->claims()->get('user_id'));
    }

    /** @test */
    public function testAuthenticateWithInvalidKey(): void
    {
        $this->authenticator->setPublicKey('');
        $authorizationHeader = 'Bearer ' . $this->createTokenString([], null, 'some-user-id', []);
        $request = Mockery::mock(Request::class, ['header' => $authorizationHeader, 'has' => false]);

        $this->expectException(InvalidAccessTokenException::class);
        $this->authenticator->authenticate($request);
    }

    /** @test */
    public function testAuthenticateWithInvalidToken(): void
    {
        $authorizationHeader = 'Bearer ' . $this->createTokenString([], null, 'some-user-id', []);
        $authorizationHeader .= 'this-will-make-it-invalid';
        $request = Mockery::mock(Request::class, ['header' => $authorizationHeader, 'has' => false]);

        $this->expectException(InvalidAccessTokenException::class);
        $this->authenticator->authenticate($request);
    }

    /** @test */
    public function testAuthenticateWithInvalidSignature(): void
    {
        $privateKeyResource = openssl_pkey_new(['private_key_bits' => 2048]);
        openssl_pkey_export($privateKeyResource, $this->privateKey);
        $this->generateKeys();

        $authorizationHeader = 'Bearer ' . $this->createTokenString([], null, 'some-user-id', []);
        $request = Mockery::mock(Request::class, ['header' => $authorizationHeader, 'has' => false]);

        $this->expectException(InvalidAccessTokenException::class);
        $this->authenticator->authenticate($request);
    }

    /** @test */
    public function testAuthenticateWithExpiredToken(): void
    {
        $authorizationHeader = 'Bearer ' . $this->createTokenString([], time() - 100, 'some-user-id', []);
        $request = Mockery::mock(Request::class, ['header' => $authorizationHeader, 'has' => false]);

        $this->expectException(InvalidAccessTokenException::class);
        $this->authenticator->authenticate($request);
    }

    /** @test */
    public function testAccessTokenWithRequestWithoutAuthorizationHeader(): void
    {
        $request = Mockery::mock(Request::class, ['header' => null, 'has' => false]);

        $this->expectException(MissingTokenException::class);
        $this->authenticator->authenticate($request);
    }

    /** @test */
    public function testAccessTokenParsing(): void
    {
        $request = Mockery::mock(Request::class, ['header' => 'r.i.p', 'has' => false]);

        $this->expectException(InvalidAccessTokenException::class);
        $this->authenticator->authenticate($request);
    }

    /** @test */
    public function testGetPublicKeyExceptionWithoutKey(): void
    {
        $this->authenticator = new JwtRequestAuthenticator();

        $this->expectException(Error::class);
        $this->authenticator->getPublicKey();
    }
}
