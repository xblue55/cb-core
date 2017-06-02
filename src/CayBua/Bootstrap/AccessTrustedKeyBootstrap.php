<?php

namespace CayBua\Bootstrap;

use CayBua\BootstrapInterface;
use Phalcon\Config;
use Phalcon\DiInterface;
use PhalconRest\Api;
use CayBua\Mvc\BaseModel as Base;

class AccessTrustedKeyBootstrap implements BootstrapInterface
{
    public function run(Api $api, DiInterface $di, Config $config)
    {
        Base::$accessTrustedKey = $config->get('authentication')->accesstrustedkey;
    }
}