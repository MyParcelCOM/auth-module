<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Traits;

use Illuminate\Database\Eloquent\Collection;
use MyParcelCom\AuthModule\Contracts\PermissionInterface;

/**
 * Traits that an eloquent model can use when implementing the ScopeInterface.
 */
trait EloquentAuthScopeTrait
{
    /**
     * A permission string can be given in the format: resource_type:action[,action]
     * These permissions are checked on this scope. If this scope has all permissions,
     * true is returned, false otherwise.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        $permissionSlugs = $this->explodePermissionSlug($permission);
        $permissions = $this->permissions()->whereIn('slug', $permissionSlugs)->get();

        // If the number of found permissions is smaller than the number of asked permisssions, this scope does not have
        // all the permissions.
        if (count($permissions) < count($permissionSlugs)) {
            return false;
        }

        // Check all permissions
        foreach ($permissions as $permission) {
            // If one of the permissions disallows an action, the check fails.
            if (!$permission->isAllowed()) {
                return false;
            }

            // Remove the permissions from the slugs, so at the end we know what permissions weren't checked.
            if (($key = array_search($permission->getSlug(), $permissionSlugs)) !== false) {
                unset($permissionSlugs[$key]);
            }
        }

        // If the array is empty, all permissions have been successfully checked.
        return empty($permissionSlugs);
    }

    /**
     * Rewrites permission slugs with multiple actions to an array with each
     *
     * @param string $slug
     * @return string[]
     */
    protected function explodePermissionSlug(string $slug): array
    {
        $resource = strtok($slug, PermissionInterface::SLUG_RESOURCE_DELIMITER);
        $action = strtok('');
        $actions = $action === PermissionInterface::ACTION_ALL
            ? PermissionInterface::ACTIONS
            : explode(PermissionInterface::SLUG_ACTION_DELIMITER, $action);

        return array_map(function ($action) use ($resource) {
            return $resource . PermissionInterface::SLUG_RESOURCE_DELIMITER . $action;
        }, $actions);
    }

    /**
     * Get all the permissions.
     *
     * @return iterable
     */
    public function getPermissions(): iterable
    {
        return $this->permissions()->get();
    }

    /**
     * Replace permissions with given permissions.
     *
     * @param PermissionInterface[] $permissions
     * @return $this
     */
    public function setPermissions(PermissionInterface ...$permissions)
    {
        $this->permissions()->sync(new Collection($permissions));

        return $this;
    }

    /**
     * Add given permissions to this scope.
     *
     * @param PermissionInterface[] $permissions
     * @return $this
     */
    public function addPermissions(PermissionInterface ...$permissions)
    {
        $this->permissions()->attach(new Collection($permissions));

        return $this;
    }
}
