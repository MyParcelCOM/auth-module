<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Lcobucci\JWT\Token;
use MyParcelCom\AuthModule\AuthorizationService;
use MyParcelCom\AuthModule\Contracts\AccessTokenInterface;
use MyParcelCom\AuthModule\Contracts\AccessTokenRepositoryInterface;
use MyParcelCom\AuthModule\Contracts\TokenAuthenticatorInterface;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;

class AuthenticateToken
{
    /** @var TokenAuthenticatorInterface */
    private $tokenAuthenticator;

    /** @var AccessTokenRepositoryInterface */
    private $accessTokenRepository;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');
        if ($authHeader === null || strpos($authHeader, 'Bearer ') !== 0) {
            throw new InvalidAccessTokenException('No or invalid Authorization header supplied');
        }

        $tokenString = str_ireplace('Bearer ', '', $authHeader);
        $user = $this->getTokenAuthenticator()->authenticate($tokenString);
        /** @var Token $token */
        $token = $user->getToken();

        /** @var AccessTokenInterface $accessToken */
        $accessToken = $this->accessTokenRepository->getById($token->getHeader('jti'));
        if ($accessToken === null || $accessToken->isRevoked()) {
            throw new InvalidAccessTokenException('The provided token has been revoked.');
        }

        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }

    /**
     * @return TokenAuthenticatorInterface
     */
    public function getTokenAuthenticator(): TokenAuthenticatorInterface
    {
        return $this->tokenAuthenticator;
    }

    /**
     * @param TokenAuthenticatorInterface $tokenAuthenticator
     * @return AuthenticateToken
     */
    public function setTokenAuthenticator(TokenAuthenticatorInterface $tokenAuthenticator): AuthenticateToken
    {
        $this->tokenAuthenticator = $tokenAuthenticator;

        return $this;
    }

    /**
     * @param AuthorizationService $authorizationService
     * @return $this
     */
    public function setAuthorizationService(AuthorizationService $authorizationService): self
    {
        $this->authorizationService = $authorizationService;
        return $this;
    }

    /**
     * @param AccessTokenRepositoryInterface $accessTokenRepository
     * @return AuthenticateToken
     */
    public function setAccessTokenRepository(AccessTokenRepositoryInterface $accessTokenRepository): self
    {
        $this->accessTokenRepository = $accessTokenRepository;

        return $this;
    }
}
