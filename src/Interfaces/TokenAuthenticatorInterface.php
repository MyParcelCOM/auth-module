<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Interfaces;

use Lcobucci\JWT\Token;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;

interface TokenAuthenticatorInterface
{
    /**
     * Authenticate given Authorization header and return the Token.
     *
     * @param string $token
     * @return Token
     * @throws InvalidAccessTokenException
     */
    public function authenticateAuthorizationHeader(string $token): Token;
}
