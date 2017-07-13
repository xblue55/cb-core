<?php

namespace CayBua\Transformers\Documentation;

use CayBua\Export\Documentation\ApiEndpoint as DocumentationEndpoint;
use PhalconRest\Transformers\Transformer;

class ApiEndpointTransformer extends Transformer
{
    public function transform(DocumentationEndpoint $endpoint)
    {
        return [
            'name' => $endpoint->getName(),
            'description' => $endpoint->getDescription(),
            'httpMethod' => $endpoint->getHttpMethod(),
            'path' => $endpoint->getPath(),
            'exampleParameters' => $endpoint->getExampleParameters(),
            'exampleResponse' => $endpoint->getExampleResponse(),
            'allowedRoles' => $endpoint->getAllowedRoles()
        ];
    }
}
