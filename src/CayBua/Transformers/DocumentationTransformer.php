<?php

namespace CayBua\Transformers;

use CayBua\Transformers\Documentation\ApiCollectionTransformer;
use CayBua\Export\Documentation;

use PhalconRest\Transformers\Documentation\RouteTransformer;
use PhalconRest\Transformers\Transformer;

class DocumentationTransformer extends Transformer
{
    public $defaultIncludes = [
        'routes',
        'collections'
    ];

    public function transform(Documentation $documentation)
    {
        return [
            'name' => $documentation->name,
            'basePath' => $documentation->basePath
        ];
    }

    public function includeRoutes(Documentation $documentation)
    {
        return $this->collection($documentation->getRoutes(), new RouteTransformer);
    }

    public function includeCollections(Documentation $documentation)
    {
        return $this->collection($documentation->getCollections(), new ApiCollectionTransformer);
    }
}