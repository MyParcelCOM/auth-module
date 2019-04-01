<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests;

use Mockery;
use MyParcelCom\AuthModule\JwtAuthenticator;
use MyParcelCom\AuthModule\Tests\Traits\AccessTokenTrait;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;
use PHPUnit\Framework\TestCase;

class ScopeCheckerTest extends TestCase
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
    public function testGetToken()
    {
        $this->expectNotToPerformAssertions();
        $authorizationHeader = 'Bearer '.$this->createTokenString([], null, 'some-user-id', []);
        $this->jwtAuthenticator->authenticateAuthorizationHeader($authorizationHeader);
    }

    /** @test */
    public function testTokenCan()
    {
        $this->expectException(InvalidAccessTokenException::class);
        $this->jwtAuthenticator->authenticateAuthorizationHeader('r.i.p');
    }
}
