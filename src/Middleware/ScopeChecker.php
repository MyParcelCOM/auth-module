<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Middleware;

use Illuminate\Http\Request;
use Lcobucci\JWT\Token;
use MyParcelCom\AuthModule\Interfaces\RequestAuthenticatorInterface;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;
use MyParcelCom\JsonApi\Exceptions\MissingTokenException;

class ScopeChecker
{
    private RequestAuthenticatorInterface $authenticator;
    private Request $request;
    private ?Token $token = null;

    /**
     * Check if a token contains a scope.
     *
     * @throws InvalidAccessTokenException
     * @throws MissingTokenException
     */
    public function tokenCan(string $scope): bool
    {
        $scopeString = $this->getToken()->claims()->get('scope');

        $scopes = explode(' ', $scopeString);

        return in_array($scope, $scopes);
    }

    /**
     * @throws InvalidAccessTokenException
     * @throws MissingTokenException
     */
    private function getToken(): Token
    {
        if ($this->token) {
            return $this->token;
        }

        $this->token = $this->getAuthenticator()->authenticate($this->getRequest());

        return $this->token;
    }

    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setAuthenticator(RequestAuthenticatorInterface $authenticator): self
    {
        $this->authenticator = $authenticator;

        return $this;
    }

    public function getAuthenticator(): RequestAuthenticatorInterface
    {
        return $this->authenticator;
    }
}
