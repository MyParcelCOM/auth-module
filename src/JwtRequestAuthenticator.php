<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule;

use DateTimeImmutable;
use Exception;
use Illuminate\Http\Request;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Validator;
use MyParcelCom\AuthModule\Interfaces\RequestAuthenticatorInterface;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;
use MyParcelCom\JsonApi\Exceptions\MissingTokenException;

/**
 * Token authenticator that Request contains a valid JWT tokens.
 */
class JwtRequestAuthenticator implements RequestAuthenticatorInterface
{
    /** @var string */
    private $publicKey;

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): Token
    {
        try {
            $parsedToken = (new Parser())->parse(
                $this->getTokenString($request)
            );

            $constraint = new SignedWith(new Sha256(), new Key($this->getPublicKey()));
            $valid = (new Validator())->validate($parsedToken, $constraint);

            if (!$valid) {
                throw new InvalidAccessTokenException('Token could not be verified');
            }

            if ($parsedToken->isExpired(new DateTimeImmutable())) {
                throw new InvalidAccessTokenException('Token expired');
            }

            return $parsedToken;
        } catch (InvalidAccessTokenException $exception) {
            // Rethrow the exception so it is caught by the exception handler instead of this try catch block.
            throw $exception;
        } catch (MissingTokenException $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw new InvalidAccessTokenException('Token could not be parsed', $exception);
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getPublicKey(): string
    {
        if (!$this->publicKey) {
            throw new Exception('Public Key not provided for JwtRequestAuthenticator');
        }

        return $this->publicKey;
    }

    /**
     * @param string $publicKey
     * @return $this
     */
    public function setPublicKey(string $publicKey): self
    {
        $this->publicKey = $publicKey;

        return $this;
    }

    /**
     * Get the token string
     *
     * Either extract it from Authorization: Bearer header or from access_token query parameter
     *
     * @param Request $request
     * @return string
     * @throws InvalidAccessTokenException
     * @throws MissingTokenException
     */
    private function getTokenString(Request $request): string
    {
        $authorizationHeader = $request->header('Authorization');

        if (!$authorizationHeader) {
            if (!$request->has('access_token')) {
                throw new MissingTokenException();
            }

            return $request->query('access_token');
        }

        if (strpos($authorizationHeader, 'Bearer ') !== 0) {
            throw new InvalidAccessTokenException('Invalid Authorization header supplied');
        }

        return str_ireplace('Bearer ', '', $authorizationHeader);
    }
}
