<?php

namespace CayBua\Middleware;

use CayBua\Data\Query;
use Phalcon\Mvc\Micro;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use PhalconApi\Constants\Services;
use PhalconApi\Mvc\Plugin;

class UrlQueryMiddleware extends Plugin implements MiddlewareInterface
{
    public function beforeExecuteRoute()
    {
        $params = $this->getDI()->get(Services::REQUEST)->getQuery();
        $query = $this->getDI()->get(Services::URL_QUERY_PARSER)->createQuery($params);
        /** @var Query $queryService */
        $queryService  = $this->getDI()->get(Services::QUERY);
        $queryService->mergeQuery($query);
    }

    public function call(Micro $api)
    {
        return true;
    }
}