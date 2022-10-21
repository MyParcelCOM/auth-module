<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests\Traits;

use DateTimeImmutable;
use Illuminate\Http\Request;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;

trait AccessTokenTrait
{
    protected Configuration $config;
    protected string $privateKey = '';
    protected string $publicKey;

    /**
     * Generate RSA keys.
     */
    protected function generateKeys(bool $overrideConfig = false): void
    {
        $privateKeyResource = openssl_pkey_new(['private_key_bits' => 2048]);
        openssl_pkey_export($privateKeyResource, $this->privateKey);
        $this->publicKey = openssl_pkey_get_details($privateKeyResource)['key'];

        if ($overrideConfig) {
            config(['auth.public_key' => $this->publicKey]);
        }

        $this->config = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::plainText($this->privateKey),
            InMemory::plainText($this->publicKey)
        );
    }

    /**
     * Create a new access token.
     */
    protected function createTokenString(
        array $scopes = [],
        int $expiration = null,
        string $userId = '',
        array $claims = []
    ): string {
        $builder = $this->config->builder();
        $builder
            ->withClaim('user_id', $userId)
            ->withClaim('scope', implode(' ', $scopes));

        array_walk($claims, function ($value, $claim) use ($builder) {
            $builder->withClaim($claim, $value);
        });

        if ($expiration !== null) {
            $builder->expiresAt((new DateTimeImmutable())->setTimestamp($expiration));
        }

        return $builder->getToken($this->config->signer(), $this->config->signingKey())->toString();
    }

    /**
     * Create a token parsed to a JWT object.
     */
    protected function createParsedToken(
        array $scopes = [],
        int $expiration = null,
        string $userId = '',
        array $claims = []
    ): Token {
        $tokenString = $this->createTokenString($scopes, $expiration, $userId, $claims);

        return $this->config->parser()->parse($tokenString);
    }

    /**
     * Create a headers array with an access token in it. Optionally a headers array can be supplied and the auth header
     * will be added to this array.
     */
    protected function createAuthorizationHeaders(
        string $userId = 'some-user-id',
        array $claims = [],
        array $scopes = [],
        int $expiration = null,
        array $headers = []
    ): array {
        $headers['Authorization'] = 'Bearer ' . $this->createTokenString($scopes, $expiration, $userId, $claims);

        return $headers;
    }

    protected function createAuthorizationRequest(...$params): Request
    {
        $request = new Request();
        $request->headers->set('Authorization', 'Bearer ' . $this->createTokenString(...$params));

        return $request;
    }
}
