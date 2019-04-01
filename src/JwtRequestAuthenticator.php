<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule;

use Exception;
use Illuminate\Http\Request;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
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
        $authorizationHeader = $request->header('Authorization');

        try {
            if (!$authorizationHeader) {
                throw new MissingTokenException();
            }

            if (strpos($authorizationHeader, 'Bearer ') !== 0) {
                throw new InvalidAccessTokenException('Invalid Authorization header supplied');
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
}
