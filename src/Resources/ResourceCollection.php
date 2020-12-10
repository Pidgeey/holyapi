<?php

namespace Src\Resources;

use Src\Model\Model;
use Slim\Psr7\Response;

/**
 * Class ResourceCollection
 *
 * @package App\Resources
 */
class ResourceCollection implements ResourceCollectionInterface
{
    /** @var array Collection de resources */
    protected $resourceCollection = [];

    /**
     * ResourceCollection constructor.
     *
     * @param array $models
     */
    public function __construct(array $models)
    {
        foreach ($models as $model) {
            $this->setResourceCollection($model);
        }
    }

    /**
     * Add current model to resource collection
     *
     * @param Model $model
     */
    private function setResourceCollection(Model $model): void
    {
        /** @var Resource $modelResource */
        $modelResource = $model->resource;
        $this->resourceCollection[] = (new $modelResource($model))->toArray();
    }

    /**
     * Return current collection into json response
     *
     * @return Response
     */
    public function toJsonResponse(): Response
    {
        return jsonResponse($this->resourceCollection);
    }
}
