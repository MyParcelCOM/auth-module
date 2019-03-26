<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Middleware;

use Closure;
use Illuminate\Http\Request;
use MyParcelCom\JsonApi\Exceptions\MissingScopeException;
use MyParcelCom\JsonApi\Exceptions\MissingTokenException;

class CheckForAnyScope
{
    /**
     * Check an incoming request for any of the passed scopes.
     *
     * @param Request $request
     * @param Closure $next
     * @param array   $scopes
     * @return mixed
     * @throws MissingScopeException
     * @throws MissingTokenException
     */
    public function handle(Request $request, Closure $next, ...$scopes)
    {
        if (!$request->user() || !$request->user()->getToken()) {
            throw new MissingTokenException();
        }

        foreach ($scopes as $scope) {
            if ($request->user()->tokenCan($scope)) {
                return $next($request);
            }
        }

        throw new MissingScopeException($scopes);
    }
}
