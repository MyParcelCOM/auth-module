<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Lcobucci\JWT\Token;
use MyParcelCom\Scopes\Scope;

interface UserInterface extends Authenticatable
{
    /**
     * Get the identifier of this user.
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Get all the scopes this user has.
     *
     * @return Scope[]
     */
    public function getScopes(): iterable;

    /**
     * Get all the scopes this user has for a given resource.
     *
     * @param ResourceInterface $resource
     * @return Scope[]
     */
    public function getScopesForResource(ResourceInterface $resource): iterable;

    /**
     * Add a scope to this user.
     *
     * @param ScopeInterface $scope
     * @return $this
     */
    public function addScope(ScopeInterface $scope);

    /**
     * Add a scope to this user for given resource.
     *
     * @param ScopeInterface    $scope
     * @param ResourceInterface $resource
     * @return $this
     */
    public function addScopeForResource(ScopeInterface $scope, ResourceInterface $resource);

    /**
     * Replace all the scopes for this user with given scopes.
     *
     * @param ScopeInterface[] ...$scopes
     * @return $this
     */
    public function setScopes(ScopeInterface ...$scopes);

    /**
     * Check if the access token is set on the model.
     *
     * @return Token|null
     */
    public function getToken(): ?Token;

    /**
     * Check if a token contains a scope.
     *
     * @param string $scope
     * @return bool
     */
    public function tokenCan(string $scope): bool;
}
