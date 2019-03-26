<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Contracts;

interface PermissionInterface
{
    public const SLUG_RESOURCE_DELIMITER = ':';
    public const SLUG_ACTION_DELIMITER = ',';

    public const ACTION_CREATE = 'create';
    public const ACTION_READ = 'read';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';
    public const ACTION_ALL = '*';
    public const ACTIONS = [
        self::ACTION_CREATE,
        self::ACTION_READ,
        self::ACTION_UPDATE,
        self::ACTION_DELETE,
    ];

    /**
     * Return true if this permission allows set action on set resource type.
     *
     * @return bool
     */
    public function isAllowed(): bool;

    /**
     * Get the slug of this permission. A slug is a string composed out of a resource type and an action in the
     * following format: resource:action
     *
     * @return string
     */
    public function getSlug(): string;

    /**
     * Return the action this permission (dis)allows.
     *
     * @return string
     */
    public function getAction(): string;

    /**
     * Return the resource type this permission (dis)allows.
     *
     * @return string
     */
    public function getResourceType(): string;
}
