<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Interfaces;

use Lcobucci\JWT\Token;

interface TokenAuthenticatorInterface
{
    /**
     * Authenticate given token and return the user associated with that token.
     *
     * @param string $token
     * @return Token
     */
    public function authenticate(string $token): Token;
}
