<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule;

use Exception;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use MyParcelCom\AuthModule\Interfaces\TokenAuthenticatorInterface;
use MyParcelCom\JsonApi\Exceptions\InvalidAccessTokenException;
use Lcobucci\JWT\Token;

/**
 * Token authenticator that authenticates JWT tokens.
 */
class JwtAuthenticator implements TokenAuthenticatorInterface
{
    /** @var string */
    private $publicKey;

    /**
     * @param string $token
     * @return Token
     * @throws Exception
     */
    public function authenticate(string $token): Token
    {
        $parser = new Parser();

        try {
            $parsedToken = $parser->parse($token);

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
     */
    public function getPublicKey(): string
    {
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
