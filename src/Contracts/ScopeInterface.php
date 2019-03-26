<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Contracts;

use MyParcelCom\AuthModule\Enums\ScopeEnum;

interface ScopeInterface
{
    /**
     * @TODO: Remove this constant and replace it with a standardized way of
     *        adding scopes to a user/resource.
     */
    public const DEFAULT_ORGANIZATION_SCOPES = [
        ScopeEnum::SHIPMENTS_MANAGE,
        ScopeEnum::ORGANIZATIONS_MANAGE,
        ScopeEnum::EXPERIMENTAL,
    ];

    /**
     * Get the id of this scope.
     *
     * @return string
     */
    public function getId(): ?string;

    /**
     * Check if this scope has the permissions given in the permission string.
     *
     * A permission string has the format: resource_type:action[,action]|*
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool;

    /**
     * Get all the permission objects.
     *
     * @return PermissionInterface[]
     */
    public function getPermissions(): iterable;

    /**
     * Replace all the current permissions for this scope with given permissions.
     *
     * @param PermissionInterface[] ...$permissions
     * @return $this
     */
    public function setPermissions(PermissionInterface ...$permissions);

    /**
     * Add all given permissions to this scope.
     *
     * @param PermissionInterface[] ...$permissions
     * @return $this
     */
    public function addPermissions(PermissionInterface ...$permissions);
}
