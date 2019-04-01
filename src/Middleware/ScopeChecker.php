<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Middleware;

use Illuminate\Http\Request;
use Lcobucci\JWT\Token;
use MyParcelCom\AuthModule\Interfaces\TokenAuthenticatorInterface;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;
use MyParcelCom\JsonApi\Exceptions\MissingTokenException;

class ScopeChecker
{
    /** @var Token */
    private $token;

    /** @var TokenAuthenticatorInterface */
    private $authenticator;

    /** @var Request */
    private $request;

    /**
     * Check if a token contains a scope.
     *
     * @param string $scope
     * @return bool
     * @throws MissingTokenException
     * @throws InvalidAccessTokenException
     */
    public function tokenCan(string $scope): bool
    {
        $scopeString = $this->getToken()->getClaim('scope');

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

        try {
            $authorizationHeader = $this->getRequest()->headers->get('Authorization');

            if (!$authorizationHeader) {
                throw new MissingTokenException();
            }

            $this->token = $this->getAuthenticator()->authenticateAuthorizationHeader($authorizationHeader);
        } catch (InvalidAccessTokenException $exception) {
            throw $exception;
        }

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
     * @param TokenAuthenticatorInterface $authenticator
     * @return $this
     */
    public function setAuthenticator(TokenAuthenticatorInterface $authenticator)
    {
        $this->authenticator = $authenticator;

        return $this;
    }

    /**
     * @return TokenAuthenticatorInterface
     */
    public function getAuthenticator(): TokenAuthenticatorInterface
    {
        return $this->authenticator;
    }
}
