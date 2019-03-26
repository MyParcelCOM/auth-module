<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Contracts;

interface TokenAuthenticatorInterface
{
    /**
     * Authenticate given token and return the user associated with that token.
     *
     * @param string $token
     * @return UserInterface
     */
    public function authenticate(string $token): UserInterface;
}
