<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Contracts;

interface HasOwnerInterface extends ResourceInterface
{
    /**
     * Return the owner of this resource.
     *
     * @return ResourceInterface
     */
    public function getOwner(): ResourceInterface;
}
