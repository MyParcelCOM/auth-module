<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule;

use MyParcelCom\AuthModule\Contracts\AccessTokenInterface;
use MyParcelCom\AuthModule\Contracts\AccessTokenRepositoryInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    /**
     * @param string $id
     * @return AccessTokenInterface|null
     */
    public function getById(string $id): ?AccessTokenInterface
    {
        return AccessToken::query()
            ->where('id', '=', $id)
            ->first();
    }
}
