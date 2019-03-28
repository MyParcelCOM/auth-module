<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Interfaces;

use Lcobucci\JWT\Token;

interface TokenAuthenticatorInterface
{
    /**
     * Authenticate given Authorization header and return the Token.
     *
     * @param string $token
     * @return Token
     */
    public function authenticateAuthorizationHeader(string $token): Token;
}
