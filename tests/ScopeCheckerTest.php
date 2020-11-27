<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests;

use Mockery;
use MyParcelCom\AuthModule\Middleware\ScopeChecker;
use MyParcelCom\AuthModule\Tests\Traits\AccessTokenTrait;
use MyParcelCom\AuthModule\Tests\Traits\ScopeTrait;
use PHPUnit\Framework\TestCase;

class ScopeCheckerTest extends TestCase
{
    use AccessTokenTrait;
    use ScopeTrait;

    /** @var ScopeChecker */
    protected $scopeChecker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generateKeys();

        $this->scopeChecker = new ScopeChecker();
    }

    protected function tearDown(): void
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
}
