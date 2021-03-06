<?php
/**
 * Created by PhpStorm.
 * User: BangDinh
 * Date: 7/13/17
 * Time: 09:50
 */

namespace CayBua\Bootstrap;

use CayBua\BootstrapInterface;
use CayBua\Collections\ExportCollection;
use CayBua\Api;

use Phalcon\Config;
use Phalcon\DiInterface;

class CollectionBootstrap implements BootstrapInterface
{
    public function run(Api $api, DiInterface $di, Config $config)
    {
        $api
            ->collection(new ExportCollection('/' . $config->get('domainName') . '/export'));
    }
}