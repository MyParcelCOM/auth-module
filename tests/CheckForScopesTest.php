<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests;

use Closure;
use Illuminate\Http\Request;
use Mockery;
use MyParcelCom\AuthModule\Middleware\CheckForScopes;
use MyParcelCom\AuthModule\Tests\Traits\AccessTokenTrait;
use MyParcelCom\AuthModule\Tests\Traits\ScopeTrait;
use MyParcelCom\JsonApi\Exceptions\MissingScopeException;
use PHPUnit\Framework\TestCase;

class CheckForScopesTest extends TestCase
{
    use AccessTokenTrait;
    use ScopeTrait;

    /** @var CheckForScopes */
    protected $checkForScopes;

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
        $this->checkForScopes = new CheckForScopes();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function testHandle()
    {
        $this->checkForScopes->setAuthenticator($this->createAuthenticatorReturningScopes(['test-scope']));

        $this->assertTrue($this->checkForScopes->handle($this->request, $this->trueClosure, 'test-scope'));
    }

    /** @test */
    public function testHandleWithMissingScopesGivesMissingScopeExceptionWhenMissingOnlyOne()
    {
        $this->expectException(MissingScopeException::class);
        $this->checkForScopes->setAuthenticator($this->createAuthenticatorReturningScopes(['test-scope']));

        $this->checkForScopes->handle($this->request, $this->trueClosure, 'test-scope', 'test-scope2');
    }

    /** @test */
    public function testHandleWithMissingScopeGivesMissingScopeExceptionWhenMissingOne()
    {
        $this->expectException(MissingScopeException::class);
        $this->checkForScopes->setAuthenticator($this->createAuthenticatorReturningScopes([
            'test-scope2',
            'test-scope3',
        ]));

        $this->checkForScopes->handle($this->request, $this->trueClosure, 'test-scope');
    }

    /** @test */
    public function testHandleWithMultipleScopes()
    {
        $this->checkForScopes->setAuthenticator($this->createAuthenticatorReturningScopes([
            'test-scope',
            'test-scope2',
            'test-scope3',
        ]));

        $this->assertTrue($this->checkForScopes->handle($this->request, $this->trueClosure, 'test-scope', 'test-scope2'));
    }
}
