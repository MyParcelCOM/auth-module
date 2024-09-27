<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests;

use Carbon\Carbon;
use Closure;
use Hamcrest\Core\IsEqual;
use Illuminate\Http\Request;
use Lcobucci\JWT\Token;
use Mockery;
use MyParcelCom\AuthModule\JwtRequestAuthenticator;
use MyParcelCom\AuthModule\Middleware\CheckIfTokenIsFresh;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;
use PHPUnit\Framework\TestCase;

class CheckIfTokenIsFreshTest extends TestCase
{
    public function testItThrowsAnExceptionIfInvalidToken(): void
    {
        $requestAuthenticator = Mockery::mock(JwtRequestAuthenticator::class);
        $requestAuthenticator
            ->shouldReceive('authenticate')
            ->andThrow(Mockery::mock(InvalidAccessTokenException::class));

        $middleware = new CheckIfTokenIsFresh($requestAuthenticator);
        $this->expectException(InvalidAccessTokenException::class);

        $middleware->handle(Mockery::mock(Request::class), fn () => null);
    }

    public function testItThrowsInvalidAccessTokenExceptionIfTokenIsNotFresh(): void
    {
        Carbon::setTestNow(now());

        $tokenMock = Mockery::mock(Token::class);
        $tokenMock
            ->shouldReceive('hasBeenIssuedBefore')
            ->once()
            ->with(IsEqual::equalTo(Carbon::now()->subMinutes(15)))
            ->andReturnTrue();

        $requestAuthenticator = Mockery::mock(JwtRequestAuthenticator::class, [
            'authenticate' => $tokenMock,
        ]);

        $middleware = new CheckIfTokenIsFresh($requestAuthenticator);
        $this->expectException(InvalidAccessTokenException::class);
        $middleware->handle(Mockery::mock(Request::class), fn () => null);
    }

    public function testItAllowsChangingTheMaxTokenAge(): void
    {
        Carbon::setTestNow(now());

        $tokenMock = Mockery::mock(Token::class);
        $tokenMock
            ->shouldReceive('hasBeenIssuedBefore')
            ->once()
            ->with(IsEqual::equalTo(Carbon::now()->subMinutes(30)))
            ->andReturnFalse();

        $requestAuthenticator = Mockery::mock(JwtRequestAuthenticator::class, [
            'authenticate' => $tokenMock,
        ]);

        $middleware = new CheckIfTokenIsFresh($requestAuthenticator, 30);
        $this->assertTrue(
            $middleware->handle(Mockery::mock(Request::class), fn () => true),
        );
    }
}
