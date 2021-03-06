<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Tests\Traits;

use DateTimeImmutable;
use Illuminate\Http\Request;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;

trait AccessTokenTrait
{
    /** @var string */
    protected $privateKey;

    /** @var string */
    protected $publicKey;

    /**
     * Generate RSA keys.
     *
     * @param bool $overrideConfig
     */
    protected function generateKeys(bool $overrideConfig = false)
    {
        $privateKeyResource = openssl_pkey_new(['private_key_bits' => 1024]);
        openssl_pkey_export($privateKeyResource, $this->privateKey);
        $this->publicKey = openssl_pkey_get_details($privateKeyResource)['key'];

        if ($overrideConfig) {
            config(['auth.public_key' => $this->publicKey]);
        }
    }

    /**
     * Create a new access token.
     *
     * @param array    $scopes
     * @param int|null $expiration
     * @param string   $userId
     * @param array    $claims
     * @return string
     */
    protected function createTokenString(
        array $scopes = [],
        int $expiration = null,
        string $userId = '',
        array $claims = []
    ): string {
        $builder = new Builder();
        $builder
            ->withClaim('user_id', $userId)
            ->withClaim('scope', implode(' ', $scopes));

        array_walk($claims, function ($value, $claim) use ($builder) {
            $builder->withClaim($claim, $value);
        });

        if ($expiration !== null) {
            $builder->expiresAt((new DateTimeImmutable())->setTimestamp($expiration));
        }

        return (string) $builder->getToken(new Sha256(), new Key($this->privateKey));
    }

    /**
     * Create a token parsed to a JWT object.
     *
     * @param array    $scopes
     * @param int|null $expiration
     * @param string   $userId
     * @param array    $claims
     * @return Token
     */
    protected function createParsedToken(
        array $scopes = [],
        int $expiration = null,
        string $userId = '',
        array $claims = []
    ): Token {
        $tokenString = $this->createTokenString($scopes, $expiration, $userId, $claims);

        return (new Parser())->parse($tokenString);
    }

    /**
     * Create a headers array with an access token in it. Optionally a headers array can be supplied and the auth header
     * will be added to this array.
     *
     * @param array    $scopes
     * @param int|null $expiration
     * @param string   $userId
     * @param array    $claims
     * @param array    $headers
     * @return array
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

    /**
     * @param array $params
     * @return Request
     */
    protected function createAuthorizationRequest(...$params): Request
    {
        $request = new Request();
        $request->headers->set('Authorization', 'Bearer ' . $this->createTokenString(...$params));

        return $request;
    }
}
