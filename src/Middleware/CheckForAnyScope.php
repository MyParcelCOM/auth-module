<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Middleware;

use Closure;
use Illuminate\Http\Request;
use MyParcelCom\AuthModule\Interfaces\ScopeCheckerInterface;
use MyParcelCom\JsonApi\Exceptions\MissingScopeException;

class CheckForAnyScope extends ScopeChecker implements ScopeCheckerInterface
{
    /**
     * Check an incoming request for any of the passed scopes.
     */
    public function handle(Request $request, Closure $next, ...$scopes): mixed
    {
        $this->setRequest($request);

        foreach ($scopes as $scope) {
            if ($this->tokenCan($scope)) {
                return $next($request);
            }
        }

        throw new MissingScopeException($scopes);
    }
}
