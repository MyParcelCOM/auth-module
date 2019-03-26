<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use MyParcelCom\AuthModule\Contracts\ResourceInterface;
use MyParcelCom\AuthModule\Contracts\ScopeInterface;

/**
 * Traits that an eloquent model can use when implementing the UserInterface.
 */
trait EloquentAuthUserTrait
{
    /**
     * Get the identifier of this user.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return (string)$this->attributes[$this->primaryKey];
    }

    /**
     * Get all the scopes this user has.
     *
     * @return iterable
     */
    public function getScopes(): iterable
    {
        return $this->scopes()->get();
    }

    /**
     * Get all the scopes this user has for a given resource.
     *
     * @param ResourceInterface $resource
     * @return iterable
     */
    public function getScopesForResource(ResourceInterface $resource): iterable
    {
        return $this->scopes()
            ->where('resource_type', $resource->getResourceType())
            ->where('resource_id', $resource->getResourceIdentifier())
            ->get();
    }

    /**
     * Add a scope to this user.
     *
     * @param ScopeInterface $scope
     * @return $this
     */
    public function addScope(ScopeInterface $scope)
    {
        $this->scopes()->attach($scope);

        return $this;
    }

    /**
     * Add a scope for given resource to this user.
     *
     * @note This saves the relationship to the database without you having to call `save()`
     *
     * @param ScopeInterface    $scope
     * @param ResourceInterface $resource
     * @return $this
     */
    public function addScopeForResource(ScopeInterface $scope, ResourceInterface $resource)
    {
        /** @var BelongsToMany $scopes */
        $scopes = $this->scopes();

        $foreignKey = explode('.', $scopes->getQualifiedForeignPivotKeyName())[1];
        $relatedKey = explode('.', $scopes->getQualifiedRelatedPivotKeyName())[1];

        DB::table($scopes->getTable())->insert([
            $foreignKey     => $this->getId(),
            $relatedKey     => $scope->getId(),
            'resource_type' => $resource->getResourceType(),
            'resource_id'   => $resource->getResourceIdentifier(),
        ]);

        return $this;
    }

    /**
     * Replace all the scopes for this user with given scopes.
     *
     * @param ScopeInterface[] ...$scopes
     * @return $this
     */
    public function setScopes(ScopeInterface ...$scopes)
    {
        $this->scopes()->sync(new Collection($scopes));

        return $this;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->primaryKey;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->attributes[$this->primaryKey];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->attributes['password'] ?? null;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->attributes['remember_token'] ?? null;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->attributes['remember_token'] = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}
