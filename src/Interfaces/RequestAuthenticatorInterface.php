<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Interfaces;

use Illuminate\Http\Request;
use Lcobucci\JWT\Token;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;
use MyParcelCom\JsonApi\Exceptions\MissingTokenException;

interface RequestAuthenticatorInterface
{
    /**
     * Authenticate given Authorization header and return the Token.
     *
     * @param Request $request
     * @return Token
     * @throws InvalidAccessTokenException
     * @throws MissingTokenException
     */
    public function authenticate(Request $request): Token;
}
