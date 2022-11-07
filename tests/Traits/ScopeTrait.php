<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests\Traits;

use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\DataSet;
use Mockery;
use MyParcelCom\AuthModule\Interfaces\RequestAuthenticatorInterface;

trait ScopeTrait
{
    protected function createAuthenticatorReturningScopes(array $scopes = []): RequestAuthenticatorInterface
    {
        $token = Mockery::mock(Token::class, [
            'claims' => new DataSet([
                'scope' => implode(' ', $scopes),
            ], ''),
        ]);

        return Mockery::mock(RequestAuthenticatorInterface::class, ['authenticate' => $token]);
    }
}
