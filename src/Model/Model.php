<?php

declare(strict_types=1);

namespace MyParcelCom\AuthModule\Model;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\App;
use MyParcelCom\AuthModule\Contracts\ResourceInterface;
use MyParcelCom\JsonApi\Exceptions\RepositoryException;
use MyParcelCom\JsonApi\Exceptions\ResourceNotFoundException;

class Model extends EloquentModel implements ResourceInterface
{
    /** @var string */
    protected $keyType = 'string';

    /** @var string */
    protected $resourceType;

    /** @var array */
    protected $guarded = [];

    /**
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * For the route model binding we use the config repository mapping to find the repository.
     * On this repository we use the getByIds() and first() functions to get the model.
     *
     * @param string $id
     * @return Model
     * @throws ResourceNotFoundException
     */
    public function resolveRouteBinding($id): Model
    {
        $class = get_class($this);
        $repositories = config('repository.mapping');

        if (!isset($repositories[$class])) {
            throw new RepositoryException('No repository for ' . $class . ' found');
        }

        try {
            $model = App::make($repositories[$class])->getById($id);
        } catch (QueryException $e) {
            throw new ResourceNotFoundException($this->getResourceType(), $e);
        }
        if (empty($model)) {
            throw new ResourceNotFoundException($this->getResourceType());
        }

        return $model;
    }

    /**
     * Get the string that identifies what the resource type is.
     *
     * @return string
     */
    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    /**
     * Get the id of the specific resource entity.
     *
     * @return string
     */
    public function getResourceIdentifier(): string
    {
        return $this->getId();
    }
}
