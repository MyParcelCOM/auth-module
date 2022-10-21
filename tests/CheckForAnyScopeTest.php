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

    protected CheckForAnyScope $checkForAnyScopes;
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
        $this->checkForAnyScopes = new CheckForAnyScope();
    }

    /** @test */
    public function testHandle(): void
    {
        $this->checkForAnyScopes->setAuthenticator($this->createAuthenticatorReturningScopes(['test-scope']));

        $this->assertTrue($this->checkForAnyScopes->handle($this->request, $this->trueClosure, 'test-scope'));
    }

    /** @test */
    public function testHandleWithOnlyOneScopeExisting(): void
    {
        $this->checkForAnyScopes->setAuthenticator($this->createAuthenticatorReturningScopes(['test-scope']));

        $this->assertTrue($this->checkForAnyScopes->handle($this->request, $this->trueClosure, 'test-scope', 'test-scope2'));
    }

    /** @test */
    public function testHandleWithMissingScopeGivesMissingScopeExceptionWhenMissingOne(): void
    {
        $this->expectException(MissingScopeException::class);
        $this->checkForAnyScopes->setAuthenticator($this->createAuthenticatorReturningScopes([
            'test-scope2',
            'test-scope3',
        ]));

        $this->checkForAnyScopes->handle($this->request, $this->trueClosure, 'test-scope');
    }

    /** @test */
    public function testHandleWithMultipleScopes(): void
    {
        $this->checkForAnyScopes->setAuthenticator($this->createAuthenticatorReturningScopes([
            'test-scope',
            'test-scope2',
            'test-scope3',
        ]));

        $this->assertTrue($this->checkForAnyScopes->handle($this->request, $this->trueClosure, 'test-scope', 'test-scope2'));
    }
}
