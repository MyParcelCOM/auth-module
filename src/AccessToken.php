<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule;

use MyParcelCom\AuthModule\Contracts\AccessTokenInterface;
use MyParcelCom\AuthModule\Model\Model;

class AccessToken extends Model implements AccessTokenInterface
{
    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isRevoked(): bool
    {
        return (bool)$this->revoked;
    }
}
