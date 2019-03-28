<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests;

use Illuminate\Http\Request;
use Mockery;
use MyParcelCom\AuthModule\Middleware\CheckForScopes;
use MyParcelCom\AuthModule\Tests\Traits\AccessTokenTrait;
use PHPUnit\Framework\TestCase;

class CheckForScopesTest extends TestCase
{
    use AccessTokenTrait;

    /** @var CheckForScopes */
    protected $checkForScopes;

    /** @var Request */
    protected $request;

    protected function setUp()
    {
        parent::setUp();

        $this->generateKeys();

        $this->checkForScopes = new CheckForScopes();
        $this->request = new Request();

        $this->request->headers->set('Authorization', 'Bearer ' . $this->createTokenString());
    }

    protected function tearDown()
    {
        parent::tearDown();

        Mockery::close();
    }

    /** @test */
    public function testHandle()
    {
        $this->checkForScopes->handle($this->request, function () {

        });
    }
}
