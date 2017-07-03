<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 6/26/17
 * Time: 15:35
 */

namespace CayBua\Resources;

use PhalconRest\Api\ApiEndpoint;
use PhalconRest\Api\ApiResource as Resource;

abstract class ApiResource extends Resource
{

    /**
     * @param ApiEndpoint $endpoint
     * @return mixed
     */
    public function endpoint(ApiEndpoint $endpoint)
    {
        return parent::endpoint($endpoint);
    }

    /**
     * @param string $collectionKey
     * @return mixed
     */
    public function collectionKey($collectionKey)
    {
        return parent::collectionKey($collectionKey);
    }

    /**
     * @param $handler
     * @param bool $lazy
     * @return mixed
     */
    public function handler($handler, $lazy = true)
    {
        return parent::handler($handler, $lazy);
    }
}