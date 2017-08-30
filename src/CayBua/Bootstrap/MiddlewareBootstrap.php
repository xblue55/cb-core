<?php

namespace CayBua\Bootstrap;

use CayBua\Api;
use CayBua\BootstrapInterface;
use CayBua\Middleware\RbacMiddleware;
use CayBua\Middleware\AuthenticationMiddleware;
use CayBua\Middleware\AuthorizationMiddleware;
use CayBua\Middleware\UrlQueryMiddleware;

use PhalconRest\Middleware\FractalMiddleware;

use PhalconApi\Middleware\CorsMiddleware;
use PhalconApi\Middleware\NotFoundMiddleware;
use PhalconApi\Middleware\OptionsResponseMiddleware;

use Phalcon\Config;
use Phalcon\DiInterface;

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