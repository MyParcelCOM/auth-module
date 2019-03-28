<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Lcobucci\JWT\Token;
use MyParcelCom\JsonApi\Exceptions\MissingScopeException;
use MyParcelCom\JsonApi\Exceptions\MissingTokenException;
use MyParcelCom\AuthModule\Interfaces\TokenAuthenticatorInterface;

class CheckForAnyScope
{
    /** @var Token */
    private $token;

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
        if(!$this->token){
            $this->token = $this->getTokenFromRequest($request);
        }

        if (!$this->token) {
            throw new MissingTokenException();
        }

        foreach ($scopes as $scope) {
            if ($this->tokenCan($this->token, $scope)) {
                return $next($request);
            }
        }

        throw new MissingScopeException($scopes);
    }

    /**
     * Check if a token contains a scope.
     *
     * @param Token  $token
     * @param string $scope
     * @return bool
     */
    public function tokenCan(Token $token, string $scope): bool
    {
        $scopeString = $token->getClaim('scope');
        $scopes = explode(' ', $scopeString);

        return in_array($scope, $scopes);
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getTokenFromRequest($request)
    {
        $authorizationHeader = $request->headers->get('Authorization');
        return App::make(TokenAuthenticatorInterface::class)->authenticateAuthorizationHeader($authorizationHeader);
    }
}
