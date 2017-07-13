<?php

namespace CayBua\Bootstrap;

use CayBua\BootstrapInterface;
use CayBua\Middleware\RbacMiddleware;
use CayBua\Middleware\AuthenticationMiddleware;
use CayBua\Middleware\AuthorizationMiddleware;
use CayBua\Api;

use Phalcon\Config;
use Phalcon\DiInterface;
use PhalconApi\Middleware\CorsMiddleware;
use PhalconRest\Middleware\FractalMiddleware;
use PhalconApi\Middleware\NotFoundMiddleware;
use PhalconApi\Middleware\OptionsResponseMiddleware;
use PhalconApi\Middleware\UrlQueryMiddleware;

class MiddlewareBootstrap implements BootstrapInterface
{
    public function run(Api $api, DiInterface $di, Config $config)
    {
        $api
            ->attach(new CorsMiddleware($config->get('cors')->allowedOrigins->toArray()))
            ->attach(new OptionsResponseMiddleware)
            ->attach(new NotFoundMiddleware)
            ->attach(new AuthenticationMiddleware)
            ->attach(new AuthorizationMiddleware)
            ->attach(new RbacMiddleware)
            ->attach(new FractalMiddleware)
            ->attach(new UrlQueryMiddleware);
    }
}