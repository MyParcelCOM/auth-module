<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Contracts;

interface AccessTokenRepositoryInterface
{
    /**
     * @param string $id
     * @return AccessTokenInterface|null
     */
    public function getById(string $id): ?AccessTokenInterface;
}
