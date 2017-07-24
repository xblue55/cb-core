<?php

namespace CayBua\Mvc\Controllers;

class CollectionController extends FractalController
{
    /** @var \CayBua\Api\ApiCollection */
    protected $_collection;

    /** @var \CayBua\Api\ApiEndpoint */
    protected $_endpoint;

    /**
     * @return \CayBua\Api\ApiCollection
     */
    public function getCollection()
    {
        if (!$this->_collection) {
            $this->_collection = $this->application->getMatchedCollection();
        }

        return $this->_collection;
    }

    /**
     * @return \CayBua\Api\ApiEndpoint
     */
    public function getEndpoint()
    {
        if (!$this->_endpoint) {
            $this->_endpoint = $this->application->getMatchedEndpoint();
        }

        return $this->_endpoint;
    }
}