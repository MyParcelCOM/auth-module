<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use MyParcelCom\AuthModule\Middleware\ScopeChecker;
use MyParcelCom\AuthModule\Tests\Traits\AccessTokenTrait;
use MyParcelCom\AuthModule\Tests\Traits\ScopeTrait;
use PHPUnit\Framework\TestCase;

class ScopeCheckerTest extends TestCase
{
    use AccessTokenTrait;
    use MockeryPHPUnitIntegration;
    use ScopeTrait;

    protected ScopeChecker $scopeChecker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generateKeys();

        $this->scopeChecker = new ScopeChecker();
    }

    public function testTokenCan(): void
    {
        $request = $this->createAuthorizationRequest();
        $authenticator = $this->createAuthenticatorReturningScopes(['some-scope']);

        $result = $this->scopeChecker->setRequest($request)->setAuthenticator($authenticator)->tokenCan('some-scope');
        $this->assertTrue($result);
    }

    public function testTokenCanNot(): void
    {
        $request = $this->createAuthorizationRequest();
        $authenticator = $this->createAuthenticatorReturningScopes(['some-other-scope']);

        $result = $this->scopeChecker->setRequest($request)->setAuthenticator($authenticator)->tokenCan('some-scope');
        $this->assertFalse($result);
    }
}
