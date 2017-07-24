<?php

namespace CayBua\Bootstrap;

use CayBua\Api;
use CayBua\BootstrapInterface;

use PhalconRest\Middleware\FractalMiddleware;

use PhalconApi\Middleware\CorsMiddleware;
use PhalconApi\Middleware\NotFoundMiddleware;
use PhalconApi\Middleware\OptionsResponseMiddleware;
use PhalconApi\Middleware\UrlQueryMiddleware;

use Phalcon\Config;
use Phalcon\DiInterface;

class PrivateMiddlewareBootstrap implements BootstrapInterface
{
    public function run(Api $api, DiInterface $di, Config $config)
    {
        $api
            ->attach(new CorsMiddleware($config->get('cors')->allowedOrigins->toArray()))
            ->attach(new OptionsResponseMiddleware)
            ->attach(new NotFoundMiddleware)
            ->attach(new FractalMiddleware)
            ->attach(new UrlQueryMiddleware);
    }
}