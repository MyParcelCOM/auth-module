<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Contracts;

/**
 * This interface only contains the bare minimum for the functionality required
 * by the api. The rest of the functionality is provided by the auth server.
 */
interface AccessTokenInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return bool
     */
    public function isRevoked(): bool;
}
