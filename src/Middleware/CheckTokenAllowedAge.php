<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use MyParcelCom\AuthModule\Interfaces\RequestAuthenticatorInterface;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;

readonly class CheckTokenAllowedAge
{
    public function __construct(
        private RequestAuthenticatorInterface $requestAuthenticator,
    ) {
    }

    public function handle(Request $request, Closure $next, ?int $maxAge = 15): mixed
    {
        $token = $this->requestAuthenticator->authenticate($request);
        $ageThreshold = Carbon::now()->subMinutes($maxAge);

        if ($token->hasBeenIssuedBefore($ageThreshold)) {
            throw new InvalidAccessTokenException(
                "The access token cannot be older than {$maxAge} minutes for this request. Please request a new access token to continue.",
            );
        }

        return $next($request);
    }
}
