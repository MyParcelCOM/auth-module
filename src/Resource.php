<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule;

use MyParcelCom\AuthModule\Contracts\ResourceInterface;

/**
 * This class is a generic implementation of the resource interface. It can be used whenever the resource type and
 * identifier are already known, but no model is available. If creation of such a model is an expensive operation, this
 * class can be used to create a lightweight resource.
 */
class Resource implements ResourceInterface
{
    /** @var string */
    protected $resourceType;

    /** @var string */
    protected $resourceIdentifier;

    /**
     * @param string $resourceType
     * @param string $resourceIdentifier
     */
    public function __construct(string $resourceType, string $resourceIdentifier)
    {
        $this->resourceType = $resourceType;
        $this->resourceIdentifier = $resourceIdentifier;
    }

    /**
     * @return string
     */
    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    /**
     * @param string $resourceType
     * @return $this
     */
    public function setResourceType(string $resourceType): self
    {
        $this->resourceType = $resourceType;

        return $this;
    }

    /**
     * @return string
     */
    public function getResourceIdentifier(): string
    {
        return $this->resourceIdentifier;
    }

    /**
     * @param string $resourceIdentifier
     * @return $this
     */
    public function setResourceIdentifier(string $resourceIdentifier): self
    {
        $this->resourceIdentifier = $resourceIdentifier;

        return $this;
    }
}
