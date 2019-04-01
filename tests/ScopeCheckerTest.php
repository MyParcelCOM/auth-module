<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests;

use Illuminate\Http\Request;
use Lcobucci\JWT\Token;
use Mockery;
use MyParcelCom\AuthModule\Interfaces\TokenAuthenticatorInterface;
use MyParcelCom\AuthModule\Middleware\ScopeChecker;
use MyParcelCom\AuthModule\Tests\Traits\AccessTokenTrait;
use MyParcelCom\JsonApi\Exceptions\MissingTokenException;
use PHPUnit\Framework\TestCase;

class ScopeCheckerTest extends TestCase
{
    use AccessTokenTrait;

    /** @var ScopeChecker */
    protected $scopeChecker;

    protected function setUp()
    {
        parent::setUp();

        $this->generateKeys();

        $this->scopeChecker = new ScopeChecker();
    }

    protected function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function testTokenCan()
    {
        $request = $this->createAuthorizationRequest();
        $authenticator = $this->createAuthenticatorReturningScopes(['some-scope']);

        $result = $this->scopeChecker->setRequest($request)->setAuthenticator($authenticator)->tokenCan('some-scope');
        $this->assertTrue($result);
    }

    /** @test */
    public function testTokenCanNot()
    {
        $request = $this->createAuthorizationRequest();
        $authenticator = $this->createAuthenticatorReturningScopes(['some-other-scope']);

        $result = $this->scopeChecker->setRequest($request)->setAuthenticator($authenticator)->tokenCan('some-scope');
        $this->assertFalse($result);
    }

    /** @test */
    public function testTokenCanWithRequestWithoutAuthorizationHeader()
    {
        $this->expectException(MissingTokenException::class);
        $authenticator = $this->createAuthenticatorReturningScopes(['some-other-scope']);

        $this->scopeChecker->setRequest(new Request())->setAuthenticator($authenticator)->tokenCan('some-scope');
    }

    /**
     * @param array $scopes
     * @return TokenAuthenticatorInterface
     */
    protected function createAuthenticatorReturningScopes($scopes = []): TokenAuthenticatorInterface
    {
        $token = Mockery::mock(Token::class, ['getClaim' => implode(' ', $scopes)]);

        return Mockery::mock(TokenAuthenticatorInterface::class, ['authenticateAuthorizationHeader' => $token]);
    }
}
