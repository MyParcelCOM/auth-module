<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Contracts;

interface ResourceInterface
{
    /**
     * Get the string that identifies what the resource type is.
     *
     * @return string
     */
    public function getResourceType(): string;

    /**
     * Get the id of the specific resource entity.
     *
     * @return string
     */
    public function getResourceIdentifier(): string;
}
