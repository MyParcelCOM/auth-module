<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use MyParcelCom\AuthModule\JwtRequestAuthenticator;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;

readonly class CheckIfTokenIsFresh
{
    public function __construct(
        private JwtRequestAuthenticator $requestAuthenticator,
        private ?int $tokenMaxAge = 15,
    ) {
    }

    public function handle(Request $request, Closure $next): mixed
    {
        $token = $this->requestAuthenticator->authenticate($request);
        $ageThreshold = Carbon::now()->subMinutes($this->tokenMaxAge);

        if ($token->hasBeenIssuedBefore($ageThreshold)) {
            throw new InvalidAccessTokenException(
                "The access token cannot be older than {$this->tokenMaxAge} minutes for this request.",
            );
        }

        return $next($request);
    }
}
