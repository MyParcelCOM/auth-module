<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests;

use Closure;
use Illuminate\Http\Request;
use Mockery;
use MyParcelCom\AuthModule\Middleware\CheckForAnyScope;
use MyParcelCom\AuthModule\Tests\Traits\AccessTokenTrait;
use MyParcelCom\AuthModule\Tests\Traits\ScopeTrait;
use MyParcelCom\JsonApi\Exceptions\MissingScopeException;
use PHPUnit\Framework\TestCase;

class CheckForAnyScopeTest extends TestCase
{
    use AccessTokenTrait;
    use ScopeTrait;

    /** @var CheckForAnyScope */
    protected $checkForAnyScopes;

    /** @var Request */
    protected $request;

    /** @var Closure */
    protected $trueClosure;

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

    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function testHandle()
    {
        $this->checkForAnyScopes->setAuthenticator($this->createAuthenticatorReturningScopes(['test-scope']));

        $this->assertTrue($this->checkForAnyScopes->handle($this->request, $this->trueClosure, 'test-scope'));
    }

    /** @test */
    public function testHandleWithOnlyOneScopeExisting()
    {
        $this->checkForAnyScopes->setAuthenticator($this->createAuthenticatorReturningScopes(['test-scope']));

        $this->assertTrue($this->checkForAnyScopes->handle($this->request, $this->trueClosure, 'test-scope', 'test-scope2'));
    }

    /** @test */
    public function testHandleWithMissingScopeGivesMissingScopeExceptionWhenMissingOne()
    {
        $this->expectException(MissingScopeException::class);
        $this->checkForAnyScopes->setAuthenticator($this->createAuthenticatorReturningScopes([
            'test-scope2',
            'test-scope3',
        ]));

        $this->checkForAnyScopes->handle($this->request, $this->trueClosure, 'test-scope');
    }

    /** @test */
    public function testHandleWithMultipleScopes()
    {
        $this->checkForAnyScopes->setAuthenticator($this->createAuthenticatorReturningScopes([
            'test-scope',
            'test-scope2',
            'test-scope3',
        ]));

        $this->assertTrue($this->checkForAnyScopes->handle($this->request, $this->trueClosure, 'test-scope', 'test-scope2'));
    }
}
