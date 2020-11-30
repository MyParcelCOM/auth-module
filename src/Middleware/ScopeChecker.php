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
    /** @var Token */
    private $token;

    /** @var RequestAuthenticatorInterface */
    private $authenticator;

    /** @var Request */
    private $request;

    /**
     * Check if a token contains a scope.
     *
     * @param string $scope
     * @return bool
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
     * @return Token
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

    /**
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param RequestAuthenticatorInterface $authenticator
     * @return $this
     */
    public function setAuthenticator(RequestAuthenticatorInterface $authenticator)
    {
        $this->authenticator = $authenticator;

        return $this;
    }

    /**
     * @return RequestAuthenticatorInterface
     */
    public function getAuthenticator(): RequestAuthenticatorInterface
    {
        return $this->authenticator;
    }
}
