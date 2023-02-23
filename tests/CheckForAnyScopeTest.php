<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests;

use Closure;
use Illuminate\Http\Request;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use MyParcelCom\AuthModule\Middleware\CheckForAnyScope;
use MyParcelCom\AuthModule\Tests\Traits\AccessTokenTrait;
use MyParcelCom\AuthModule\Tests\Traits\ScopeTrait;
use MyParcelCom\JsonApi\Exceptions\MissingScopeException;
use PHPUnit\Framework\TestCase;

class CheckForAnyScopeTest extends TestCase
{
    use AccessTokenTrait;
    use MockeryPHPUnitIntegration;
    use ScopeTrait;

    protected CheckForAnyScope $scopeChecker;
    protected Request $request;
    protected Closure $trueClosure;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generateKeys();
        $this->trueClosure = function () {
            return true;
        };
        $this->request = $this->createAuthorizationRequest();
        $this->scopeChecker = new CheckForAnyScope();
    }

    /** @test */
    public function testHandle(): void
    {
        $this->scopeChecker->setAuthenticator($this->createAuthenticatorReturningScopes(['test-scope']));

        $this->assertTrue($this->scopeChecker->handle($this->request, $this->trueClosure, 'test-scope'));
    }

    /** @test */
    public function testHandleWithOnlyOneScopeExisting(): void
    {
        $this->scopeChecker->setAuthenticator($this->createAuthenticatorReturningScopes(['test-scope']));

        $this->assertTrue($this->scopeChecker->handle($this->request, $this->trueClosure, 'test-scope', 'test-scope2'));
    }

    /** @test */
    public function testHandleWithMissingScopeGivesMissingScopeExceptionWhenMissingOne(): void
    {
        $this->expectException(MissingScopeException::class);
        $this->scopeChecker->setAuthenticator($this->createAuthenticatorReturningScopes([
            'test-scope2',
            'test-scope3',
        ]));

        $this->scopeChecker->handle($this->request, $this->trueClosure, 'test-scope');
    }

    /** @test */
    public function testHandleWithMultipleScopes(): void
    {
        $this->scopeChecker->setAuthenticator($this->createAuthenticatorReturningScopes([
            'test-scope',
            'test-scope2',
            'test-scope3',
        ]));

        $this->assertTrue($this->scopeChecker->handle($this->request, $this->trueClosure, 'test-scope', 'test-scope2'));
    }
}
