<?php

namespace CayBua\Export\Postman;

use Phalcon\Mvc\Router\Route;
use CayBua\Api\ApiCollection as CBApiCollection;

class ApiCollection
{
    public $id;
    public $name;
    public $basePath;
    protected $requests = [];

    public function __construct($name, $basePath)
    {
        $this->id = uniqid();
        $this->name = $name;
        $this->basePath = $basePath;
    }

    public function addManyRoutes(array $routes)
    {
        /** @var Route $route */
        foreach ($routes as $route) {
            $this->addRoute($route);
        }
    }

    public function addRoute(Route $route)
    {
        if (@unserialize($route->getName())) {
            return;
        }

        $name = $route->getName() ?: $route->getPattern();

        $this->addRequest(new Request(
            $this->id,
            uniqid(),
            $name,
            null,
            $this->basePath . $route->getPattern(),
            $route->getHttpMethods(),
            'Authorization: Bearer {{authToken}}',
            null,
            "raw"
        ));
    }

    public function addRequest(Request $request)
    {
        $this->requests[] = $request;
    }

    /**
     * @param array $collections
     */
    public function addManyCollections(array $collections)
    {
        /** @var CBApiCollection $collection */
        foreach ($collections as $collection) {
            $this->addCollection($collection);
        }
    }

    /**
     * @param CBApiCollection $collection
     */
    public function addCollection(CBApiCollection $collection)
    {
        foreach ($collection->getEndpoints() as $endpoint) {

            $this->addRequest(new Request(
                $this->id,
                uniqid(),
                $collection->getPrefix() . $endpoint->getPath(),
                $endpoint->getDescription(),
                $this->basePath . $collection->getPrefix() . $endpoint->getPath(),
                $endpoint->getHttpMethod(),
                'Authorization: Bearer {{authToken}}',
                null,
                "raw"
            ));
        }
    }

    public function getRequests()
    {
        return $this->requests;
    }
}