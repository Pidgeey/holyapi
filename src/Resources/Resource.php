<?php

namespace Src\Resources;

use Src\Model\Model;
use Slim\Psr7\Response;

/**
 * Class Resource
 *
 * @package App\Resources
 */
class Resource implements ResourceInterface
{
    /** @var Model Model courant */
    protected $resource;

    /**
     * Resource constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->setResource($model);
    }

    /**
     * Initiate resource
     *
     * @param Model $model
     */
    private function setResource(Model $model): void
    {
        $this->resource = $model;
    }

    /**
     * Get resource
     *
     * @return Model
     */
    protected function getResource(): Model
    {
        return $this->resource;
    }

    /**
     * Cast resource into array
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->resource->getAttributes();
    }

    /**
     * @inheritDoc
     */
    public function toJsonResponse(): Response
    {
        return jsonResponse($this->toArray());
    }
}
