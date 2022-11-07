<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Interfaces;

use Closure;
use Illuminate\Http\Request;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;
use MyParcelCom\JsonApi\Exceptions\MissingScopeException;
use MyParcelCom\JsonApi\Exceptions\MissingTokenException;

interface ScopeCheckerInterface
{
    /**
     * Authenticate given Authorization header and return the Token.
     *
     * @throws MissingScopeException
     * @throws InvalidAccessTokenException
     * @throws MissingTokenException
     */
    public function handle(Request $request, Closure $next, ...$scopes): mixed;
}
