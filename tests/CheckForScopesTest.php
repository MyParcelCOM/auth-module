<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests;

use Closure;
use Illuminate\Http\Request;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use MyParcelCom\AuthModule\Middleware\CheckForScopes;
use MyParcelCom\AuthModule\Tests\Traits\AccessTokenTrait;
use MyParcelCom\AuthModule\Tests\Traits\ScopeTrait;
use MyParcelCom\JsonApi\Exceptions\MissingScopeException;
use PHPUnit\Framework\TestCase;

class CheckForScopesTest extends TestCase
{
    use AccessTokenTrait;
    use MockeryPHPUnitIntegration;
    use ScopeTrait;

    protected CheckForScopes $scopeChecker;
    protected Request $request;
    protected Closure $trueClosure;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generateKeys();
        $this->trueClosure = fn () => true;
        $this->request = $this->createAuthorizationRequest();
        $this->scopeChecker = new CheckForScopes();
    }

    public function testHandle(): void
    {
        $this->scopeChecker->setAuthenticator($this->createAuthenticatorReturningScopes(['test-scope']));

        $this->assertTrue($this->scopeChecker->handle($this->request, $this->trueClosure, 'test-scope'));
    }

    public function testHandleWithMissingScopesGivesMissingScopeExceptionWhenMissingOnlyOne(): void
    {
        $this->expectException(MissingScopeException::class);
        $this->scopeChecker->setAuthenticator($this->createAuthenticatorReturningScopes(['test-scope']));

        $this->scopeChecker->handle($this->request, $this->trueClosure, 'test-scope', 'test-scope2');
    }

    public function testHandleWithMissingScopeGivesMissingScopeExceptionWhenMissingOne(): void
    {
        $this->expectException(MissingScopeException::class);
        $this->scopeChecker->setAuthenticator($this->createAuthenticatorReturningScopes([
            'test-scope2',
            'test-scope3',
        ]));

        $this->scopeChecker->handle($this->request, $this->trueClosure, 'test-scope');
    }

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
