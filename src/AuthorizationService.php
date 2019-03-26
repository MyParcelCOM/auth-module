<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule;

use Illuminate\Auth\AuthManager;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Builder;
use MyParcelCom\AuthModule\Contracts\ResourceInterface;
use MyParcelCom\AuthModule\Contracts\UserInterface;
use MyParcelCom\AuthModule\Enums\ResourceTypeEnum;
use Psr\SimpleCache\CacheInterface;

class AuthorizationService
{
    protected const ANY_RESOURCE = '';
    protected const CACHE_KEY = 'api_user_resource_permissions_';

    /** @var CacheInterface */
    private $cache;

    /** @var array */
    private $permissions = [];

    /** @var UserInterface */
    private $currentUser;

    /** @var AuthManager */
    private $authManager;

    /** @var DatabaseManager */
    private $databaseManager;

    /** @var array */
    protected $parentResourceTypes = [
        'shops'         => [
            [
                'resource_type' => 'organizations',
                'child_table'   => 'shops',
                'child_column'  => 'id',
                'parent_column' => 'organization_id',
            ],
            [
                'resource_type' => 'brokers',
                'child_table'   => 'shops',
                'child_column'  => 'id',
                'parent_column' => 'broker_id',
            ],
            [
                'resource_type' => 'enterprises',
                'child_table'   => 'shops',
                'child_column'  => 'id',
                'parent_column' => 'enterprise_id',
            ],
        ],
        'organizations' => [
            [
                'resource_type' => 'brokers',
                'child_table'   => 'organizations',
                'child_column'  => 'id',
                'parent_column' => 'broker_id',
            ],
            [
                'resource_type' => 'enterprises',
                'child_table'   => 'organizations',
                'child_column'  => 'id',
                'parent_column' => 'enterprise_id',
            ],
        ],
        'brokers'       => [
            [
                'resource_type' => 'enterprises',
                'child_table'   => 'brokers',
                'child_column'  => 'id',
                'parent_column' => 'enterprise_id',
            ],
        ],
    ];

    /**
     * @param UserInterface     $user
     * @param string            $permission
     * @param ResourceInterface $resource
     * @return bool
     */
    public function userCan(UserInterface $user, string $permission, ResourceInterface $resource): bool
    {
        $cachedValue = $this->getFromCache($user, $permission, $resource);
        if ($cachedValue !== null) {
            return $cachedValue;
        }

        $can = !empty($this->getResourceIdsWithPermission(
            new ResourceTypeEnum($resource->getResourceType()),
            $permission,
            [$resource->getResourceIdentifier()],
            $user
        ));

        $this->saveToCache($user, $permission, $resource, $can);

        return $can;
    }

    /**
     * @param string                              $permission
     * @param Shop|Organization|Broker|Enterprise $resource Resource that can have scopes associated.
     * @return bool
     */
    public function currentUserCan(string $permission, ResourceInterface $resource): bool
    {
        // No user means no permissions
        if (empty($this->getCurrentUser())) {
            return false;
        }

        return $this->userCan($this->getCurrentUser(), $permission, $resource);
    }

    /**
     * Get all ids of the given resource type that have the given permission.
     * Possibly filter them by a list of passed ids.
     *
     * @param ResourceTypeEnum   $resourceType
     * @param string             $permissionSlug
     * @param string[]           $resourceIds
     * @param UserInterface|null $user
     * @return string[] An array of resource ids.
     */
    public function getResourceIdsWithPermission(ResourceTypeEnum $resourceType, string $permissionSlug, array $resourceIds = null, UserInterface $user = null): array
    {
        $user = $user ?? $this->getCurrentUser();
        if ($user === null) {
            return [];
        }

        $query = $this->createPermissionQuery($user->getIdentifier(), $resourceType, $permissionSlug)
            ->whereIn('scopes.slug', $this->getScopeSlugs($user));

        // When no resource ids are supplied, we retrieved the currently
        // authorized ids.
        $queryResourceIds = $resourceIds === null
            ? $this->getAuthorizedResourceIds($resourceType)
            : $resourceIds;

        $authorizedIds = $query->whereIn('user_scope_resource.resource_id', $queryResourceIds)
            ->get()->pluck('resource_id')->toArray();

        // If there are no parent resource types that we can query for more ids,
        // return the currently found authorized ids.
        if (!isset($this->parentResourceTypes[$resourceType->getValue()])) {
            return $authorizedIds;
        }

        foreach ($this->parentResourceTypes[$resourceType->getValue()] as $parent) {
            $parentType = new ResourceTypeEnum($parent['resource_type']);
            $parentIds = $this->getAuthorizedResourceIds($parentType);

            if (empty($parentIds)) {
                continue;
            }

            $resourceIdColumn = $parent['child_table'] . '.' . $parent['child_column'];
            $parentIdColumn = $parent['child_table'] . '.' . $parent['parent_column'];
            $parentQuery = $this->createPermissionQuery($user->getIdentifier(), $parentType, $permissionSlug, $resourceIdColumn)
                ->join($parent['child_table'], $parentIdColumn, '=', 'user_scope_resource.resource_id')
                ->whereIn($parentIdColumn, $parentIds);

            $authorizedIds = array_merge(
                $authorizedIds,
                $parentQuery->get()->pluck('id')->toArray()
            );
        }

        // When resource ids are supplied, we only want to return a subset that
        // has the permission.
        if ($resourceIds !== null) {
            $authorizedIds = array_intersect($resourceIds, $authorizedIds);
        }

        return array_unique($authorizedIds);
    }

    /**
     * @param string           $userId
     * @param ResourceTypeEnum $resourceType
     * @param string           $permissionSlug
     * @param string           $resourceIdColumn
     * @return Builder
     */
    private function createPermissionQuery(string $userId, ResourceTypeEnum $resourceType, string $permissionSlug, string $resourceIdColumn = 'user_scope_resource.resource_id'): Builder
    {
        return $this->databaseManager
            ->table('user_scope_resource')
            ->select($resourceIdColumn)
            ->join('scopes', 'scopes.id', '=', 'user_scope_resource.scope_id')
            ->join('scope_permission', 'scope_permission.scope_id', '=', 'scopes.id')
            ->join('permissions', 'permissions.id', '=', 'scope_permission.permission_id')
            ->where('user_scope_resource.user_id', $userId)
            ->where('user_scope_resource.resource_type', $resourceType->getValue())
            ->where('permissions.slug', $permissionSlug)
            ->where('permissions.allowed', true);
    }

    /**
     * Refreshes the permission cache for given user.
     *
     * @param UserInterface $user
     * @return $this
     */
    public function refreshUserCache(UserInterface $user): self
    {
        unset($this->permissions[$user->getIdentifier()]);

        if ($this->getCache()) {
            $this->getCache()->delete(self::CACHE_KEY . $user->getIdentifier());
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function refreshCurrentUserCache(): self
    {
        return $this->refreshUserCache($this->getCurrentUser());
    }

    /**
     * Get the value for the permission from cache. Return `null` if it's not cached.
     *
     * @param UserInterface          $user
     * @param string                 $permission
     * @param ResourceInterface|null $resource
     * @return bool|null
     */
    protected function getFromCache(UserInterface $user, string $permission, ?ResourceInterface $resource): ?bool
    {
        if (!isset($this->permissions[$user->getIdentifier()])) {
            $this->permissions[$user->getIdentifier()] = $this->getCache()
                ? $this->getCache()->get(self::CACHE_KEY . $user->getIdentifier())
                : [];
        }

        return $this->permissions[$user->getIdentifier()][$permission][$resource === null ? self::ANY_RESOURCE : $resource->getResourceType() . $resource->getResourceIdentifier()]
            ?? null;
    }

    /**
     * Save the given permission value to the cache.
     *
     * @param UserInterface          $user
     * @param string                 $permission
     * @param ResourceInterface|null $resource
     * @param                        $value
     * @return $this
     */
    protected function saveToCache(UserInterface $user, string $permission, ?ResourceInterface $resource, $value): self
    {
        $resourceIdentifier = $resource === null ? self::ANY_RESOURCE : $resource->getResourceType() . $resource->getResourceIdentifier();
        $this->permissions[$user->getIdentifier()][$permission][$resourceIdentifier] = $value;

        if ($this->getCache()) {
            $this->getCache()->set(
                self::CACHE_KEY . $user->getIdentifier(),
                $this->permissions[$user->getIdentifier()],
                24 * 60 // store for a day
            );
        }

        return $this;
    }

    /**
     * @param ResourceTypeEnum $resourceType
     * @return string[]
     */
    public function getAuthorizedResourceIds(ResourceTypeEnum $resourceType): array
    {
        $token = $this->getCurrentUser()->getToken();
        if ($token === null) {
            return [];
        }

        return $token->getClaim($resourceType->getValue(), []);
    }

    /**
     * @param ResourceTypeEnum $resourceType
     * @param string           $id
     * @return bool
     */
    public function isAuthorizedResourceId(ResourceTypeEnum $resourceType, string $id): bool
    {
        return in_array($id, $this->getAuthorizedResourceIds($resourceType));
    }

    /**
     * @return string|null
     */
    public function getCurrentBrokerId(): ?string
    {
        $clientId = $this->getCurrentUser()->getToken()->getClaim('client_id', null);

        if ($clientId === null) {
            return null;
        }

        return $this->databaseManager
            ->table('clients')
            ->select('broker_id')
            ->where('id', '=', $clientId)
            ->first()
            ->broker_id;
    }

    /**
     * @param UserInterface $user
     * @return string[]
     */
    protected function getScopeSlugs(UserInterface $user): array
    {
        $token = $user->getToken();
        if ($token !== null) {
            return explode(' ', $token->getClaim('scope'));
        }

        $slugs = [];
        foreach ($user->getScopes() as $scope) {
            $slugs[] = $scope->getSlug();
        }

        return $slugs;
    }

    /**
     * @return null|string
     */
    public function getCurrentClientId(): ?string
    {
        $user = $this->getCurrentUser();

        if (!$user instanceof UserInterface) {
            return null;
        }

        $token = $user->getToken();

        if ($token === null) {
            return null;
        }

        return $token->getClaim('client_id', null);
    }

    /**
     * @return null|UserInterface
     */
    public function getCurrentUser(): ?UserInterface
    {
        if (!isset($this->currentUser) && isset($this->authManager)) {
            $this->currentUser = call_user_func($this->authManager->userResolver());
        }

        return $this->currentUser;
    }

    /**
     * @param  UserInterface $currentUser
     * @return $this
     */
    public function setCurrentUser(?UserInterface $currentUser): self
    {
        $this->currentUser = $currentUser;

        return $this;
    }

    /**
     * @return CacheInterface
     */
    protected function getCache(): ?CacheInterface
    {
        return $this->cache;
    }

    /**
     * @param CacheInterface $cache
     * @return $this
     */
    public function setCache(CacheInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @param AuthManager $authManager
     * @return $this
     */
    public function setAuthManager(AuthManager $authManager): self
    {
        $this->authManager = $authManager;

        return $this;
    }

    /**
     * @param DatabaseManager $databaseManager
     * @return $this
     */
    public function setDatabaseManager(DatabaseManager $databaseManager): self
    {
        $this->databaseManager = $databaseManager;

        return $this;
    }
}
