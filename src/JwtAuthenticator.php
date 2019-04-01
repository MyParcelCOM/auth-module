<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule;

use Exception;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use MyParcelCom\AuthModule\Interfaces\TokenAuthenticatorInterface;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;

/**
 * Token authenticator that authenticates JWT tokens.
 */
class JwtAuthenticator implements TokenAuthenticatorInterface
{
    /** @var string */
    private $publicKey;

    /**
     * @param string $authorizationHeader
     * @return null|Token
     * @throws InvalidAccessTokenException
     */
    public function authenticateAuthorizationHeader(?string $authorizationHeader): Token
    {
        try {
            if ($authorizationHeader === null || strpos($authorizationHeader, 'Bearer ') !== 0) {
                throw new InvalidAccessTokenException('No or invalid Authorization header supplied');
            }

            $tokenString = str_ireplace('Bearer ', '', $authorizationHeader);
            $parser = new Parser();

            $parsedToken = $parser->parse($tokenString);

            $signer = new Sha256();
            $publicKey = new Key($this->getPublicKey());

            if (!$parsedToken->verify($signer, $publicKey)) {
                throw new InvalidAccessTokenException('Token could not be verified');
            }

            if ($parsedToken->isExpired()) {
                throw new InvalidAccessTokenException('Token expired');
            }

            return $parsedToken;
        } catch (InvalidAccessTokenException $exception) {
            // Rethrow the exception so it is caught by the exception handler
            // instead of this try catch block.
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
            throw new Exception('Public Key not provided for JwtAuthenticator');
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
}
